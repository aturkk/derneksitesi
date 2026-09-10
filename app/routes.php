<?php
declare(strict_types=1);

/** @var App\Core\Router $router */

// --- Ön yüz ---
$router->get('/', [App\Controllers\HomeController::class, 'index']);
$router->get('/haberler', [App\Controllers\NewsController::class, 'index']);
$router->get('/haber/{slug}', [App\Controllers\NewsController::class, 'show']);
$router->get('/galeri', [App\Controllers\GalleryController::class, 'index']);
$router->get('/galeri/{slug}', [App\Controllers\GalleryController::class, 'show']);
$router->get('/iletisim', [App\Controllers\ContactController::class, 'show']);
$router->post('/iletisim', [App\Controllers\ContactController::class, 'submit']);
$router->get('/arama', [App\Controllers\SearchController::class, 'index']);

// --- Yönetim paneli: oturum ---
$router->get('/admin/login', [App\Controllers\Admin\AuthController::class, 'showLogin']);
$router->post('/admin/login', [App\Controllers\Admin\AuthController::class, 'login']);
$router->get('/admin/logout', [App\Controllers\Admin\AuthController::class, 'logout']);

// --- Yönetim paneli: bölümler ---
$router->get('/admin', [App\Controllers\Admin\DashboardController::class, 'index']);

$router->get('/admin/pages', [App\Controllers\Admin\PagesController::class, 'index']);
$router->get('/admin/pages/new', [App\Controllers\Admin\PagesController::class, 'create']);
$router->post('/admin/pages', [App\Controllers\Admin\PagesController::class, 'store']);
$router->get('/admin/pages/{id}/edit', [App\Controllers\Admin\PagesController::class, 'edit']);
$router->post('/admin/pages/{id}', [App\Controllers\Admin\PagesController::class, 'update']);
$router->post('/admin/pages/{id}/delete', [App\Controllers\Admin\PagesController::class, 'destroy']);
$router->post('/admin/pages/{id}/move/{dir}', [App\Controllers\Admin\PagesController::class, 'move']);

$router->get('/admin/posts', [App\Controllers\Admin\PostsController::class, 'index']);
$router->get('/admin/posts/new', [App\Controllers\Admin\PostsController::class, 'create']);
$router->post('/admin/posts', [App\Controllers\Admin\PostsController::class, 'store']);
$router->get('/admin/posts/{id}/edit', [App\Controllers\Admin\PostsController::class, 'edit']);
$router->post('/admin/posts/{id}', [App\Controllers\Admin\PostsController::class, 'update']);
$router->post('/admin/posts/{id}/delete', [App\Controllers\Admin\PostsController::class, 'destroy']);

$router->get('/admin/categories', [App\Controllers\Admin\CategoriesController::class, 'index']);
$router->post('/admin/categories', [App\Controllers\Admin\CategoriesController::class, 'store']);
$router->post('/admin/categories/{id}/delete', [App\Controllers\Admin\CategoriesController::class, 'destroy']);

$router->get('/admin/albums', [App\Controllers\Admin\AlbumsController::class, 'index']);

$router->get('/admin/albums/new', [App\Controllers\Admin\AlbumsController::class, 'create']);
$router->post('/admin/albums', [App\Controllers\Admin\AlbumsController::class, 'store']);
$router->get('/admin/albums/{id}', [App\Controllers\Admin\AlbumsController::class, 'show']);
$router->post('/admin/albums/{id}', [App\Controllers\Admin\AlbumsController::class, 'addPhotos']);
$router->post('/admin/albums/{id}/update', [App\Controllers\Admin\AlbumsController::class, 'update']);
$router->post('/admin/albums/{id}/delete', [App\Controllers\Admin\AlbumsController::class, 'destroy']);
$router->post('/admin/photos/{id}/delete', [App\Controllers\Admin\AlbumsController::class, 'destroyPhoto']);
$router->post('/admin/photos/{id}/move/{dir}', [App\Controllers\Admin\AlbumsController::class, 'movePhoto']);

$router->get('/admin/slides', [App\Controllers\Admin\SlidesController::class, 'index']);
$router->get('/admin/slides/new', [App\Controllers\Admin\SlidesController::class, 'create']);
$router->post('/admin/slides', [App\Controllers\Admin\SlidesController::class, 'store']);
$router->get('/admin/slides/{id}/edit', [App\Controllers\Admin\SlidesController::class, 'edit']);
$router->post('/admin/slides/{id}', [App\Controllers\Admin\SlidesController::class, 'update']);
$router->post('/admin/slides/{id}/delete', [App\Controllers\Admin\SlidesController::class, 'destroy']);
$router->post('/admin/slides/{id}/move/{dir}', [App\Controllers\Admin\SlidesController::class, 'move']);

$router->get('/admin/messages', [App\Controllers\Admin\MessagesController::class, 'index']);
$router->get('/admin/messages/{id}', [App\Controllers\Admin\MessagesController::class, 'show']);
$router->post('/admin/messages/{id}/delete', [App\Controllers\Admin\MessagesController::class, 'destroy']);

$router->get('/admin/settings', [App\Controllers\Admin\SettingsController::class, 'edit']);
$router->post('/admin/settings', [App\Controllers\Admin\SettingsController::class, 'update']);

$router->get('/admin/users', [App\Controllers\Admin\UsersController::class, 'index']);
$router->get('/admin/users/new', [App\Controllers\Admin\UsersController::class, 'create']);
$router->post('/admin/users', [App\Controllers\Admin\UsersController::class, 'store']);
$router->get('/admin/users/{id}/edit', [App\Controllers\Admin\UsersController::class, 'edit']);
$router->post('/admin/users/{id}', [App\Controllers\Admin\UsersController::class, 'update']);
$router->post('/admin/users/{id}/delete', [App\Controllers\Admin\UsersController::class, 'destroy']);

$router->get('/admin/profile', [App\Controllers\Admin\UsersController::class, 'profile']);
$router->post('/admin/profile', [App\Controllers\Admin\UsersController::class, 'updateProfile']);

$router->post('/admin/upload', [App\Controllers\Admin\MediaController::class, 'upload']);

// --- Dinamik sayfalar en sonda ---
$router->get('/{slug}', [App\Controllers\PageController::class, 'show']);
