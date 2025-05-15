<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
        {
            $query = User::query();

            // Jika ada fungsi filter / pencarian:
            if ($request->filled('search')) {
                $query->where('name', 'like', '%'.$request->search.'%');
            }

            // Tambahkan withCount untuk men‐generate properti posts_count
            $users = $query
                ->withCount('posts')   // <-- ini yang bikin $user->posts_count
                ->paginate(10);

            return view('user', compact('users'));
        }

    public function store(Request $request)
    {
        Log::info('Attempting to add user', $request->all());
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:6|confirmed',
            'jabatan'      => 'required|string|max:255',
            'tgl_lahir'    => 'nullable|date',
            'tgl_masuk'    => 'nullable|date',
            'tempat_lahir' => 'nullable|string|max:100',
            'no_telp'      => 'nullable|string|max:20',
        ]);

        try {
            $data['password'] = bcrypt($data['password']);
            $user = User::create($data);

            Log::info('User created successfully', ['id' => $user->id]);

            return redirect()
                ->route('user.index')
                ->with('success', 'Pegawai berhasil ditambahkan');
        } catch (\Exception $e) {
            Log::error('Failed to add user', [
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('user.index')
                ->with('error', 'Gagal menambahkan pegawai: ' . $e->getMessage())
                ->withInput();
        }
    }


   public function update(Request $request, $id)
    {
        // Validasi data input
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|max:255',
            'jabatan' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'tgl_masuk' => 'required|date',
            'tempat_lahir' => 'required|string|max:100',
            'no_telp' => 'required|string|max:100',
        ]);

        // Cari user berdasarkan ID
        $user = User::findOrFail($id);
        
        // Update data di database
        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'jabatan' => $request->jabatan,
            'tgl_lahir' => $request->tgl_lahir,
            'tgl_masuk' => $request->tgl_masuk,
            'tempat_lahir' => $request->tempat_lahir,
            'no_telp' => $request->no_telp,
            
        ]);


        return redirect()->route('user.index')->with('success', 'Data pengguna berhasil diperbarui');
    }

    public function edit(User $user)
    {
        return view('editMarketing', compact('user'));
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()
            ->route('user.index')
            ->with('success', 'Pegawai berhasil dihapus');
    }
}
