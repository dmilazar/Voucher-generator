<?php

namespace App\Repository;

use App\Entity\Voucher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Voucher>
 *
 * @method Voucher|null find($id, $lockMode = null, $lockVersion = null)
 * @method Voucher|null findOneBy(array $criteria, array $orderBy = null)
 * @method Voucher[]    findAll()
 * @method Voucher[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoucherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voucher::class);
    }


    public function findValidVoucher($provider, $amount): ?Voucher
    {
        return $this->createQueryBuilder('v')
            ->where('v.provider = :provider')
            ->andWhere('v.amount = :amount')
            ->andWhere('v.is_used = :is_used')
            ->andWhere('v.expires_at > :currentDate')
            ->orderBy('v.expires_at', 'ASC')
            ->setMaxResults(1)
            ->setParameters([
                'provider' => $provider,
                'amount' => $amount,
                'is_used' => false,
                'currentDate' => new \DateTime(),
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
