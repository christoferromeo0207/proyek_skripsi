<?php
namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Message;
use App\Notifications\MitraSentMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;     


class MitraMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

   public function index(Post $post, Request $req)
    {
        abort_if($post->pic_mitra !== Auth::user()->name, 403);

        $q = $req->get('q');

        $msgs = $post->messages()
            ->with(['sender','receiver'])
            ->where(function($query) {
                $query->where('user_id', Auth::id())
                    ->orWhere('receiver_id', Auth::id());
            })
            ->with(['sender','receiver'])
            ->when($q, fn($b) => $b->where('subject','like', "%{$q}%"))
            ->latest()
            ->get();

        return view('mitra.messages.index', compact('post','msgs','q'));
    }

    public function create(Post $post)
    {
        abort_if($post->pic_mitra !== Auth::user()->name, 403);
        return view('mitra.messages.create', compact('post'));
    }

    public function store(Post $post, Request $req)
    {
        abort_if($post->pic_mitra !== Auth::user()->name, 403);

        $data = $req->validate([
            'subject'=>'required|string',
            'body'   =>'required|string',
            'attachments.*'=>'file|max:5120',
        ]);

        $files = [];
        foreach ($req->file('attachments',[]) as $f) {
            $files[] = $f->store('messages','public');
        }

        $msg = $post->messages()->create([
            
            'post_id'=>$post->id,
            'user_id'=>Auth::id(),
            'receiver_id'  => $post->picUser->id,
            'subject'=>$data['subject'],
            'body'   =>$data['body'],
            'attachments'=>$files,
            'is_read'=>false,
        ]);

        return redirect()
            ->route('mitra.informasi.messages.index',$post)
            ->with('success','Pesan berhasil dikirim.');
    }

    public function markRead(Post $post, Message $msg)
    {
        abort_if($post->pic_mitra !== Auth::user()->name, 403);
        $msg->update(['is_read'=>true]);
        return back();
    }

    public function renameAttachment(Request $req, Post $post, Message $msg, $fn)
    {
        abort_if($post->pic_mitra !== Auth::user()->name, 403);
        $req->validate(['new_name'=>'required|string']);
        $ext = pathinfo($fn, PATHINFO_EXTENSION);
        $new = time().'_'. Str::slug($req->new_name).'.'.$ext;
        Storage::disk('public')->move("messages/$fn","messages/$new");
        $atts = array_map(fn($a)=> $a===$fn?$new:$a, $msg->attachments);
        $msg->update(['attachments'=>$atts]);
        return back()->with('success','Lampiran di-rename.');
    }

    public function deleteAttachment(Post $post, Message $msg, $fn)
    {
        abort_if($post->pic_mitra !== Auth::user()->name, 403);
        Storage::disk('public')->delete("messages/$fn");
        $atts = array_filter($msg->attachments, fn($a)=> $a!==$fn);
        $msg->update(['attachments'=>array_values($atts)]);
        return back()->with('success','Lampiran dihapus.');
    }
}
