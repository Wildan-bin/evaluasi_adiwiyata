<?php

namespace App\Http\Controllers\Example;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        // Validasi minimal — *backend tetap sumber kebenaran*
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:5120', // max 5MB
        ]);

        // Menyimpan file sementara (belum catat ke DB)
        $path = $request->file('file')->store('uploads');

        return back()->with('success', 'File uploaded: ' . $path);
    }
}
