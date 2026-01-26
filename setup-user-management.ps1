# User Management Setup Script
# Run this script in PowerShell to set up the user management system

Write-Host "=====================================" -ForegroundColor Cyan
Write-Host "User Management System Setup" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host ""

# Run migrations
Write-Host "1. Running migrations..." -ForegroundColor Yellow
php artisan migrate
Write-Host "✓ Migrations completed" -ForegroundColor Green
Write-Host ""

# Seed roles and permissions
Write-Host "2. Seeding roles and permissions..." -ForegroundColor Yellow
php artisan db:seed --class=RolePermissionSeeder
Write-Host "✓ Roles and permissions seeded" -ForegroundColor Green
Write-Host ""

# Create admin user
Write-Host "3. Creating admin user..." -ForegroundColor Yellow
php artisan app:make-admin admin@gmail.com --password=Admin@123
Write-Host "✓ Admin user created" -ForegroundColor Green
Write-Host ""

# Clear cache
Write-Host "4. Clearing cache..." -ForegroundColor Yellow
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
Write-Host "✓ Cache cleared" -ForegroundColor Green
Write-Host ""

Write-Host "=====================================" -ForegroundColor Cyan
Write-Host "Setup Complete!" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Admin Login Credentials:" -ForegroundColor Yellow
Write-Host "Email: yohan@gmail.com" -ForegroundColor White
Write-Host "Password: Yohan@123" -ForegroundColor White
Write-Host ""
Write-Host "You can now login and access:" -ForegroundColor Yellow
Write-Host "- User Management" -ForegroundColor White
Write-Host "- Role Management" -ForegroundColor White
Write-Host "- Permission Management" -ForegroundColor White
Write-Host ""
