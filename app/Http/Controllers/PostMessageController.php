<?php

namespace App\Http\Controllers;

use App\Mail\NewMessageMail;
use App\Models\Post;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class PostMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request, Post $post)
    {
        $q = $request->get('q');
        $msgs = $post->messages()
            ->with('sender')
            ->when($q, fn($qb) => $qb->where('subject','like',"%{$q}%")
            ->orWhere('body','like',"%{$q}%"))
            ->latest()
            ->get();

        return view('messages.index', compact('post','msgs','q'));
    }


    public function create(Post $post)
    {
        return view('messages.create', compact('post'));
    }


    public function store(Request $request, Post $post)
    {
        $data = $request->validate([
            'subject'     => 'required|string|max:255',
            'body'        => 'required|string',
            'attachments' => 'sometimes|array',
            'attachments.*' => 'file|max:5120', 
        ]);


        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('messages', 'public');
                $attachments[] = basename($path);
            }
        }


        $message = $post->messages()->create([
            'user_id' => Auth::id(),
            'subject' => $data['subject'],
            'body'    => $data['body'],
        ]);

        $mailData = [
            'sender'      => Auth::user(),
            'subject'     => $data['subject'],
            'bodyText'        =>$data['body'],
            'message'     => $message,
            'attachments' => $attachments,
            'post'        => $post,
        ];

        Mail::mailer('smtp')
            ->to($post->email)
            ->send(new NewMessageMail($mailData));

        $mailable = (new \App\Mail\NewMessageMail($mailData))
            ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));


        Mail::mailer('sendgrid')
            ->to($post->email)
            ->send(new NewMessageMail($mailData));

        return redirect()
            ->route('posts.messages.index', $post)
            ->with('success', 'Pesan tersimpan dan terkirim (Mailtrap & SendGrid)!');
    }


    public function show(Post $post, Message $message)
    {
        return view('messages.show', compact('post','message'));
    }


    public function markRead(Post $post, Message $message)
    {
        $message->update(['is_read' => true]);
        return back();
    }

}
