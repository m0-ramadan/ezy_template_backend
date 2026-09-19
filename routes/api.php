<?php

use App\Http\Controllers\Api\{ResourceController, CategoryController, ArticleController, DownloadController, NewsletterController, ServiceRequestController, CmsController, AuthController};

Route::get('/health', fn() => ['ok' => true, 'app' => 'EzyTemplate', 'version' => '1.0']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::get('/settings', [CmsController::class, 'settings']);
Route::get('/stats/realtime', [CmsController::class, 'realtimeStats']);
Route::get('/content/home', [CmsController::class, 'home']);
Route::get('/content/about', [CmsController::class, 'about']);
Route::get('/content/services', [CmsController::class, 'services']);
Route::get('/content/custom-pages', [CmsController::class, 'customPages']);
Route::get('/content/page/{key}', [CmsController::class, 'customPageByKey']);
Route::get('/resources', [ResourceController::class, 'index']);
Route::get('/resources/{slug}', [ResourceController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/main-categories', [CategoryController::class, 'mainCategories']);
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{slug}', [ArticleController::class, 'show']);
Route::post('/newsletter', [NewsletterController::class, 'store']);
Route::post('/service-requests', [ServiceRequestController::class, 'store']);
Route::post('/resources/{resource}/files/{file}/download', [DownloadController::class, 'start'])->middleware([\Illuminate\Session\Middleware\StartSession::class]);
Route::get('/downloads/{token}', [DownloadController::class, 'stream'])->middleware([\Illuminate\Session\Middleware\StartSession::class])->name('api.download.stream');

// Tools Platform API Endpoints
Route::get('/tools', [\App\Http\Controllers\Api\ToolApiController::class, 'index']);
Route::get('/tools/{slug}', [\App\Http\Controllers\Api\ToolApiController::class, 'show']);
Route::post('/tools/{slug}/track', [\App\Http\Controllers\Api\ToolApiController::class, 'track']);
