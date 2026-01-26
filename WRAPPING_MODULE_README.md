# Wrapping Areas Module Setup Guide

This module provides a complete backend and frontend solution for managing wrapping areas (kitchen, bathroom, etc.) with Laravel + Metronic admin and Next.js frontend connected via GraphQL.

## Backend Setup (Laravel)

### 1. Run Migration

```bash
php artisan migrate
```

This will create the `wrapping_areas` table and `wrapping_area_product` pivot table with all necessary columns.

### 2. Seed Initial Data (Optional)

```bash
php artisan db:seed --class=WrappingAreaSeeder
```
 
This will populate the database with initial kitchen and bathroom wrapping data.

### 3. Storage Link

Make sure the storage link exists for image uploads:

```bash
php artisan storage:link
```

### 4. Admin Panel Access

Navigate to: `http://your-domain/wrapping-areas`

Features:

- **List**: View all wrapping areas with pagination
- **Create**: Add new wrapping area with all sections
- **Edit**: Update existing wrapping area
- **Delete**: Remove wrapping area (with confirmation)
- **Toggle Active**: Quickly enable/disable areas

### 5. GraphQL Endpoints

The following GraphQL queries are available:

#### Get All Wrapping Areas

```graphql
query {
    wrappingAreas(is_active: true, first: 10, page: 1) {
        data {
            id
            slug
            title
            meta_title
            meta_description
            # ... other fields
        }
        paginatorInfo {
            currentPage
            lastPage
            total
        }
    }
}
```

#### Get Single Wrapping Area

```graphql
query {
    wrappingArea(slug: "kitchen-wrapping") {
        id
        slug
        title
        meta_title
        meta_description
        keywords
        main_heading
        main_description
        main_image
        why_partner_heading
        why_partner_description
        why_partner_image
        features {
            title
            description
        }
        guide_heading
        guide_description
        guide {
            image
            heading
            subheading
            description
            features {
                title
            }
        }
        why_use_heading
        why_use_description
        hero_text
        hero_subtext
        hero_image
        gallery_heading
        gallery_description
        photos {
            src
            alt
        }
        products {
            id
            name
            slug
            thumbnail
            price
        }
    }
}
```

## Frontend Setup (Next.js)

### 1. Environment Variables

Add to your `.env.local`:

```env
NEXT_PUBLIC_GRAPHQL_ENDPOINT=http://your-backend-domain/graphql
```

### 2. Usage

The wrapping areas are now fetched from the GraphQL API automatically:

- Static paths are generated at build time
- Data is cached for 1 hour (revalidate: 3600)
- If data is not found, the 404 page is shown

### 3. Routes

- `/kitchen-wrapping` - Kitchen wrapping page
- `/bathroom-wrapping` - Bathroom wrapping page
- `/[any-slug]` - Dynamic wrapping area page

## File Structure

### Backend (Laravel)

```
app/
├── Models/
│   └── WrappingArea.php                    # Eloquent model
├── Http/Controllers/
│   └── WrappingAreaController.php          # CRUD controller
└── GraphQL/
    └── Queries/
        ├── GetWrappingAreas.php            # List query resolver
        └── GetWrappingArea.php             # Single query resolver

database/
├── migrations/
│   ├── 2026_01_22_000001_create_wrapping_areas_table.php
│   └── 2026_01_22_000002_create_wrapping_area_product_table.php
└── seeders/
    └── WrappingAreaSeeder.php

resources/views/pages/apps/wrapping/
├── index.blade.php                         # List view
├── create.blade.php                        # Create form
└── edit.blade.php                          # Edit form

routes/
└── web.php                                 # Web routes

graphql/
└── schema.graphql                          # GraphQL schema
```

### Frontend (Next.js)

```
src/
├── app/
│   └── [wrap-areas]/
│       ├── page.tsx                        # Dynamic route page
│       └── warapping.tsx                   # Component
├── lib/
│   └── wrappingAreas.ts                    # GraphQL queries & helpers
└── data/
    └── wrapping_data.ts                    # Type definitions (kept for reference)
```

## Features

### Admin Panel

✅ Full CRUD operations
✅ Image upload with preview
✅ Dynamic feature management
✅ Guide sections with multiple items
✅ Gallery management
✅ Keywords management
✅ Active/Inactive toggle
✅ Sort ordering
✅ Search functionality
✅ Metronic UI styling

### Frontend

✅ Dynamic routing
✅ SEO optimization (metadata, Open Graph, Twitter cards)
✅ Breadcrumb schema
✅ Responsive design
✅ Image optimization
✅ GraphQL integration
✅ Static generation with revalidation

### API

✅ GraphQL queries
✅ Pagination support
✅ Search functionality
✅ Active status filtering
✅ Error handling
✅ Type safety

## Customization

### Adding New Wrapping Area Fields

1. **Database**: Add column to migration
2. **Model**: Add field to `$fillable` array
3. **Controller**: Add validation rule
4. **Views**: Add form field in create/edit
5. **GraphQL Schema**: Add field to `WrappingArea` type
6. **Frontend**: Update TypeScript interface

### Styling

Backend uses Metronic theme classes. Frontend uses Tailwind CSS.

## Troubleshooting

### Images Not Showing

- Ensure `php artisan storage:link` has been run
- Check file permissions on `storage/` directory
- Verify image paths in database

### GraphQL Errors

- Check GraphQL endpoint configuration
- Verify Lighthouse package is installed
- Clear cache: `php artisan cache:clear`

### Frontend 404 Errors

- Ensure backend is running
- Check GraphQL endpoint in `.env.local`
- Verify wrapping area exists and is active

## API Testing

Use GraphQL Playground at: `http://your-domain/graphql-playground`

## Production Checklist

- [ ] Run migrations on production
- [ ] Seed initial data or create via admin panel
- [ ] Set up proper storage permissions
- [ ] Configure CORS for GraphQL endpoint
- [ ] Set production GraphQL endpoint in Next.js
- [ ] Build Next.js with `npm run build`
- [ ] Test all routes and functionality

## Support

For issues or questions, refer to:

- Laravel Documentation: https://laravel.com/docs
- Lighthouse GraphQL: https://lighthouse-php.com
- Next.js Documentation: https://nextjs.org/docs
