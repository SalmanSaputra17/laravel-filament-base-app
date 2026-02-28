# Laravel 12 Admin Skeleton App

A Laravel 12 admin skeleton application with FilamentPHP v3, roles & permissions, activity logging, and more.

## Tech Stack

- **Framework:** Laravel 12
- **Admin Panel:** FilamentPHP v3 (TALL Stack)
- **Roles & Permissions:** spatie/laravel-permission + bezhansalleh/filament-shield
- **Activity Log:** spatie/laravel-activitylog
- **Media Management:** spatie/laravel-medialibrary
- **Monitoring:** Laravel Pulse & Laravel Telescope

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   ```
3. Copy environment file:
   ```bash
   cp .env.example .env
   ```
4. Generate application key:
   ```bash
   php artisan key:generate
   ```
5. Run migrations:
   ```bash
   php artisan migrate
   ```
6. Create admin user:
   ```bash
   php artisan make:filament-user
   ```
7. Install Filament Shield:
   ```bash
   php artisan shield:install
   ```
8. Generate permissions:
   ```bash
   php artisan shield:generate
   ```

## Features

### 1. Admin Panel (Filament v3)
- Access at `/admin`
- Login with Filament user

### 2. Roles & Permissions (Filament Shield)
- UI for managing roles and permissions
- "Super Admin" role has all access automatically
- Run `php artisan shield:generate` to generate permissions for new resources

### 3. HasAuthor Trait
Automatically populates `created_by` and `updated_by` fields:
```php
use App\Traits\HasAuthor;

class MyModel extends Model
{
    use HasAuthor;
}
```

### 4. Activity Log
- All model changes (Created, Updated, Deleted) are logged
- Access Activity Log from admin panel (Super Admin only)

### 5. Module Generator Command

Create a new module with all necessary files:

```bash
php artisan make:module Post
```

This command will:
1. Create Model with HasAuthor trait and activity logging
2. Create Migration with created_by and updated_by foreign keys
3. Create Filament Resource with table and form
4. Create Policy with standard CRUD permissions
5. Create Filament Resource pages (List, Create, Edit)

After creating a module:
1. Run migrations: `php artisan migrate`
2. Register the policy in `App/Providers/AuthServiceProvider.php`
3. Generate permissions: `php artisan shield:generate`

### 6. Settings Page
- Application Name and URL settings
- Available in admin panel (Super Admin only)

### 7. Indonesian Date Format
All table columns use Indonesian format: `M Y, H:i`

## Commands Reference

```bash
# Create admin user
php artisan make:filament-user

# Install Filament Shield
php artisan shield:install

# Generate permissions for all resources
php artisan shield:generate

# Create new module
php artisan make:module {name}

# Run Telescope (local only)
php artisan telescope:install
php artisan migrate

# Run Pulse (local only)
php artisan pulse:install
php artisan migrate
```

## File Structure

```
app/
├── Console/Commands/MakeModuleCommand.php
├── Filament/
│   ├── Admin/AdminPanelProvider.php
│   ├── Pages/Settings/GeneralSettings.php
│   └── Resources/
│       ├── ActivityLogResource.php
│       └── UserResource.php
├── Models/
│   └── User.php
├── Providers/
│   └── AppServiceProvider.php
└── Traits/
    └── HasAuthor.php
```

## Environment Variables

The application uses SQLite by default for local development. To use MySQL, update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_base_app
DB_USERNAME=root
DB_PASSWORD=
```

## License

MIT
