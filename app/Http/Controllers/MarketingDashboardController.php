<?php
// app/Http/Controllers/MarketingDashboardController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Transaction;

class MarketingDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // hanya untuk marketing
        if (Auth::user()->role !== 'marketing') {
            abort(403);
        }

        $myId = Auth::id();

        // 1) jumlah mitra di bawah PIC ini
        $mitraCount = Transaction::where('pic_rs', $myId)
                        ->distinct()
                        ->count('pic_mitra');

        // 2) kategori perusahaan (sesuaikan model Anda)
        $kategoriCount = Category::count();

        // 3) berapa banyak dari mitra-mitra itu masih “proses”
        $mitraProsesCount = Transaction::where('pic_rs', $myId)
            ->where(fn($q) => $q
                ->where('approval_rs', 0)
                ->orWhere('approval_mitra', 0)
            )
            ->distinct()
            ->count('pic_mitra');

        return view('dashboardMarketing', compact(
            'mitraCount',
            'kategoriCount',
            'mitraProsesCount'
        ));
    }
}
