# EzyTemplate Backend — Laravel 10

Professional Laravel 10 backend + admin console for EzyTemplate.

## Setup
```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve --port=8000
```

Admin: `/admin/login`
Default email: `admin@ezytemplate.local`
Default password: `ChangeMe123!` (change immediately in production).

## Analytics
Tracks page views, unique visitors, resource views, file views and downloads. Analytics are separated into dashboard tabs:
- Visitors & Traffic
- Resource Views
- File Analytics
- Downloads

The visitor identifier is a server-side SHA-256 hash derived from IP + user agent + application key; raw IP/user-agent are retained for operational analytics. Configure retention according to your privacy policy.

## Resource model
Resources support website, Excel, Word, design, presentations and UI kits. Each resource can have multiple files, formats and qualities.
# ezy_template_backend
