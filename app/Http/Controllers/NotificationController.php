<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Transaction;
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

        // 4) activity log untuk Post
        $postActivities = Activity::forSubject($selectedPost)
            ->when($q, fn($b) =>
                $b->where('description','like',"%{$q}%")
            )
            ->latest()
            ->get();

        // 5) ambil semua ID Transaction milik Post ini
        $txIds = $selectedPost->transactions()->pluck('id');

        // 6) activity log untuk Transaction
        $txActivities = Activity::query()
            ->where('subject_type', Transaction::class)
            ->whereIn('subject_id', $txIds)
            ->when($q, fn($b) =>
                $b->where('description','like',"%{$q}%")
            )
            ->latest()
            ->get();

        // 7) gabungkan dan urutkan descending
        $activities = $postActivities
            ->concat($txActivities)
            ->sortByDesc('created_at')
            ->values();

        // 8) render view
        return view('notifications.index', compact(
            'posts','selectedPost','messages','q','activities'
        ));
    }


     // untuk mitra
    public function mitraIndex(Request $request)
    {
        $user = Auth::user();

       
        $selectedPost = Post::where('pic_mitra', $user->name)->firstOrFail();

    
        $q = $request->get('q');
        $messages = $selectedPost->messages()
            ->with('sender')
            ->when($q, fn($b) =>
                $b->where('subject', 'like', "%{$q}%")
                ->orWhere('body',    'like', "%{$q}%")
            )
            ->latest()
            ->get();

        $postActivities = Activity::forSubject($selectedPost)
            ->when($q, fn($b) =>
                $b->where('description', 'like', "%{$q}%")
            )
            ->latest()
            ->get();


        $txIds = $selectedPost->transactions()->pluck('id');

      
        $txActivities = Activity::query()
            ->where('subject_type', Transaction::class)
            ->whereIn('subject_id', $txIds)
            ->when($q, fn($b) =>
                $b->where('description', 'like', "%{$q}%")
            )
            ->latest()
            ->get();

        $activities = $postActivities
            ->concat($txActivities)
            ->sortByDesc('created_at')
            ->values();

        return view('mitra.notifications', compact(
            'selectedPost', 'messages', 'q', 'activities'
        ));
    }


}
