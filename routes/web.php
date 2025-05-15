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
    TestingUpload
};
use App\Http\Controllers\Auth\{
    LoginController,
    RegisterController
};
use App\Models\{Category, Post, User};

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
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('logout');

    Route::get('/users',                 [UserController::class, 'index'])->name('user.index');
    Route::get('/users/create',          [UserController::class, 'create'])->name('user.create');
    Route::post('/users',                [UserController::class, 'store'])->name('user.store');
    Route::get('/users/{user}/edit',     [UserController::class, 'edit'])->name('user.edit');
    Route::put('/users/{user}',          [UserController::class, 'update'])->name('user.update');
    Route::delete('/users/{user}',       [UserController::class, 'destroy'])->name('user.destroy');

    // Posts Resource
    Route::prefix('posts')
         ->controller(PostController::class)
         ->group(function () {
        Route::get('/',               'index')->name('posts.index');
        Route::get('/create',         'create')->name('posts.create');
        Route::post('/',              'store')->name('posts.store');
        Route::get('/{post:slug}',    'show')->name('posts.show');
        Route::get('/{post:slug}/edit','edit')->name('posts.edit');
        Route::put('/{post:slug}',     'update')->name('posts.update');
        Route::delete('/posts/{post}',       'destroy')->name('posts.destroy');
        Route::post('/{id}/changePIC','changePIC')->name('posts.changePIC');
        Route::get('/category/{category:slug}', 'index')->name('posts.category');
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
    
    //Transactions
    Route::controller(TransactionController::class)->prefix('transactions')->group(function () {
        Route::get('transactions',          [TransactionController::class, 'index'])  ->name('transactions.index');
        Route::get('/posts/{post}/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
        Route::get('posts/{post}/transactions/create',   [TransactionController::class, 'create']) ->name('transactions.create');
        Route::post('posts/{post}/transactions', [TransactionController::class, 'store'])->name('transactions.store');
        Route::get('transactions/{transaction}/edit',   [TransactionController::class, 'edit'])   ->name('transactions.edit');
        Route::put(
        'posts/{post}/transactions/{transaction}',
        [TransactionController::class, 'update']
        )->name('posts.transactions.update');

        Route::delete('transactions/{transaction}',     [TransactionController::class, 'destroy'])->name('transactions.destroy');
        //Detail Transaction
       
        Route::get('posts/{post}/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
        
        
        Route::delete(
            'posts/{post}/transactions/{transaction}/file/{filename}',
            [TransactionController::class, 'destroyFile']
        )->name('posts.transactions.file.destroy');
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