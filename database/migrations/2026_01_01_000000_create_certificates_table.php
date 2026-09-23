<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use Illuminate\Support\Str;

class CertificateController extends Controller
{
    public function create()
    {
        return view('pages.admin.create_certificate');
    }

    public function store(Request $request)
    {
        // 1. Handle Upload Template (jika ada)
        $templatePath = null;
        if ($request->hasFile('template')) {
            $templatePath = $request->file('template')->store('certificates/templates', 'public');
        }

        // Ambil prefix & event name dari form dasar
        $prefix = $request->certificate_number_prefix ?? 'SERT/';
        $eventName = $request->event_name ?? 'Kegiatan Tanpa Nama';
        $issueDate = $request->issue_date ?? date('Y-m-d');

        // CASE 1: JIKA ADA DATA PESERTA DARI MODAL GRID / CSV
        if ($request->has('participants') && is_array($request->participants) && count($request->participants) > 0) {
            foreach ($request->participants as $index => $p) {
                // Pastikan nama tidak kosong
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
                        'qr_token'           => Str::uuid()->toString(), // Memenuhi constraint unique qr_token
                    ]);
                }
            }
        } 
        // CASE 2: JIKA INPUT SATUAN / DARI FORM BIASA
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
                'qr_token'           => Str::uuid()->toString(), // Memenuhi constraint unique qr_token
            ]);
        }

        return redirect()->route('admin.certificate.create')->with('success', 'Sertifikat berhasil dibuat!');
    }

    public function editor($id = 1)
    {
        $certificate = Certificate::find($id);
        return view('pages.admin.editor_positions', compact('certificate'));
    }
}