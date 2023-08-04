<?php

namespace App\Controller;

use App\Entity\Token;
use App\Repository\VoucherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;



class VoucherController extends AbstractController
{
    private EntityManagerInterface $em;
    private VoucherRepository $voucherRepository;

    public function __construct(EntityManagerInterface $entityManager, VoucherRepository $voucherRepository)
    {
        $this->em = $entityManager;
        $this->voucherRepository = $voucherRepository;
    }


    #[Route('/auth', name: 'app_auth', methods: ['POST'])]
    public function auth(Request $request): JsonResponse
    {
        $appSecret = $this->getParameter('app_secret');

        $content = $request->getContent();
        $data = json_decode($content, true);

        if (isset($data['secret']) && $data['secret'] === $appSecret ) {
                $token = Uuid::v4()->jsonSerialize();

                $newToken = new Token();
                $newToken->setValue($token);
                $newToken->setIsUsed(false);

                $this->em->persist($newToken);
                $this->em->flush();

                return $this->json([
                    'token' => $token,
                ]);
            }

        return $this->json([
            'error' => 'Unauthorized',
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }


    #[Route('/voucher/get', name: 'app_voucher_get', methods: ['GET'])]
    public function getVoucher(Request $request): JsonResponse
    {
        $token = $request->headers->get('Authorization');
        $token = str_replace("Bearer ", "", $token);

        $tokenValid = $this->validateToken($token);

        if ($tokenValid) {
            $content = $request->getContent();
            $data = json_decode($content, true);

            if (isset($data['voucher_provider']) && isset($data['voucher_amount'])) {
                $provider = $data['voucher_provider'];
                $amount = floatval($data['voucher_amount']);

                if (($provider === "foo" || $provider === "bar") && ($amount == 10 || $amount == 20)) {
                   $voucher = $this->voucherRepository->findValidVoucher($provider, $amount);

                   if (!empty($voucher)) {
                       $voucher->setIsUsed(true);

                       $this->em->persist($voucher);
                       $this->em->flush();

                       $expiresAt = date('Y-m-d\TH:i:s', $voucher->getExpiresAt()->getTimestamp());

                       return $this->json([
                           'voucher_number' => $voucher->getNumber(),
                           'expires_at' => $expiresAt
                       ]);
                   }

                    return $this->json([
                        'error' => 'Voucher not available',
                    ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
                }
            }
        }

        return $this->json([
            'error' => 'Unauthorized',
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }


    private function validateToken($token): bool
    {
        $existingToken = $this->em->getRepository(Token::class)->findOneBy(['value' => $token]);

        if (!empty($existingToken) && $existingToken->isIsUsed() === false) {
            $existingToken->setIsUsed(true);
            $this->em->flush();

            return true;
        }

        return false;
    }
}
