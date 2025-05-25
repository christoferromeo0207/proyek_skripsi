<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Message;
use App\Models\User;
use App\Models\Category;
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
        if ($user->role !== 'mitra') {
            abort(403, 'Unauthorized.');
        }

        // Judul mitra sama dengan title dari Post yang di–show
        $companyTitle = $post->title;

        return view('mitra.postMitra', compact('post', 'companyTitle'));
    }

    public function edit(Post $post)
    {
        $categories = Category::orderBy('name')->get();
        $users      = User::orderBy('name')->get();
        // Pilihan induk kecuali dirinya sendiri
        $parents    = Post::whereNull('parent_id')
                          ->where('id', '!=', $post->id)
                          ->get();

        return view('mitra.editMitra', compact('post', 'categories', 'users', 'parents'));
    }

    public function update(Request $request, Post $post)
    {
        $user = Auth::user();
        if ($user->role !== 'mitra' || $post->pic_mitra !== $user->username) {
            abort(403, 'Unauthorized.');
        }

        // Validasi input
        $data = $request->validate([
            'title'            => 'required|string|max:255',
            'category_id'      => 'required|exists:categories,id',
            'body'             => 'required|string',
            'email'            => 'nullable|email',
            'phone'            => 'nullable|string',
            'alamat'           => 'nullable|string',
            'keterangan_bpjs'  => 'required|in:yes,no',
            'pembayaran'       => 'required|string',
            'tanggal_awal'     => 'required|date',
            'tanggal_akhir'    => 'required|date|after_or_equal:tanggal_awal',
            'picUser_id'       => 'nullable|exists:users,id',
            'pic_mitra'        => 'nullable|string',
            'file_path.*'      => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:2048',
            'is_child'         => 'nullable|boolean',
        ]);

        // Update fields dasar
        $post->update($data);

        // Handle upload file (jika ada)
        if ($request->hasFile('file_path')) {
            $paths = [];
            foreach ($request->file('file_path') as $file) {
                $paths[] = $file->store('mitra_docs', 'public');
            }
            // simpan array path ke JSON
            $post->file_path = json_encode($paths);
            $post->save();
        }

        return redirect()->route('mitra.informasi.edit', $post)
                        ->with('success', 'Detail mitra berhasil diperbarui.');
    }



    
}
