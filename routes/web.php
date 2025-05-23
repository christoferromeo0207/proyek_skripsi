<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    DashboardController,
    CategoryController,
    AuthorsController,
    UserController,
    PostController,
    //NotulenController,
    TransactionController,
    ScheduleController,
    PasswordController,
    TestingUpload,
    PostMessageController,
    NotificationController,
    MitraDashboardController,
    MarketingDashboardController,
};
use App\Http\Controllers\Auth\{
    LoginController,
    RegisterController
};
use App\Models\{Category, Post, User};
use App\Mail\Email;
use App\Mail\NewMessageMail;






// //routes untuk mitra
// Route::middleware(['auth','role:mitra'])
// ->prefix('mitra')->name('mitra.')->group(function(){
//     Route::get('dashboard', [MitraDashboardController::class, 'index'])->name('dashboard');
// });


// // Routes untuk admin & marketing
// Route::middleware(['auth','role:admin,marketing'])
//      ->group(function(){
//          Route::get('/dashboard', [DashboardController::class, 'index'])
//               ->name('dashboard');
//          Route::resource('posts', PostController::class)
//               ->except(['create','edit']); // contoh
//          Route::resource('categories', CategoryController::class)
//               ->only(['index','store']);
//      });


// // Routes untuk admin
// Route::middleware(['auth','role:admin'])->prefix('admin')->name('admin.')->group(function(){
//     // route admin eksklusif
// });

Route::get('/dashboard-mitra', [MitraDashboardController::class, 'index'])
     ->middleware('auth')
     ->name('mitra.dashboard');


Route::get('/dashboardMarketing', [MarketingDashboardController::class, 'index'])
     ->middleware('auth')
     ->name('marketing.dashboard');


Route::get('/dashboard', [DashboardController::class, 'index'])
     ->middleware('auth')
     ->name('dashboard');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('index');
    Route::post('/', [LoginController::class, 'login'])->name('login');
    Route::get('register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

// Password Reset Routes
Route::controller(PasswordController::class)->group(function () {
    Route::get('forgot-password', 'showForgotPasswordForm')->name('password.request');
    Route::post('/check-username', 'checkUsername')->name('check-username');
    Route::post('reset-password', 'resetPassword')->name('password.update');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', function () {Auth::logout();return redirect()->route('login');})->name('logout');
    Route::get('/users',                 [UserController::class, 'index'])->name('user.index');
    Route::get('/users/create',          [UserController::class, 'create'])->name('user.create');
    Route::post('/users',                [UserController::class, 'store'])->name('user.store');
    Route::get('/users/{user}/edit',     [UserController::class, 'edit'])->name('user.edit');
    Route::put('/users/{user}',          [UserController::class, 'update'])->name('user.update');
    Route::delete('/users/{user}',       [UserController::class, 'destroy'])->name('user.destroy');
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications');

    // Posts Resource
    Route::prefix('posts')->controller(PostController::class)->group(function () {
        Route::get('/',                'index'      )->name('posts.index');
        //Route::get('/create',          'create'     )->name('posts.create');
        Route::post('/',               'store'      )->name('posts.store');
        Route::get('/{post:slug}',     'show'       )->name('posts.show');
        Route::get('/{post:slug}/edit','edit'       )->name('posts.edit');
        Route::put('/{post:slug}',     'update'     )->name('posts.update');
        // perbaikan di sini:
        Route::delete('/{post:slug}',  'destroy'    )->name('posts.destroy');
        Route::post('/{post:slug}/changePIC','changePIC')->name('posts.changePIC');
        Route::get('/category/{category:slug}', 'index')->name('posts.category');
    });


    // Messages
    Route::prefix('posts/{post}/messages')->name('posts.messages.')->controller(PostMessageController::class)->group(function(){
            Route::get('/',           'index')->name('index');
            Route::get('/create',     'create')->name('create');
            Route::post('/',          'store')->name('store');
            Route::get('/{message}',  'show')->name('show');
            Route::post('/{message}/read', 'markRead')->name('read');
            Route::post('/sendgrid',  'storeViaSendgrid')->name('sendgrid');
        });
    
    
    


    // Categories
    Route::controller(CategoryController::class)->prefix('categories')->group(function () {
        Route::get('/', 'index')->name('categories.index');
        Route::post('/{id}/add-data', 'addData')->name('addData');
    });

    // Users/Pegawai Marketing
    Route::prefix('authors')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('authors.index');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('updateUser');
    });
    
    
    Route::prefix('posts/{post}/transactions')->name('posts.transactions.')->controller(TransactionController::class)->group(function(){
         Route::get('/',                 'index')->name('index');
         Route::get('/create',           'create')->name('create');
         Route::post('/',                'store')->name('store');
         Route::get('/{transaction}',    'show')->name('show');
         Route::get('/{transaction}/edit','edit')->name('edit');
         Route::put('/{transaction}',    'update')->name('update');
         Route::delete('/{transaction}', 'destroy')->name('destroy');
         Route::post('/{transaction}/files/delete', 'deleteFile')->name('files.delete');
         Route::post('/{transaction}/files/rename', 'renameFile')->name('files.rename');
     });


    // Schedule
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');

    // Static Pages
    Route::get('/about', function () {
        return view('about', ['name' => 'San Deep', 'title' => 'About']);
    });

    Route::get('/contact', function () {
        return view('contact', ['title' => 'Contact']);
    });
});

// Public Routes (accessible without auth)
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/authors/{user:username}', function(User $user) {
    return view('posts', [
        'title' => count($user->posts) . ' Articles by ' . $user->name,
        'posts' => $user->posts
    ]);
});
Route::get('/categories/{category:slug}', function(Category $category) {
    return view('posts', [
        'title' => 'Articles in Category ' . $category->name,
        'posts' => $category->posts
    ]);
});


