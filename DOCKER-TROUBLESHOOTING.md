# Docker Troubleshooting Guide

## Common Issues with Vite and Solutions

### 500 Internal Server Error

If you're seeing 500 errors after deployment, check the following:

1. **Verify Vite Build**:
   ```bash
   # Check if manifest.json exists and is valid
   docker exec laravel_app cat /var/www/public/build/manifest.json
   ```

2. **Check Laravel Logs**:
   ```bash
   docker exec laravel_app cat /var/www/storage/logs/laravel.log
   ```

3. **Check Nginx Logs**:
   ```bash
   docker exec laravel_app cat /var/log/nginx/error.log
   ```

### Fixing Vite Build Issues

If Vite build is failing inside Docker:

1. **Build Locally First**:
   ```bash
   npm ci
   npm run build
   ```

2. **Manually Copy Assets**:
   ```bash
   docker cp public/build laravel_app:/var/www/public/
   docker exec laravel_app chown -R www-data:www-data /var/www/public/build
   ```

3. **Restart Container**:
   ```bash
   docker restart laravel_app
   ```

### Clearing Laravel Cache

If you suspect cache issues:

```bash
docker exec laravel_app php artisan config:clear
docker exec laravel_app php artisan route:clear
docker exec laravel_app php artisan view:clear
docker exec laravel_app php artisan cache:clear
docker exec laravel_app php artisan optimize:clear
```

### Rebuilding Container

If you need to rebuild:

```bash
docker-compose down
docker-compose build --no-cache app
docker-compose up -d
```

## Maintenance Mode

To put the application in maintenance mode:

```bash
docker exec laravel_app php artisan down
```

To bring it back up:

```bash
docker exec laravel_app php artisan up
```

## Database Issues

If your database connection is failing:

1. **Verify Environment Variables**:
   ```bash
   docker exec laravel_app cat /var/www/.env | grep DB_
   ```

2. **Check Database Connection**:
   ```bash
   docker exec laravel_app php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Connected successfully!'; } catch (\Exception \$e) { echo 'Connection failed: ' . \$e->getMessage(); }"
   ```

## Frontend Asset Issues

If you're having issues with frontend assets:

1. **Force Replace Blade Templates**:
   ```bash
   docker exec laravel_app find /var/www/resources/views -type f -name "*.blade.php" -exec sed -i 's/@vite(\[[^]]*\])/<script src="{{ asset('\''build\/assets\/app.js'\'') }}"><\/script><link rel="stylesheet" href="{{ asset('\''build\/assets\/app.css'\'') }}">/' {} \;
   ```

2. **Clear View Cache**:
   ```bash
   docker exec laravel_app php artisan view:clear
   ```

3. **Regenerate View Cache**:
   ```bash
   docker exec laravel_app php artisan view:cache
   ``` 