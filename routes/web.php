<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\{
    DashboardController,
    CategoryController,
    AuthorsController,
    UserController,
    PostController,
    TransactionController,
    ScheduleController,
    PasswordController,
    TestingUpload,
    PostMessageController,
    NotificationController,
    MitraDashboardController,
    MarketingDashboardController,
    MitraMessageController,
    MitraTransactionController,
    CommissionController,
    MasterBarangController,
    MasterJasaController,
};
use App\Http\Controllers\Auth\{
    LoginController,
    RegisterController,
    MitraRegisterController
};
use App\Models\{Category, Post, User};
use App\Mail\Email;
use App\Mail\NewMessageMail;




// Grup Routes Milik Mitra

Route::middleware(['auth'])->prefix('dashboard-mitra')->name('mitra.')->group(function() {
        Route::get('/', [MitraDashboardController::class, 'index'])
              ->name('dashboard');
        Route::get('informasi/{post:slug}', [MitraDashboardController::class,'show'])
             ->name('informasi.show');

        Route::get('informasi/{post:slug}/edit', [MitraDashboardController::class,'edit'])
             ->name('editMitra');
        Route::put('informasi/{post:slug}', [MitraDashboardController::class, 'update'])
             ->name('updateMitra');

        // Mitra baru
        Route::get('mitra/create', [MitraDashboardController::class, 'createPartner'])  
             ->name('create');
        Route::post('mitra',      [MitraDashboardController::class, 'storePartner'])
             ->name('store');
 
        // Message+Notification
        Route::prefix('informasi/{post:slug}')->name('informasi.')->group(function() {
            Route::get('notifications', [NotificationController::class, 'mitraIndex'])
                ->name('notifications');

            Route::get('messages',            [MitraMessageController::class,'index'])
                ->name('messages.index');
            Route::get('messages/create',     [MitraMessageController::class,'create'])
                ->name('messages.create');

            Route::post('messages', [MitraMessageController::class,'store'])
                ->name('messages.store');
            Route::put('messages/{message}/read', [MitraMessageController::class,'markRead'])
                ->name('messages.markRead');
                
            Route::put('messages/{message}/{filename}/rename',
                [MitraMessageController::class,'renameAttachment'])
                ->name('messages.renameAttachment');
            Route::delete('messages/{message}/{filename}',
                [MitraMessageController::class,'deleteAttachment'])
                ->name('messages.deleteAttachment');

            // Transaksi  
            Route::get('transaksi', [MitraTransactionController::class,'index'])
                ->name('transactions.index');
            Route::get('transaksi/create',    [MitraTransactionController::class,'create'])
                ->name('transactions.create');
            Route::post('transaksi',          [MitraTransactionController::class,'store'])
                ->name('transactions.store');

            Route::get('transaksi/{transaction}', [MitraDashboardController::class,'showTransaction'])
                ->name('transactions.show')
                ->whereNumber('transaction');    // mencegah “create” tertangkap di sini
            Route::put('transaksi/{transaction}', [MitraDashboardController::class,'updateTransaction'])
                ->name('transactions.update')
                ->whereNumber('transaction');

        });
});  





// marketing 
Route::middleware(['auth'])
     ->get('/dashboardMarketing', [MarketingDashboardController::class, 'index'])
     ->name('dashboardMarketing');

Route::get('/posts-pic', [MarketingDashboardController::class, 'postsPIC'])
->middleware(['auth'])
->name('posts.pic');


Route::post('/master-barangs', [MasterBarangController::class, 'store'])->name('master_barangs.store');
Route::post('/master-jasas', [MasterJasaController::class, 'store'])->name('master_jasas.store');

// Inline store untuk MasterBarang
Route::post('/master-barangs/inline', [App\Http\Controllers\MasterBarangController::class, 'storeInline'])
    ->name('master-barangs.store.inline');

// Inline store untuk MasterJasa
Route::post('/master-jasas/inline', [App\Http\Controllers\MasterJasaController::class, 'storeInline'])
    ->name('master-jasas.store.inline');


// Route detail transaksi jasa
Route::get('/posts/{post}/transactions/jasa/{transaction}', [TransactionController::class, 'showJasa'])->name('posts.transactions.jasa.show');


    
//role: admin
Route::get('/dashboard', [DashboardController::class, 'index'])
     ->middleware('auth')
     ->name('dashboard');



// Authentication Routes
//role: admin,marketing,mitra
Route::middleware('guest')->group(function () {
    Route::get('/',         [LoginController::class, 'showLoginForm'])->name('index');
    Route::post('/',        [LoginController::class, 'login'])->name('login');

    // marketing/admin
    Route::get('register',  [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    // mitra
    Route::get('register/mitra',       [MitraRegisterController::class, 'show'])
         ->name('register.mitra');
    Route::post('register/mitra',      [MitraRegisterController::class, 'register'])
         ->name('register.mitra.submit');
});

// email‐verification 
Route::middleware('auth')->group(function () {
    // verifikasi
    Route::get('email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    // link ke email
    Route::get('email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard-mitra'); // or wherever
    })
    ->middleware('signed')
    ->name('verification.verify');

    // resend link
    Route::post('email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status','Link verifikasi telah dikirim ulang.');
    })
    ->middleware('throttle:6,1')
    ->name('verification.send');
});



