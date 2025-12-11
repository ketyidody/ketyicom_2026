# Photographer Portfolio & E-shop

A modern photographer portfolio website with integrated e-commerce functionality, built with Laravel 12, Inertia.js, Vue 3, and Tailwind CSS.

## Features

### Photo Gallery
- **Album Management** - Organize photos into themed collections
- **Multiple Image Upload** - Upload multiple photos at once with automatic thumbnail generation
- **Image Processing** - Automatic creation of thumbnail, medium, and original sizes
- **Lazy Loading** - Optimized image loading for better performance
- **Lightbox Viewer** - Full-screen photo viewing experience
- **Featured Photos** - Highlight special images in your portfolio

### E-commerce Shop
- **Product Catalog** - Sell prints, digital downloads, and photography services
- **Search & Filtering** - Filter products by type and search functionality
- **Shopping Cart** - Session-based cart system supporting guest checkout
- **Checkout Process** - Comprehensive order form with customer and shipping details
- **Order Management** - Admin dashboard to view and manage orders
- **Stock Management** - Real-time inventory tracking

### Admin Panel
- **Dashboard** - Overview of albums, photos, products, and orders
- **Album CRUD** - Create, read, update, and delete photo albums
- **Photo Management** - Bulk upload with automatic processing
- **Product Management** - Add products linked to photos or standalone
- **Order Processing** - View orders, update status, and track fulfillment

## Tech Stack

- **Backend**: Laravel 12 (PHP 8.2+)
- **Frontend**: Vue 3 with Composition API
- **Routing**: Inertia.js (SPA-like experience without separate API)
- **Styling**: Tailwind CSS
- **Authentication**: Laravel Breeze with Inertia stack
- **Database**: MariaDB/MySQL (configurable to PostgreSQL or SQLite)
- **Build Tool**: Vite
- **Image Processing**: Intervention Image v3
- **Route Helpers**: Ziggy (Laravel routes in JavaScript)

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js 18.x or higher
- NPM or Yarn
- MariaDB 10.5+ or MySQL 8.0+ (or PostgreSQL/SQLite)

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd ketyi.com_2026
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install JavaScript Dependencies

```bash
npm install --legacy-peer-deps
```

### 4. Environment Setup

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Configure Environment

Edit `.env` file and set your configuration:

```env
APP_NAME="Photographer Portfolio"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# For SQLite alternative, use:
# DB_CONNECTION=sqlite
# (Database will be created at database/database.sqlite)
```

### 6. Create Database

For MySQL/MariaDB, create the database:
```bash
mysql -u root -p
CREATE DATABASE your_database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

Or use your preferred database management tool (phpMyAdmin, TablePlus, etc.).

For SQLite alternative:
```bash
touch database/database.sqlite
```

### 7. Run Migrations

```bash
php artisan migrate
```

### 8. Create Storage Symlink

This links the public storage folder for image access:

```bash
php artisan storage:link
```

### 9. (Optional) Seed Sample Data

If you have seeders configured:

```bash
php artisan db:seed
```

## Running the Application

### Development Mode

Start the Laravel development server:

```bash
php artisan serve
```

In a separate terminal, start the Vite development server for hot module replacement:

```bash
npm run dev
```

The application will be available at `http://localhost:8000`

### Building for Production

Build optimized frontend assets:

```bash
npm run build
```

## Project Structure

```
.
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Admin/          # Admin panel controllers
│   │       ├── CartController.php
│   │       ├── CheckoutController.php
│   │       ├── GalleryController.php
│   │       └── ShopController.php
│   └── Models/                 # Eloquent models
│       ├── Album.php
│       ├── Photo.php
│       ├── Product.php
│       ├── CartItem.php
│       ├── Order.php
│       └── OrderItem.php
│
├── database/
│   └── migrations/             # Database schema migrations
│
├── resources/
│   ├── js/
│   │   ├── Components/         # Reusable Vue components
│   │   ├── Layouts/            # Layout components
│   │   └── Pages/              # Full-page components
│   │       ├── Admin/          # Admin pages
│   │       ├── Gallery/        # Public gallery pages
│   │       ├── Shop/           # Shop pages
│   │       ├── Cart/           # Shopping cart
│   │       └── Checkout/       # Checkout flow
│   └── css/
│       └── app.css             # Tailwind CSS
│
├── routes/
│   ├── web.php                 # Application routes
│   └── auth.php                # Authentication routes
│
└── public/
    └── storage/                # Publicly accessible storage (symlinked)
```

## Development Workflow

### Creating a New Album

1. Log in to admin panel at `/dashboard`
2. Navigate to Albums → Create New Album
3. Fill in album details and upload a cover image
4. Save the album

### Uploading Photos

1. Navigate to Photos → Upload Photos
2. Select an album
3. Choose multiple images (bulk upload supported)
4. Optionally set a base title and description
5. Upload photos - thumbnails generated automatically

### Adding Products

1. Navigate to Products → Create Product
2. Link to a photo (optional) or upload product image
3. Set product type, price, and stock
4. Publish the product

### Managing Orders

1. Navigate to Orders in admin panel
2. View order details, customer information
3. Update order status (pending → processing → completed)

## Image Storage

Images are stored in the `storage/app/public` directory with the following structure:

```
storage/app/public/
├── photos/
│   ├── original/      # Full-size original images
│   ├── medium/        # 1200px wide versions
│   └── thumbnails/    # 400px wide versions
├── albums/            # Album cover images
└── products/          # Product images
```

## Environment Configuration

### Database Options

**MySQL/MariaDB** (Default):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=photographer_db
DB_USERNAME=root
DB_PASSWORD=secret
```

**SQLite** (Alternative - good for quick development):
```env
DB_CONNECTION=sqlite
```

### File Storage

Configure filesystem disk in `.env`:
```env
FILESYSTEM_DISK=public
```

For cloud storage (S3, etc.), update `config/filesystems.php` and set appropriate credentials.

## Deployment

### Production Checklist

1. Set environment to production:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   ```

2. Optimize application:
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. Build frontend assets:
   ```bash
   npm run build
   ```

4. Ensure database is properly configured and optimized

5. Configure web server (Nginx/Apache) to point to `public/` directory

6. Set up SSL certificate

7. Configure email settings for order notifications

8. Set appropriate file permissions:
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

## Troubleshooting

### Images not displaying

Ensure storage symlink is created:
```bash
php artisan storage:link
```

Check file permissions:
```bash
chmod -R 755 storage
```

### NPM Install Issues

If you encounter peer dependency conflicts:
```bash
npm install --legacy-peer-deps
```

### Database Connection Error

For MySQL/MariaDB, verify:
- Database credentials are correct in `.env`
- Database exists
- MySQL service is running: `sudo systemctl status mysql` (Linux) or check via your database tool

For SQLite alternative, ensure the database file exists:
```bash
touch database/database.sqlite
```

### Vite Not Running

Clear Vite cache:
```bash
rm -rf node_modules/.vite
npm run dev
```

## Default Admin Access

After installation, you'll need to create an admin user. You can do this via Laravel Tinker:

```bash
php artisan tinker
```

Then create a user:
```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@example.com';
$user->password = bcrypt('password');
$user->save();
```

## Testing

Run PHP tests:
```bash
php artisan test
```

## License

This project is proprietary software. All rights reserved.

## Support

For issues or questions, please contact the development team.
