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
        if (Auth::user()->role !== 'marketing') {
            abort(403);
        }

        $myId = Auth::id();

        $mitraCount       = Post::where('PIC', $myId)
                                ->count('PIC');

        $kategoriCount    = Category::count();

        $mitraStatusCount = Transaction::where('pic_rs', $myId)
            ->where(function($q) {
                $q->where(function($q1) {
                        $q1->where('approval_rs', 0)
                        ->where('approval_mitra', 1);
                    })
                ->orWhere(function($q2) {
                        $q2->where('approval_rs', 1)
                        ->where('approval_mitra', 0);
                    });
            })
            ->distinct()
            ->count('pic_mitra');


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
                'Proses Mitra',
                $mitraStatusCount,
                'Mitra yang masih proses',
                'fas fa-hourglass-half',
                route('posts.pic') 
            ],
        ];

        return view('dashboardMarketing', compact('stats'));
    }

     public function postsPIC()
    {
        $userId = Auth::id();
        $categories = Category::all();

        $myPosts = Post::where('PIC', $userId)
                ->orderBy('created_at','desc')
                ->paginate(9, ['*'], 'myPage');

        $otherPosts = Post::where('PIC','<>',$userId)
                        ->orderBy('created_at','desc')
                        ->paginate(9, ['*'], 'otherPage');

        return view('postsPIC', compact('myPosts','otherPosts','categories'));
    }


}
