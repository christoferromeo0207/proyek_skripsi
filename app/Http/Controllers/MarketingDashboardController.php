<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MarketingDashboardController extends Controller
{
    public function __construct()
    {
        // pastikan user sudah login
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // hanya untuk mitra
        if ($user->role !== 'marketing') {
            abort(403, 'Unauthorized.');
        }

        $transactions = Transaction::where('pic_rs', $user->username)
                                   ->orderBy('created_at', 'desc')
                                   ->get();

        // Statistik
        $total           = $transactions->count();
        $newMessagesCount = 0; // jika belum ada tabel pesan
        // Anggap aktif = status 'proses'
        $activeCount     = $transactions->where('status', 'proses')->count();
        Log::info("MEMEMEK");
        return view('dashboardMarketing', compact(
            'transactions',
            'total',
            'newMessagesCount',
            'activeCount'
        ));
    }
}
