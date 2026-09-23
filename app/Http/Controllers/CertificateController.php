<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function create()
    {
        return view('pages.admin.sertifikat');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'certificate_number' => 'required|string|unique:certificates,certificate_number',
            'recipient_name'     => 'required|string|max:255',
            'recipient_identity' => 'required|string|max:255',
            'institution'        => 'nullable|string|max:255',
            'event_name'         => 'required|string|max:255',
            'role'               => 'required|string',
            'issue_date'         => 'required|date',
            'template'           => 'nullable|image|mimes:png,jpg,jpeg|max:4096',
        ]);

        $templatePath = null;
        if ($request->hasFile('template')) {
            $templatePath = $request->file('template')->store('templates', 'public');
        }

        $qrToken = Str::random(32);

        $certificate = Certificate::create([
            'certificate_number' => $validated['certificate_number'],
            'recipient_name'     => $validated['recipient_name'],
            'recipient_identity' => $validated['recipient_identity'],
            'institution'        => $validated['institution'],
            'event_name'         => $validated['event_name'],
            'role'               => $validated['role'],
            'issue_date'         => $validated['issue_date'],
            'template_path'      => $templatePath,
            'qr_token'           => $qrToken,
            'pos_name_x'         => 200,
            'pos_name_y'         => 150,
            'pos_number_x'       => 200,
            'pos_number_y'       => 100,
            'pos_qr_x'           => 50,
            'pos_qr_y'           => 250,
        ]);

        return redirect()->route('admin.certificate.editor', $certificate->id);
    }

    public function editor($id)
    {
        $certificate = Certificate::findOrFail($id);
        return view('pages.admin.editor', compact('certificate'));
    }

    public function updatePositions(Request $request, $id)
    {
        $request->validate([
            'pos_number_x' => 'required|integer',
            'pos_number_y' => 'required|integer',
            'pos_name_x'   => 'required|integer',
            'pos_name_y'   => 'required|integer',
            'pos_qr_x'     => 'required|integer',
            'pos_qr_y'     => 'required|integer',
        ]);

        $certificate = Certificate::findOrFail($id);
        $certificate->update([
            'pos_number_x' => $request->pos_number_x,
            'pos_number_y' => $request->pos_number_y,
            'pos_name_x'   => $request->pos_name_x,
            'pos_name_y'   => $request->pos_name_y,
            'pos_qr_x'     => $request->pos_qr_x,
            'pos_qr_y'     => $request->pos_qr_y,
        ]);

        // LANGSUNG REDIRECT KE DAFTAR / CEK SERTIFIKAT
        return redirect()->route('admin.certificate.show_all')
            ->with('success', 'Sertifikat berhasil dibuat dan posisi telah disimpan!');
    }

    // Menampilkan Tabel Cek Sertifikat (Semua Data + Search)
    public function showAll(Request $request)
    {
        $query = Certificate::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('recipient_name', 'like', "%{$search}%")
                  ->orWhere('recipient_identity', 'like', "%{$search}%")
                  ->orWhere('event_name', 'like', "%{$search}%")
                  ->orWhere('institution', 'like', "%{$search}%");
            });
        }

        $certificates = $query->latest()->paginate(10);

        return view('pages.admin.show', compact('certificates'));
    }

    // Download Sertifikat (Dummy / Gambar Template)
    public function download($id)
    {
        $certificate = Certificate::findOrFail($id);
        
        if ($certificate->template_path && Storage::disk('public')->exists($certificate->template_path)) {
            return Storage::disk('public')->download($certificate->template_path, 'Sertifikat_' . Str::slug($certificate->recipient_name) . '.png');
        }

        return back()->with('error', 'File sertifikat tidak ditemukan.');
    }

    // Hapus Sertifikat
    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);
        
        if ($certificate->template_path) {
            Storage::disk('public')->delete($certificate->template_path);
        }

        $certificate->delete();

        return redirect()->route('admin.certificate.show_all')
            ->with('success', 'Sertifikat berhasil dihapus.');
    }
}
