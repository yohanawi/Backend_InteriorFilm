#!/usr/bin/env php
<?php

echo "==============================================\n";
echo "  Wrapping Areas Module Setup\n";
echo "==============================================\n\n";

// Check if we're in the Laravel directory
if (!file_exists('artisan')) {
    echo "❌ Error: Please run this script from the Laravel root directory\n";
    exit(1);
}

echo "✓ Laravel directory detected\n\n";

// Step 1: Run migrations
echo "Step 1: Running migrations...\n";
$output = shell_exec('php artisan migrate --force 2>&1');
echo $output;

if (strpos($output, 'Migration table created successfully') !== false || 
    strpos($output, 'Migrating:') !== false || 
    strpos($output, 'Nothing to migrate') !== false) {
    echo "✓ Migrations completed\n\n";
} else {
    echo "⚠ Warning: Migration may have encountered issues\n\n";
}

// Step 2: Create storage link
echo "Step 2: Creating storage link...\n";
$output = shell_exec('php artisan storage:link 2>&1'); 
echo $output;
echo "✓ Storage link created\n\n";

// Step 3: Seed wrapping areas data
echo "Step 3: Seeding wrapping areas data...\n";
echo "Do you want to seed initial data (kitchen & bathroom)? (y/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
if (trim($line) === 'y' || trim($line) === 'Y') {
    $output = shell_exec('php artisan db:seed --class=WrappingAreaSeeder 2>&1');
    echo $output;
    echo "✓ Initial data seeded\n\n";
} else {
    echo "⊘ Skipped seeding\n\n";
}
fclose($handle);

// Step 4: Clear cache
echo "Step 4: Clearing cache...\n";
shell_exec('php artisan cache:clear');
shell_exec('php artisan config:clear');
shell_exec('php artisan route:clear');
shell_exec('php artisan view:clear');
echo "✓ Cache cleared\n\n";

echo "==============================================\n";
echo "  Setup Complete! ✓\n";
echo "==============================================\n\n";
echo "Next steps:\n";
echo "1. Access admin panel: http://your-domain/wrapping-areas\n";
echo "2. Configure GraphQL endpoint in Next.js .env.local\n";
echo "3. Test GraphQL at: http://your-domain/graphql-playground\n\n";
echo "Documentation: See WRAPPING_MODULE_README.md\n";
