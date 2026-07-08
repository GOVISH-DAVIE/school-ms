# Ekattor 8 - Docker Setup

This document provides instructions for running the Ekattor 8 School Management System using Docker.

## Prerequisites

- Docker
- Docker Compose
- At least 4GB of available RAM
- At least 10GB of available disk space

## Quick Start

1. **Clone or download the project** to your local machine

2. **Navigate to the project directory**:
   ```bash
   cd codecanyon-39611172-ekattor-8-school-management-system/Ekattor8
   ```

3. **Create environment file** (if not exists):
   ```bash
   cp .env.example .env
   ```

4. **Update the .env file** with the following database settings:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=db
   DB_PORT=3306
   DB_DATABASE=ekattor8
   DB_USERNAME=ekattor8_user
   DB_PASSWORD=ekattor8_password
   
   REDIS_HOST=redis
   REDIS_PORT=6379
   
   APP_ENV=production
   APP_DEBUG=false
   ```

5. **Build and start the containers**:
   ```bash
   docker-compose up -d --build
   ```

6. **Wait for the containers to be ready** (this may take a few minutes on first run):
   ```bash
   docker-compose logs -f app
   ```

7. **Access the application**:
   - Main application: http://localhost
   - Direct app container: http://localhost:8000

## Services

The Docker setup includes the following services:

- **app**: Laravel application with PHP 8.1 and Apache
- **db**: MySQL 8.0 database
- **redis**: Redis 7 cache server
- **nginx**: Nginx reverse proxy (optional)

## Default Credentials

### Database
- **Host**: localhost (or `db` from within containers)
- **Port**: 3306
- **Database**: ekattor8
- **Username**: ekattor8_user
- **Password**: ekattor8_password
- **Root Password**: root_password

### Redis
- **Host**: localhost (or `redis` from within containers)
- **Port**: 6379

## Useful Commands

### View logs
```bash
# All services
docker-compose logs

# Specific service
docker-compose logs app
docker-compose logs db
docker-compose logs redis

# Follow logs in real-time
docker-compose logs -f app
```

### Access containers
```bash
# Access Laravel app container
docker-compose exec app bash

# Access database container
docker-compose exec db mysql -u ekattor8_user -p ekattor8

# Access Redis container
docker-compose exec redis redis-cli
```

### Run Laravel commands
```bash
# Run migrations
docker-compose exec app php artisan migrate

# Clear cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# Generate application key
docker-compose exec app php artisan key:generate

# Run seeders
docker-compose exec app php artisan db:seed
```

### Stop and remove containers
```bash
# Stop containers
docker-compose down

# Stop and remove volumes (WARNING: This will delete all data)
docker-compose down -v

# Stop and remove everything including images
docker-compose down --rmi all --volumes --remove-orphans
```

## File Structure

```
Ekattor8/
├── Dockerfile                 # Main application container
├── docker-compose.yml         # Multi-container orchestration
├── docker-entrypoint.sh       # Container startup script
├── .dockerignore             # Files to exclude from build
├── docker/                   # Docker configuration files
│   ├── apache.conf          # Apache virtual host configuration
│   ├── nginx/               # Nginx configuration
│   │   ├── nginx.conf       # Main nginx configuration
│   │   └── conf.d/          # Server block configurations
│   └── mysql/               # MySQL configuration
│       └── init.sql         # Database initialization script
└── DOCKER_README.md         # This file
```

## Troubleshooting

### Common Issues

1. **Port conflicts**: If ports 80, 3306, or 6379 are already in use, modify the `docker-compose.yml` file to use different ports.

2. **Permission issues**: If you encounter permission issues, run:
   ```bash
   sudo chown -R $USER:$USER .
   ```

3. **Memory issues**: Ensure your Docker has at least 4GB of RAM allocated.

4. **Database connection issues**: Wait for the database container to fully start before accessing the application.

### Reset Everything

To completely reset the Docker setup:

```bash
# Stop and remove all containers, volumes, and images
docker-compose down --rmi all --volumes --remove-orphans

# Remove any remaining Docker resources
docker system prune -a --volumes

# Rebuild and start
docker-compose up -d --build
```

### Backup and Restore

**Backup database**:
```bash
docker-compose exec db mysqldump -u ekattor8_user -p ekattor8 > backup.sql
```

**Restore database**:
```bash
docker-compose exec -T db mysql -u ekattor8_user -p ekattor8 < backup.sql
```

## Production Deployment

For production deployment, consider the following:

1. **Use environment-specific .env files**
2. **Set up proper SSL certificates**
3. **Configure proper backup strategies**
4. **Use Docker secrets for sensitive data**
5. **Set up monitoring and logging**
6. **Configure proper firewall rules**

## Support

If you encounter any issues with the Docker setup, please check:

1. Docker and Docker Compose versions are up to date
2. Sufficient system resources are available
3. No port conflicts exist
4. All required files are present in the project directory

For additional support, refer to the official Ekattor 8 documentation or contact the development team. 
