<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{AuthController, DashboardController, ResourceController, CategoryController, ArticleController, UserController, SettingController, ServiceRequestController, NewsletterController, AnalyticsController, PageCmsController, LoginLogController};

Route::get('/admin/login', [AuthController::class, 'show'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->middleware('auth')->name('admin.logout');
Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/analytics/visitors', [AnalyticsController::class, 'visitors'])->name('analytics.visitors');
    Route::get('/analytics/resources', [AnalyticsController::class, 'resources'])->name('analytics.resources');
    Route::get('/analytics/files', [AnalyticsController::class, 'files'])->name('analytics.files');
    Route::get('/analytics/downloads', [AnalyticsController::class, 'downloads'])->name('analytics.downloads');
    Route::resource('resources', ResourceController::class)->except(['show']);
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::get('tools', [\App\Http\Controllers\Admin\AdminToolController::class, 'index'])->name('tools.index');
    Route::post('tools/{tool}/toggle', [\App\Http\Controllers\Admin\AdminToolController::class, 'toggle'])->name('tools.toggle');
    Route::put('tools/{tool}', [\App\Http\Controllers\Admin\AdminToolController::class, 'update'])->name('tools.update');
    Route::resource('articles', ArticleController::class)->except(['show']);
    Route::resource('users', UserController::class)->only(['index', 'update']);
    Route::get('login-logs', [LoginLogController::class, 'index'])->name('login-logs.index');
    Route::delete('login-logs/{loginLog}', [LoginLogController::class, 'destroy'])->name('login-logs.destroy');
    Route::post('login-logs/clear', [LoginLogController::class, 'clear'])->name('login-logs.clear');
    Route::get('requests', [ServiceRequestController::class, 'index'])->name('requests.index');
    Route::put('requests/{serviceRequest}', [ServiceRequestController::class, 'update'])->name('requests.update');
    Route::get('newsletter', [NewsletterController::class, 'index'])->name('newsletter.index');
    Route::put('newsletter/{subscriber}', [NewsletterController::class, 'update'])->name('newsletter.update');
    Route::get('settings', [SettingController::class, 'index'])->name('settings');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('cms/home', [PageCmsController::class, 'home'])->name('cms.home');
    Route::post('cms/home', [PageCmsController::class, 'updateHome'])->name('cms.home.update');
    Route::get('cms/about', [PageCmsController::class, 'about'])->name('cms.about');
    Route::post('cms/about', [PageCmsController::class, 'updateAbout'])->name('cms.about.update');
    Route::get('cms/services', [PageCmsController::class, 'services'])->name('cms.services');
    Route::post('cms/services', [PageCmsController::class, 'updateServices'])->name('cms.services.update');
    Route::get('cms/custom-pages', [PageCmsController::class, 'customPages'])->name('cms.custom-pages');
    Route::post('cms/custom-pages', [PageCmsController::class, 'updateCustomPages'])->name('cms.custom-pages.update');
});
Route::get('/', fn() => redirect()->route('admin.login'));
