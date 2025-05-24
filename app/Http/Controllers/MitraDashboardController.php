<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Message;
use App\Models\Transaction;
use Carbon\Carbon;

class MitraDashboardController extends Controller
{
    public function __construct()
    {
        // semua route butuh login
        $this->middleware('auth');
    }

    /**
     * Dashboard utama: 3 kartu statistik.
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->role !== 'mitra') {
            abort(403, 'Unauthorized.');
        }

        $today = Carbon::today();

        $total             = Post::where('pic_mitra', $user->username)->count();
        $newMessagesCount  = Message::where('user_id', $user->id)
                                    ->where('is_read', 0)
                                    ->count();
        $activeCount       = Post::where('pic_mitra', $user->username)
                                 ->whereDate('tanggal_awal', '<=', $today)
                                 ->whereDate('tanggal_akhir', '>=', $today)
                                 ->count();
        $companyTitle      = Post::where('pic_mitra', $user->username)
                                 ->value('title')
                             ?? 'Mitra';

        return view('mitra.dashboardMitra', compact(
            'total',
            'newMessagesCount',
            'activeCount',
            'companyTitle'
        ));
    }

    /**
     * Halaman “Informasi Mitra” detail, termasuk form edit & daftar transaksi.
     */
    public function information()
    {
        $user = Auth::user();
        if ($user->role !== 'mitra') {
            abort(403, 'Unauthorized.');
        }

        // “post” perusahaan milik mitra ini
        $post = Post::where('pic_mitra', $user->username)
                    ->firstOrFail();

        // semua nama PIC Mitra yang pernah muncul (untuk dropdown)
        $allMitra = Post::pluck('pic_mitra')->unique()->values();

        // semua transaksi yang post_id-nya sama dengan $post->id
        $transactions = Transaction::where('post_id', $post->id)
                                   ->get();

        return view('mitra.informationMitra', compact(
            'post',
            'allMitra',
            'transactions'
        ));
    }

    /**
     * Terima update terbatas dari mitra untuk deskripsi, phone, email, alamat, pic_mitra.
     */
    public function update(Request $request, Post $post)
    {
        $user = Auth::user();
        if ($user->role !== 'mitra' || $post->pic_mitra !== $user->username) {
            abort(403, 'Unauthorized.');
        }

        $data = $request->validate([
            'body'      => 'required|string',
            'phone'     => 'required|string',
            'email'     => 'required|email',
            'alamat'    => 'required|string',
            'pic_mitra' => 'required|string',
        ]);

        $post->update($data);

        return redirect()
            ->route('mitra.informationMitra')
            ->with('success', 'Informasi berhasil diubah.');
    }
}
