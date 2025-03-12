# M@ieutIC

## Introduction
La plateforme facilitant la discussion entre doctorants !

## Requirements
Ensure you have the following dependencies installed:
- PHP 8.1 or higher
- Composer
- Symfony CLI (optional but recommended)
- MySQL

## Installation
1. **Clone the repository**:
   ```sh
   git clone https://github.com/victorSejournes22005043/MaieutIC.git
   cd your-symfony-project
   ```

2. **Install PHP dependencies**:
   ```sh
   composer install
   ```

3. **Install JS dependencies**:
   ```sh
   npm install
   ```

4. **Set up environment variables**:
   ```sh
   cp .env.example .env
   ```
   Update the `.env` file with your database credentials.

5. **Database setup**:
   Create the database and run migrations:
   ```sh
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

6. **Compile Assets with Webpack Encore**:
   Development Mode
   ```bash
   npm run dev
   ```
   
   Production Mode
   ```bash
   npm run build
   ```

7. **Start the Symfony Server**:
   ```sh
   symfony server:start
   ```
