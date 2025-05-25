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
        $companyTitle      = Post::where('pic_mitra', $user->name)
                                 ->value('title')
                             ?? 'Mitra';
        $post = Post::where('pic_mitra', $user->username)->first();
        return view('mitra.dashboardMitra', compact(
            'total',
            'newMessagesCount',
            'activeCount',
            'companyTitle',
            'post',
        ));
    }

    public function show(Post $post) 
    {
        $user = Auth::user();

            // Hanya mitra yang boleh melihat
            if ($user->role !== 'mitra') {
                abort(403, 'Unauthorized.');
            }

            return view('mitra.postMitra', compact('post'));
    }

    
}
