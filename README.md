# Voucher-generator

### Setup & Installation

1. Clone the repository:
git clone https://github.com/dmilazar/voucher-generator.git
cd voucher-generator

2. Install dependencies:
composer install

3. Configure environment variables:
- Create a copy of the `.env` file:
  ```
  cp .env.dist .env
  ```
- Open the `.env` file and update it with your specific configuration details.


4. Create the database and run migrations:
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

5. Start the local web server:
symfony server:start

6. **Note**: There is an `EY.sql` database file located at the root of the project.
