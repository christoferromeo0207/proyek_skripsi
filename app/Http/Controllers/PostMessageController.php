<?php

namespace App\Http\Controllers;

use App\Mail\NewMessageMail;
use App\Models\Post;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;  

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
            ->with(['sender', 'receiver'])
            ->when($q, fn($qb) => $qb->where('subject','like',"%{$q}%")
            ->orWhere('body','like',"%{$q}%"))
            ->latest()
            ->get();

        return view('messages.index', compact('post','msgs','q'));
    }


    public function create(Post $post)
    {
        // grab the PIC user directly
        $receiver = $post->picUser;

        // optionally guard if it’s null
        if (! $receiver) {
        abort(404, 'This company has no PIC assigned.');
        }

        return view('messages.create', compact('post','receiver'));
    }



    public function store(Request $request, Post $post)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'receiver_id' => 'required|exists:users,id',
            'attachments' => 'sometimes|array',
            'attachments.*' => 'file|max:5120', 
        ]);

        $receiver = User::findOrFail($data['receiver_id']);


        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('messages', 'public');
                $attachments[] = basename($path);
            }
        }


        $message = $post->messages()->create([
            'user_id' => Auth::id(),
            'receiver_id' => $receiver->id,
            'subject' => $data['subject'],
            'body'    => $data['body'],
            'attachments' => $attachments,
            'is_read' => false,
        ]);

        $mailData = [
            'sender'      => Auth::user(),
            'receiver'    => Auth::user(),  
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
            ->with('success', 'Pesan tersimpan dan terkirim (Mailtrap)!');
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

    public function renameAttachment(Request $request, Post $post, Message $message, string $filename)
    {
        $request->validate([
            'new_name' => 'required|string|max:255',
        ]);

        // Build new filename
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $base = Str::slug($request->input('new_name'));
        $newFilename = time() . '_' . $base . '.' . $ext;

        // Move file on disk
        Storage::disk('public')->move("messages/{$filename}", "messages/{$newFilename}");

        // Update JSON array in DB
        $attachments = collect($message->attachments)
            ->map(fn($att) => $att === $filename ? $newFilename : $att)
            ->toArray();

        $message->update(['attachments' => $attachments]);

        return back()->with('success', 'Nama lampiran berhasil diperbarui.');
    }

    /**
     * Delete a single attachment from disk and JSON array.
     */
    public function deleteAttachment(Post $post, Message $message, string $filename)
    {
        // Delete from storage
        Storage::disk('public')->delete("messages/{$filename}");

        // Remove from JSON array
        $attachments = array_values(
            array_filter(
                $message->attachments,
                fn($att) => $att !== $filename
            )
        );

        $message->update(['attachments' => $attachments]);

        return back()->with('success', 'Lampiran berhasil dihapus.');
    }



}
