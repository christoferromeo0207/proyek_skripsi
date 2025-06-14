<?php
// app/Http/Controllers/MarketingDashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Post;
use App\Models\Transaction;

class MarketingDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

   public function index()
    {
        // Pastikan marketing
        if (Auth::user()->role !== 'marketing') {
            abort(403);
        }

        $myId   = Auth::id();
        $myName = Auth::user()->name; 

        $mitraCount = Post::where('PIC', $myId)
                        ->count('PIC');


        $kategoriCount = Category::count();


        $mitraStatusCount = Transaction::whereColumn('approval_rs', '!=', 'approval_mitra')->count();


        $stats = [
            [
                'Total Mitra',
                $mitraCount,
                'Mitra di bawah PIC Anda',
                'fas fa-users',
                route('posts.pic')
            ],
            [
                'Kategori',
                $kategoriCount,
                'Jumlah kategori perusahaan',
                'fas fa-list',
                route('categories.index')
            ],
            [
                'Proses Transaksi Mitra',
                $mitraStatusCount,
                'Transaksi yang masih proses',
                'fas fa-hourglass-half',
                route('posts.pic')
            ],
        ];

        return view('dashboardMarketing', compact('stats'));
    }


      public function postsPIC()
    {
        $userId     = Auth::id();
        $categories = Category::all();

        // Ambil semua post di bawah marketing ini
        $myPosts = Post::where('PIC', $userId)
                       ->orderBy('created_at', 'desc')
                       ->paginate(9, ['*'], 'myPage');

        // Ambil semua post selain yang di bawah marketing ini
        $otherPosts = Post::where('PIC', '<>', $userId)
                          ->orderBy('created_at', 'desc')
                          ->paginate(9, ['*'], 'otherPage');

        return view('postsPIC', compact('myPosts', 'otherPosts', 'categories'));
    }


}
