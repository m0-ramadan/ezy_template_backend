<?php
namespace App\Providers;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider
{
    public function register() {}

    public function boot(): void
    {
        // The admin dashboard ships its own CSS rather than Tailwind. Laravel's
        // default Tailwind pagination markup therefore renders duplicated rows
        // and unbounded SVG arrows. Use the dashboard-native pagination view.
        Paginator::defaultView('vendor.pagination.default');
        Paginator::defaultSimpleView('vendor.pagination.simple-default');
    }
}
