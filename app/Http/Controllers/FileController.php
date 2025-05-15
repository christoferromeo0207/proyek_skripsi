<?php
namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Notulen;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'notulen_id' => 'required|exists:notulen,id', // Pastikan notulen_id ada di tabel notulen
            'file' => 'required|file|mimes:png,jpg,jpeg', // Contoh validasi untuk file
        ]);

        // Ambil notulen_id dari request
        $notulenId = $request->input('notulen_id');

        // Logika upload file di sini
        // Misalnya, simpan file ke direktori uploads
        $path = $request->file('file')->store('uploads');

        // Menyimpan file ke tabel files
        $file = File::create([
            'notulen_id' => $notulenId,
            'file_path' => $path,
            'file_type' => $request->file('file')->getClientOriginalExtension(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'File uploaded successfully', 'file' => $file]);
    }
}
