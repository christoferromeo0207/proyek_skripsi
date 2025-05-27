<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;




class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        
        // 1) semua mitra untuk dropdown
        $posts = Post::orderBy('title')->get();

        // 2) mitra terpilih (param ?post=ID)
        $postId = $request->get('post', optional($posts->first())->id);
        $selectedPost = Post::findOrFail($postId);

        // 3) pesan untuk mitra ini (filter optional)
        $q = $request->get('q');
        $messages = $selectedPost->messages()
            ->with('sender')
            ->when($q, fn($b) =>
                $b->where('subject','like',"%{$q}%")
                  ->orWhere('body','like',   "%{$q}%")
            )
            ->latest()
            ->get();

        // 4) activity log untuk mitra ini
        $activities = Activity::query()
            ->where('subject_type', Post::class)
            ->where('subject_id',   $selectedPost->id)
            ->latest()
            ->get();
// dd($posts, $messages,$selectedPost, $activities);
        // 5) render view
        return view('notifications.index', compact(
            'posts','selectedPost','messages','q','activities'
        ));
    }


     // untuk mitra: langsung pakai Post milik user yang login
    public function mitraIndex(Request $request)
    {
        $user = Auth::user();

        // 1) cari Post milik user (asumsi ada kolom user_id di tabel posts)
        $selectedPost = Post::where('pic_mitra', $user->name)->firstOrFail();

        // 2) pesan + filter sama seperti index
        $q = $request->get('q');
        $messages = $selectedPost->messages()
            ->with('sender')
            ->when($q, fn($b) =>
                $b->where('subject','like',"%{$q}%")
                  ->orWhere('body','like',   "%{$q}%")
            )
            ->latest()
            ->get();

        // 3) activity log milik Post ini
        $activities = Activity::forSubject($selectedPost)->latest()->get();

        return view('mitra.notifications', compact(
            'selectedPost','messages','q','activities'
        ));
    }

}
