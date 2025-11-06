# STON.fi Pools Dashboard

A web dashboard for monitoring liquidity pools on the STON.fi DEX, built on the TON blockchain.

This application fetches data from the STON.fi API, stores historical snapshots, and provides a user interface to view real-time and historical data for liquidity pools, including TVL, trading volumes, and APY.

## ✨ Features

- 📊 Real-time monitoring of liquidity pools
- 📈 Historical charts for TVL, volume, and APY
- 🔄 Automatic data synchronization every 5 minutes via Cron
- 💾 Historical data stored in a MySQL database
- 🐳 Fully containerized with Docker for easy setup and deployment

## 🛠️ Tech Stack

- **Backend**: PHP 8.2 with the Yii2 Framework
- **Database**: MySQL 8.0
- **Web Server**: Nginx
- **Containerization**: Docker & Docker Compose
- **Scheduler**: Cron

## 🚀 Quick Start

Follow these steps to set up and run the project locally.

### 1. Prerequisites

- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)
- Git

### 2. Installation

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/CasperOnrun/Stonfi-pools.git
    cd Stonfi-pools
    ```

2.  **Create a data directory for MySQL:**
    ```bash
    mkdir -p docker/mysql/data
    ```

3.  **Set up environment variables:**
    There is no `.env` file in the project. The configuration is handled directly in the `config/` files (`db.php`, `params.php`). For a production setup, you may want to implement a library like `vlucas/phpdotenv`.

4.  **Build and start the Docker containers:**
    ```bash
    docker-compose up -d --build
    ```

### 3. Application Setup

1.  **Enter the PHP container:**
    ```bash
    docker-compose exec php bash
    ```

2.  **Inside the container, run the following commands:**
    ```bash
    # Install Composer dependencies
    composer install

    # Apply database migrations
    ./yii migrate --interactive=0

    # Perform the initial data synchronization
    ./yii pools/sync
    ```

3.  **Exit the container:**
    ```bash
    exit
    ```

### 4. Access the Application

You can now access the dashboard in your web browser at:
**[http://localhost:8080](http://localhost:8080)**

## ⚙️ Project Structure

```
├── commands/           # Console commands (e.g., for cron jobs)
├── config/             # Application configuration
├── controllers/        # Web request handlers
├── docker/             # Docker configuration (Nginx, PHP, Cron)
├── migrations/         # Database migrations
├── models/             # ActiveRecord models
├── services/           # Business logic (e.g., API services)
├── views/              # View templates
└── web/                # Public web root
```

## 🕹️ Usage

### Web Interface

-   `GET /`: Main dashboard with a list of all liquidity pools.
-   `GET /pool/{address}`: Detailed view of a specific pool.
-   `GET /pool/{address}/history?period=24h`: JSON endpoint to fetch historical data for a pool.

### Console Commands

These commands can be run inside the `php` container (`docker-compose exec php bash`).

-   **Sync pool data:**
    ```bash
    ./yii pools/sync
    ```

-   **Clean up old snapshots (older than 30 days by default):**
    ```bash
    ./yii pools/cleanup
    ```

-   **Clean up with a custom retention period (e.g., 60 days):**
    ```bash
    ./yii pools/cleanup 60
    ```

## 🐳 Docker Environment

The `docker-compose.yml` file defines the following services:

-   `nginx`: The web server, accessible on port `8080`.
-   `php`: The PHP-FPM service that executes the application code.
-   `mysql`: The MySQL database, accessible on port `3307`. Data is persisted in `docker/mysql/data`.
-   `cron`: A container that runs the `pools/sync` command every 5 minutes.

### Managing Containers

-   **Stop containers:**
    ```bash
    docker-compose down
    ```

-   **View logs:**
    ```bash
    # View all logs
    docker-compose logs -f

    # View logs for a specific service
    docker-compose logs -f php
    ```

-   **Clean up (removes containers, networks, and volumes):**
    ```bash
    docker-compose down -v
    ```

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a pull request or open an issue if you find a bug or have a feature request.

1.  Fork the repository.
2.  Create a new branch (`git checkout -b feature/your-feature-name`).
3.  Commit your changes (`git commit -m 'Add some feature'`).
4.  Push to the branch (`git push origin feature/your-feature-name`).
5.  Open a pull request.

## 📄 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