// Password Reset Routes
Route::controller(PasswordController::class)->group(function () {
    Route::get('forgot-password', 'showForgotPasswordForm')->name('password.request');
    Route::post('/check-username', 'checkUsername')->name('check-username');
    Route::post('reset-password', 'resetPassword')->name('password.update');
});

// Authenticated Routes
//role: admin
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', function () {Auth::logout();return redirect()->route('login');})->name('logout');    
    Route::get('/users', [UserController::class, 'index'])->name('user.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('user.create');
    Route::post('/users', [UserController::class, 'store'])->name('user.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('user.destroy');
    
    //role: admin, marketing
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications');


    // Messages
    //role: admin, marketing
    Route::prefix('posts/{post:slug}/messages')->name('posts.messages.')->controller(PostMessageController::class)->group(function(){
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{message}', 'show')->name('show');
            Route::post('/{message}/read', 'markRead')->name('read');
            Route::post('/sendgrid', 'storeViaSendgrid')->name('sendgrid');
        });

    //role: admin, marketing
    // Posts Resource
    Route::prefix('posts')->controller(PostController::class)->group(function () {
        Route::get('/', 'index')->name('posts.index');
        //Route::get('/create',          'create'     )->name('posts.create');
        Route::post('/',               'store'      )->name('posts.store');
        Route::delete('/{child:slug}/clear-commission', 'clearCommission')
             ->name('posts.clearCommission');
        Route::get('/{post:slug}',     'show'       )->name('posts.show');
        Route::get('/{post:slug}/edit','edit'       )->name('posts.edit');
        Route::put('/{post:slug}',     'update'     )->name('posts.update');
        // perbaikan di sini:
        Route::delete('/{post:slug}',  'destroy'    )->name('posts.destroy');
        Route::post('/{post:slug}/changePIC','changePIC')->name('posts.changePIC');
        Route::get('/category/{category:slug}', 'index')->name('posts.category');
    });


// Untuk komisi
Route::resource('commissions', CommissionController::class);
//List semua komisi
Route::get('/commissions', [CommissionController::class, 'index'])
     ->name('commissions.index');

//Tampilkan form untuk membuat komisi baru
Route::get('/commissions/create', [CommissionController::class, 'create'])
     ->name('commissions.create');

// Simpan komisi baru ke database
Route::post('/commissions', [CommissionController::class, 'store'])
     ->name('commissions.store');

// Tampilkan detail satu komisi berdasarkan {id}
Route::get('/commissions/{commission}', [CommissionController::class, 'show'])
     ->name('commissions.show');

// Tampilkan form untuk meng‐edit komisi yang sudah ada
Route::get('/commissions/{commission}/edit', [CommissionController::class, 'edit'])
     ->name('commissions.edit');

//Update data komisi berdasarkan {id}
Route::put('/commissions/{commission}', [CommissionController::class, 'update'])
     ->name('commissions.update');

// Hapus (delete) komisi berdasarkan {id}
Route::delete('/commissions/{commission}', [CommissionController::class, 'destroy'])
     ->name('commissions.destroy');


//untuk status komisi di mitra
Route::post('/commissions/{id}/disburse', [CommissionController::class, 'disburse'])
     ->name('commissions.disburse');


// Route untuk mengosongkan komisi (clear commission) pada child
Route::delete('posts/{child}/clear-commission', [PostController::class, 'clearCommission'])
     ->name('posts.clearCommission');


    //role: admin, marketing
    Route::prefix('posts/{post}')->name('posts.')->group(function(){
        // Rename a file by its array index
        Route::post('files/{index}/rename', [PostController::class, 'renameFile'])
            ->name('files.rename');

        // Delete a file by its array index
        Route::delete('files/{index}', [PostController::class, 'deleteFile'])
            ->name('files.destroy');
    }); 

    //role: admin, marketing
    Route::prefix('posts')->group(function(){
        // ... resource routes you already have

        // VIEW (inline display)
        Route::get('{post}/files/{index}', [PostController::class, 'viewFile'])
            ->name('posts.files.view');

        // DOWNLOAD
        Route::get('{post}/files/{index}/download', [PostController::class, 'downloadFile'])
            ->name('posts.files.download');
    });


    
    
    

//role: admin,marketing
    // Categories
    Route::controller(CategoryController::class)->prefix('categories')->group(function () {
        Route::get('/', 'index')->name('categories.index');
        Route::post('/{id}/add-data', 'addData')->name('addData');
    });

    // Users/Pegawai Marketing
    //role: admin
    Route::prefix('authors')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('authors.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories',       [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('updateUser');
    });

    // membuaka card pada schedule
    Route::get('/posts/{post}/transactions', [TransactionController::class, 'getTransactionsForPost']);

    
    //role: admin,marketing
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
    //role: admin,marketing
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');

    // Static Pages
    Route::get('/about', function () {
        return view('about', ['name' => 'San Deep', 'title' => 'About']);
    });

    Route::get('/contact', function () {
        return view('contact', ['title' => 'Contact']);
    });
});

// role:admin, marketing
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


