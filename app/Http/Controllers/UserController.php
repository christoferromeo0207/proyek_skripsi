<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
        {
            $query = User::query()->where('role', '!=', 'mitra');


            // Jika ada fungsi filter / pencarian:
            if ($request->filled('search')) {
                $query->where('name', 'like', '%'.$request->search.'%');
            }


            // untuk paginate
            $users = $query
                ->withCount('posts')  
                ->paginate(5);

            return view('user', compact('users'));
        }

    public function store(Request $request)
    {
        Log::info('Attempting to add user', $request->all());
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'nullable|string|min:6|confirmed',
            'jabatan'      => 'required|string|max:255',
            'tgl_lahir'    => 'required|date',
            'tgl_masuk'    => 'required|date',
            'tempat_lahir' => 'required|string|max:100',
            'no_telp'      => 'required|string|max:20',
        ]);

        try {
            // $data['password'] = bcrypt($data['password']);
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
    $data = $request->validate([
        'name'         => 'required|string|max:255',
        'username'     => 'required|string|max:255',
        'email'        => 'required|email|max:255',
        'password'     => 'nullable|string|min:6|confirmed',
        'jabatan'      => 'required|string|max:255',
        'tgl_lahir'    => 'required|date',
        'tgl_masuk'    => 'required|date',
        'tempat_lahir' => 'required|string|max:100',
        'no_telp'      => 'required|string|max:100',
    ]);

    $user = User::findOrFail($id);

    // Build the payload:
    $payload = Arr::only($data, [
        'name','username','email','jabatan',
        'tgl_lahir','tgl_masuk','tempat_lahir','no_telp',
    ]);

    // if (filled($data['password'])) {
    //     $payload['password'] = Hash::make($data['password']);
    // }

    $user->update($payload);

    return redirect()->route('user.index')
                    ->with('success','Data pengguna berhasil diperbarui');
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
