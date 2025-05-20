<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // 1) ambil semua mitra (posts) untuk dropdown
        $posts = Post::orderBy('title')->get();

        // 2) tentukan mitra terpilih: 
        //    param ?post=ID, jika tidak ada pakai yang pertama
        $postId = $request->get('post', optional($posts->first())->id);
        $selectedPost = Post::findOrFail($postId);

        // 3) ambil messages milik mitra itu, plus optional filter on subject/body
        $q = $request->get('q');
        $messages = $selectedPost->messages()
            ->with('sender') 
            ->when($q, fn($b) =>
                $b->where('subject','like',"%{$q}%")
                  ->orWhere('body','like',   "%{$q}%")
            )
            ->latest()
            ->get();

        // 4) render view
        return view('notifications.index', compact(
            'posts','selectedPost','messages','q'
        ));
    }
}
