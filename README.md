# Laravel 12 Project

This is a Laravel 12 project. Follow the steps below to set up and run the project locally.

## Prerequisites

- PHP >= 8.3
- Composer
- Node.js and npm
- posgres or any other supported database

## Installation

1. Clone the repository:

    ```bash
    git clone <repository-url>
    cd prueba_1
    ```

2. Install PHP dependencies:

    ```bash
    composer install
    ```

3. Install JavaScript dependencies:

    ```bash
    npm install
    ```

4. Copy the `.env.example` file to `.env`:

    ```bash
    cp .env.example .env
    ```

5. Generate the application key:

    ```bash
    php artisan key:generate
    ```

6. Configure your database in the `.env` file:

    ```
     DB_CONNECTION=pgsql
     DB_HOST=postgres
     DB_PORT=5432
     DB_DATABASE=DATABASE
     DB_USERNAME=USER
     DB_PASSWORD=PASSWORD
    ```

7. Run database migrations:

    ```bash
    php artisan migrate
    ```

8. Build frontend assets:

    ```bash
    npm run dev
    ```

9. Start the development server:
    ```bash
    php artisan serve
    ```

## Contributing

We welcome contributions! Please follow the standard GitHub workflow for submitting pull requests.

### Contributors

- [Junior](https://github.com/junior) - Project Maintainer

## License

This project is licensed under the [Creative Commons Attribution-NonCommercial 4.0 International (CC BY-NC 4.0)](LICENSE).
Commercial use is not allowed.
