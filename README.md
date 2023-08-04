# Voucher-generator

### Setup & Installation

1. Clone the repository: <br>
git clone https://github.com/dmilazar/voucher-generator.git <br>
cd voucher-generator <br>

2. Install dependencies: <br>
composer install <br>

3. Configure environment variables: <br>
- Create a copy of the `.env` file: <br>
  ```
  cp .env.dist .env
  ```
- Open the `.env` file and update it with your specific configuration details. <br>


4. Create the database and run migrations: <br>
php bin/console doctrine:database:create <br>
php bin/console doctrine:migrations:migrate

5. Start the local web server: <br>
symfony server:start <br>

6. **Note**: There is an `EY.sql` database file located at the root of the project. <br>
