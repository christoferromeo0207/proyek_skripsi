<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MitraRegisterController extends Controller
{
    use RegistersUsers;

    // setelah register, redirect ke notice verifikasi
    protected $redirectTo = '/email/verify';

    public function __construct()
    {
        $this->middleware('guest');
    }

    // tampilkan form
    public function show()
    {
        return view('auth.register-mitra');
    }

    // handle POST
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $user->sendEmailVerificationNotification();

        // login langsung kemudian diarahkan ke notice verifikasi
        $this->guard()->login($user);

        return $this->registered($request, $user)
                    ?: redirect($this->redirectPath());
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
        'name'              => ['required','string','max:255'],
        'username'          => ['required','string','max:100','unique:users,username'],
        'email'             => ['required','string','email','max:255','unique:users,email'],
        'password'          => ['required','string','min:8','confirmed'],
        // kalau kamu ingin jabatan,dll. wajib: ganti required;  
        // kalau boleh kosong, gunakan nullable  
        'jabatan'           => ['required','string','max:100'],
        'tgl_lahir'         => ['required','date'],
        'tgl_masuk'         => ['required','date'],
        'tempat_lahir'      => ['required','string','max:100'],
        'no_telp'           => ['required','string','max:255'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name'         => $data['name'],
            'username'     => $data['username'],
            'email'        => $data['email'],
            'password'     => $data['password'],
            'jabatan'      => $data['jabatan']      ?? null,
            'tgl_lahir'    => $data['tgl_lahir']    ?? null,
            'tgl_masuk'    => $data['tgl_masuk']    ?? null,
            'tempat_lahir' => $data['tempat_lahir'] ?? null,
            'no_telp'      => $data['no_telp']      ?? null,
            'role'         => 'mitra',       
        ]);
    }
}
