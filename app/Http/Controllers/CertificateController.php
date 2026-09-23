<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function create()
    {
        return view('pages.admin.create_certificate');
    }

    public function store(Request $request)
    {
        // Handle upload template
        $templatePath = null;
        if ($request->hasFile('template')) {
            $templatePath = $request->file('template')->store('certificates/templates', 'public');
        }

        $prefix = $request->certificate_number_prefix ?? 'SERT/';
        $eventName = $request->event_name ?? 'Kegiatan';
        $issueDate = $request->issue_date ?? date('Y-m-d');

        // CASE 1: JIKA INPUT BANYAK DATA (VIA MODAL GRID / CSV)
        if ($request->has('participants') && is_array($request->participants) && count($request->participants) > 0) {
            foreach ($request->participants as $index => $p) {
                if (!empty($p['name'])) {
                    Certificate::create([
                        'certificate_number' => $prefix . strtoupper(Str::random(5)) . '-' . ($index + 1),
                        'recipient_name'     => $p['name'],
                        'recipient_identity' => !empty($p['identity_number']) ? $p['identity_number'] : '-',
                        'institution'        => $p['agency'] ?? 'Diskominfo',
                        'event_name'         => $eventName,
                        'role'               => $p['role'] ?? 'Peserta',
                        'issue_date'         => $issueDate,
                        'template_path'      => $templatePath,
                        'qr_token'           => Str::uuid()->toString(),
                    ]);
                }
            }
        } 
        // CASE 2: JIKA INPUT SATUAN
        else {
            Certificate::create([
                'certificate_number' => $prefix . strtoupper(Str::random(6)),
                'recipient_name'     => $request->recipient_name ?? 'Peserta',
                'recipient_identity' => $request->recipient_identity ?? '-',
                'institution'        => $request->institution ?? 'Diskominfo',
                'event_name'         => $eventName,
                'role'               => $request->role ?? 'Peserta',
                'issue_date'         => $issueDate,
                'template_path'      => $templatePath,
                'qr_token'           => Str::uuid()->toString(),
            ]);
        }

        // REDIRECT LANGSUNG KE HALAMAN CEK SERTIFIKAT
        return redirect()->route('admin.certificate.check')->with('success', 'Sertifikat berhasil dibuat!');
    }

    public function editor($id = 1)
    {
        $certificate = Certificate::find($id);
        return view('pages.admin.editor_positions', compact('certificate'));
    }

    public function showAll()
    {
        $certificates = Certificate::latest()->paginate(10);
        return view('pages.admin.check_certificate', compact('certificates'));
    }

    // METHOD HAPUS SERTIFIKAT
    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);
        
        // Hapus file template jika ada
        if ($certificate->template_path && Storage::disk('public')->exists($certificate->template_path)) {
            Storage::disk('public')->delete($certificate->template_path);
        }

        $certificate->delete();

        return redirect()->route('admin.certificate.check')->with('success', 'Sertifikat berhasil dihapus!');
    }
}
