# Wrapping Areas Module Setup Script
# Run this from the Laravel root directory

Write-Host "==============================================" -ForegroundColor Cyan
Write-Host "  Wrapping Areas Module Setup" -ForegroundColor Cyan
Write-Host "==============================================" -ForegroundColor Cyan
Write-Host ""

# Check if we're in the Laravel directory
if (-not (Test-Path "artisan")) {
    Write-Host "❌ Error: Please run this script from the Laravel root directory" -ForegroundColor Red
    exit 1
}

Write-Host "✓ Laravel directory detected" -ForegroundColor Green
Write-Host ""

# Step 1: Run migrations
Write-Host "Step 1: Running migrations..." -ForegroundColor Yellow
php artisan migrate --force
Write-Host "✓ Migrations completed" -ForegroundColor Green
Write-Host ""

# Step 2: Create storage link
Write-Host "Step 2: Creating storage link..." -ForegroundColor Yellow 
php artisan storage:link
Write-Host "✓ Storage link created" -ForegroundColor Green
Write-Host ""

# Step 3: Seed wrapping areas data
Write-Host "Step 3: Seeding wrapping areas data..." -ForegroundColor Yellow
$seed = Read-Host "Do you want to seed initial data (kitchen & bathroom)? (y/n)"
if ($seed -eq 'y' -or $seed -eq 'Y') {
    php artisan db:seed --class=WrappingAreaSeeder
    Write-Host "✓ Initial data seeded" -ForegroundColor Green
} else {
    Write-Host "⊘ Skipped seeding" -ForegroundColor Gray 
}
Write-Host ""

# Step 4: Clear cache
Write-Host "Step 4: Clearing cache..." -ForegroundColor Yellow
php artisan cache:clear | Out-Null
php artisan config:clear | Out-Null
php artisan route:clear | Out-Null
php artisan view:clear | Out-Null
Write-Host "✓ Cache cleared" -ForegroundColor Green
Write-Host ""

Write-Host "==============================================" -ForegroundColor Cyan
Write-Host "  Setup Complete! ✓" -ForegroundColor Green
Write-Host "==============================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Access admin panel: http://your-domain/wrapping-areas" -ForegroundColor White
Write-Host "2. Configure GraphQL endpoint in Next.js .env.local" -ForegroundColor White
Write-Host "3. Test GraphQL at: http://your-domain/graphql-playground" -ForegroundColor White
Write-Host ""
Write-Host "Documentation: See WRAPPING_MODULE_README.md" -ForegroundColor Cyan
Write-Host ""
