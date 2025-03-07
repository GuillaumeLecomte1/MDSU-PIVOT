# Docker Setup for Laravel with Vite

This directory contains the Docker configuration files for the Laravel application with Vite.

## Files

- `Dockerfile`: Multi-stage build for the Laravel application with Vite
- `nginx.conf`: Nginx configuration for serving the Laravel application
- `php-fpm.conf`: PHP-FPM configuration
- `supervisord.conf`: Supervisor configuration for managing Nginx and PHP-FPM processes

## Building the Docker Image

```bash
docker build -t marketplace-app --build-arg PORT=4002 .
```

## Running the Docker Container

```bash
docker run -p 4002:4002 -e APP_ENV=production -e APP_KEY=your-app-key marketplace-app
```

## Environment Variables

Make sure to set the following environment variables:

- `APP_ENV`: The application environment (e.g., production)
- `APP_KEY`: The application key (generate with `php artisan key:generate`)
- `DB_HOST`: The database host
- `DB_PORT`: The database port
- `DB_DATABASE`: The database name
- `DB_USERNAME`: The database username
- `DB_PASSWORD`: The database password

## Notes

- The Docker image uses PHP 8.2 and Node.js 18
- The application is served on the port specified by the `PORT` build argument (default: 80)
- The application is built with Vite and the assets are copied to the `/var/www/public/build` directory 