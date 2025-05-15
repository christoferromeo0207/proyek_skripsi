<?php
namespace App\Http\Controllers;

use App\Models\Notulen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class NotulenController extends Controller
{


    public function index(Request $request)
    {
        // Ambil input pencarian dari permintaan
        $search = $request->input('search');
    
        // Ambil data notulen dari database
        $notulens = Notulen::when($search, function ($query) use ($search) {
            return $query->where('pertemuan', 'like', "%{$search}%");
        })
        ->orderBy('tanggal', 'desc') 
        ->paginate(7); 
    

        return view('notulen', [
            'title' => 'Notulen',
            'notulens' => $notulens,
            'search' => $search, 
        ]);
    }
    

    public function create()
    {
        $title = "Tambah Kegiatan"; // Atur judul untuk tampilan
        return view('create_notulen', compact('title'));
    }
    

    public function store(Request $request)
    {
    

        // Validasi data yang diterima dari form
        $validated = $request->validate([
            'pertemuan' => 'required|string',
            'tanggal' => 'required|date',
            'unit' => 'required|string',
            'jabatan' => 'required|string',
            'status' => 'required|string',
            'jenis' => 'required|string',
            'nama' => 'required|string',
            'no_hp' => 'required|string',
        ]);

        // Simpan data ke dalam database
        Notulen::create($validated);

        // Redirect kembali dengan pesan sukses
        return redirect()->route('notulen.index')->with('success', 'Data berhasil ditambahkan!');
    }
    
    

    public function show($id)
    {
        $notulen = Notulen::findOrFail($id);
        // Update the view path since the file is in the main views directory
        return view('view', compact('notulen'));
    }
    
    public function edit($id)
    {
        $notulen = Notulen::findOrFail($id);
        // Update the view path since the file is in the main views directory
        return view('edit', compact('notulen'));
    }

    

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required',
            'nama' => 'required',
            'unit' => 'required',
            'jabatan' => 'required',
            'no_hp' => 'required',
            'pertemuan' => 'required',
            'jenis' => 'required',
            'status' => 'required',
            'file' => 'nullable|file|mimes:doc,docx,xlsx,ppt,png,jpg,jpeg|max:2048',
        ]);
    
        $notulen = Notulen::findOrFail($id);
    
        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('uploads', 'public'); 
            $notulen->file_path = $filePath;
        }
    
        // Update other fields
        $notulen->tanggal = $request->tanggal;
        $notulen->nama = $request->nama;
        $notulen->unit = $request->unit;
        $notulen->jabatan = $request->jabatan;
        $notulen->no_hp = $request->no_hp;
        $notulen->pertemuan = $request->pertemuan;
        $notulen->jenis = $request->jenis;
        $notulen->status = $request->status;
    
        if ($request->filled('hasil')) {
            $notulen->hasil = $request->hasil;
        }
    
        $notulen->save();
    
        return redirect()->route('notulens.index')->with('success', 'Notulen updated successfully');
    }

    public function renameFile(Request $request, $id)
    {
        $notulen = Notulen::findOrFail($id);
        
        // Get the new name from the request
        $newName = $request->input('new_name');

        // Get the current file path
        $currentPath = $notulen->file_path;
        $currentPathParts = pathinfo($currentPath);
        
        // Create the new file path
        $newFilePath = $currentPathParts['dirname'] . '/' . $newName . '.' . $currentPathParts['extension'];

        // Rename the file in the storage
        Storage::move($currentPath, $newFilePath);

        // Update the database record
        $notulen->file_path = $newFilePath; 
        $notulen->save();

        return redirect()->back()->with('success', 'File renamed successfully!');
    }
    


    public function deleteFile($id)
    {
        $notulen = Notulen::findOrFail($id);

        // Menghapus file dari storage
        if ($notulen->file_path) {
            Storage::delete($notulen->file_path);

            // Menghapus path file dari database
            $notulen->file_path = null;
            $notulen->save();

            return redirect()->route('notulens.index')->with('success', 'File berhasil dihapus.');
        }

        return redirect()->route('notulens.index')->with('error', 'File tidak ditemukan.');
    }
    


    public function destroy(Notulen $notulen)
    {
        $notulen->delete();

        return redirect()->route('notulen.index')->with('success', 'Notulen berhasil dihapus.');
    }
}
