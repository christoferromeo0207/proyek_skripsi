<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;


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

}
