<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Message;
use App\Models\User;
use App\Models\Category;
use App\Models\Commission;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Support\Str; 
use Spatie\Activitylog\Models\Activity;

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
            abort(403);
        }

        // grab exactly *one* Post for this mitra (or fail if none)
        $post = Post::withCount('transactions')
                    ->where('pic_mitra', $user->name)
                    ->firstOrFail();

        if (! $post) {
            abort(404, 'PIC Mitra belum terdaftar');
        }

        $total         = $post->transactions_count;
        $messageCount  = Message::where('post_id', $post->id)->count();
        $today         = Carbon::today();
        $activeCount   = Post::where('pic_mitra', $user->name)
                            ->whereDate('tanggal_awal','<=',$today)
                            ->whereDate('tanggal_akhir','>=',$today)
                            ->count();
        $companyTitle  = $post->title;

        return view('mitra.dashboardMitra', compact(
            'post',
            'total',
            'messageCount',
            'activeCount',
            'companyTitle'
        ));
    }


    public function show(Post $post)
    {
        $user = Auth::user();
        if ($user->role !== 'mitra') {
            abort(403, 'Unauthorized.');
        }
        if ($post->pic_mitra !== $user->name) {
            abort(403, 'Unauthorized.');
        }

        $companyTitle = $post->title;
        // Muat relasi dasar
        $post->load(['category', 'transactions', 'picUser']);

        // Ambil semua komisi di mana post ini adalah parent (mitra)
        $commissions = Commission::with(['child', 'transaction'])
                        ->where('parent_post_id', $post->id)
                        ->orderByDesc('created_at')
                        ->get();

        return view('mitra.postMitra', [
            'post'         => $post,
            'companyTitle' => $companyTitle,
            'commissions'  => $commissions,
        ]);
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
        if ($user->role !== 'mitra' || $post->pic_mitra !== $user->name) {
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

        return redirect()->route('mitra.informasi.show', $post)
                        ->with('success', 'Detail mitra berhasil diperbarui.');
    }

    public function showTransaction(Post $post, Transaction $transaction)
    {
        $user = Auth::user();
        if ($user->role !== 'mitra' || $post->pic_mitra !== $user->name) {
            abort(403);
        }

        // Ambil daftar PIC RS untuk dropdown
        $users = User::where('role', 'rs')
                     ->orderBy('name')
                     ->get();

        return view('mitra.detailTransactionMitra', compact('post','transaction','users'));
    }

     public function updateTransaction(Request $request, Post $post, Transaction $transaction)
    {
        // 1) hanya Mitra yang boleh
        $user = Auth::user();
        abort_if($user->role !== 'mitra' || $post->pic_mitra !== $user->name, 403);

        // 2) validasi hanya PIC Mitra, Approval Mitra, dan file actions
        $data = $request->validate([
            'pic_mitra'          => 'required|string|max:255',
            'approval_mitra'     => 'required|boolean',
            'bukti_pembayaran.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:2048',
            'action_type'        => 'sometimes|in:rename,delete',
            'file_index'         => 'sometimes|integer|min:0',
            'new_name'           => 'sometimes|string|max:255',
        ]);

        // 3) tangani rename/delete seperti sebelumnya…
        if ($request->filled('action_type')) {
            $files = $transaction->bukti_pembayaran_json;
            $idx   = $data['file_index'];

            if ($data['action_type'] === 'delete' && isset($files[$idx])) {
                Storage::disk('public')->delete($files[$idx]);
                array_splice($files, $idx, 1);
            }

            if ($data['action_type'] === 'rename' && isset($files[$idx])) {
                $old = $files[$idx];
                $ext = pathinfo($old, PATHINFO_EXTENSION);
                $new = Str::slug($data['new_name']) . '.' . $ext;
                $newPath = dirname($old).'/'.$new;
                Storage::disk('public')->move($old, $newPath);
                $files[$idx] = $newPath;
            }

            $transaction->bukti_pembayaran_json = $files;
            $transaction->save();
            return back()->with('success','File berhasil diperbarui.');
        }

        // 4) update PIC Mitra & Approval Mitra
        $transaction->pic_mitra      = $data['pic_mitra'];
        $transaction->approval_mitra = $data['approval_mitra'];

        // 5) recompute status
        if ($transaction->approval_rs && $transaction->approval_mitra) {
            $transaction->status = 'Selesai';
        } elseif (! $transaction->approval_rs && ! $transaction->approval_mitra) {
            $transaction->status = 'Dibatalkan';
        } else {
            $transaction->status = 'Proses';
        }

        // 6) simpan upload baru
        if ($request->hasFile('bukti_pembayaran')) {
            $newFiles = [];
            foreach ($request->file('bukti_pembayaran') as $f) {
                $newFiles[] = $f->store("transactions/{$transaction->id}", 'public');
            }
            $transaction->bukti_pembayaran = array_merge(
                $transaction->bukti_pembayaran ?? [],
                $newFiles
            );

        }

        $transaction->save();

        // 7) kembali ke postMitra
        return redirect()
            ->route('mitra.informasi.show', $post)
            ->with('success','Transaksi berhasil diperbarui.');
    }

    public function notifications(Request $request, Post $post)
    {
        $user = Auth::user();
        abort_if($user->role !== 'mitra', 403);

        // 1) Semua Post yang ditugaskan ke Mitra ini
        $posts = Post::where('pic_mitra', $user->username)->get();

        // If no posts, just render the view with empty data
        if ($posts->isEmpty()) {
            return view('mitra.notifications', [
                'posts'        => $posts,
                'selectedPost' => null,
                'q'            => $request->get('q', ''),
                'messages'     => collect(),
                'activities'   => collect(),
            ]);
        }

        // 2) Pilih satu Post (default yang pertama)
        $selectedId   = $request->get('post', $posts->first()->id);
        $selectedPost = $posts->firstWhere('id', $selectedId) ?? $posts->first();

        // 3) Pesan untuk Mitra
        $q = $request->get('q');
        $messages = $selectedPost->messages()
            ->with('sender')
            ->when($q, fn($qb) =>
                $qb->where('subject','like', "%{$q}%")
                ->orWhere('body','like',    "%{$q}%")
            )
            ->latest()
            ->get();

        // 4) Activity Log untuk Post ini
        $activities = Activity::forSubject($selectedPost)
                            ->latest()
                            ->get();

        return view('mitra.notifications', compact(
            'posts','selectedPost','q','messages','activities'
        ));
    }

    public function createPartner()
    {
        // 1) Ambil kategori untuk dropdown
        $categories = Category::orderBy('name')->get();

        // 2) Ambil semua user dengan role = marketing
        $marketingUsers = User::where('role','marketing')
                              ->orderBy('name')
                              ->get();

        // 3) Kirim ke view
        return view('mitra.partners.create', compact('categories','marketingUsers'));
    }

    public function storePartner(Request $request)
    {
        $user = Auth::user();
        // Validasi input
        $data = $request->validate([
            'title'           => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,id',
            'body'            => 'required|string',
            'phone'           => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:255',
            'alamat'          => 'nullable|string|max:255',
            'keterangan_bpjs' => 'required|in:yes,no',
            'pembayaran'      => 'required|string|max:100',
            'PIC'             => 'required|exists:users,id',
            'tanggal_awal'    => 'required|date',
            'tanggal_akhir'   => 'required|date|after_or_equal:tanggal_awal',
        ]);

        $data['PIC']        = $data['PIC'];
        $data['slug']       = Str::slug($data['title']);
        $data['pic_mitra']  = $user->name;

        Post::create($data);

        return redirect()
            ->route('mitra.dashboard')
            ->with('success','Pengajuan Mitra baru berhasil dikirim.');
    }


 
}
