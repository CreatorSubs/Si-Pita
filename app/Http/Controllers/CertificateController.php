<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateController extends Controller
{
    public function search(Request $request) {
        $nama = $request->input('nama');
        $identitas = $request->input('nomor_identitas');
        $certificate = Certificate::where('recipient_name', 'LIKE', "%{$nama}%")
            ->where('recipient_identity', 'LIKE', "%{$identitas}%")->first();
        if (!$certificate) return response()->view('errors.404', [], 404);
        return redirect()->route('certificate.download', $certificate->id);
    }

    public function downloadPage($id) {
        $certificate = Certificate::findOrFail($id);
        return view('pages.download', compact('certificate'));
    }

    public function generatePdf($id) {
        $certificate = Certificate::findOrFail($id);
        $qrCode = base64_encode(QrCode::format('svg')->size(80)->generate(route('certificate.download', $certificate->id)));
        $pdf = Pdf::loadView('pages.pdf_template', compact('certificate', 'qrCode'))->setPaper('a4', 'landscape');
        return $pdf->stream('Sertifikat-' . Str::slug($certificate->recipient_name) . '.pdf');
    }

    public function index() {
        $certificates = Certificate::latest()->paginate(10);
        return view('pages.admin.index', compact('certificates'));
    }

    public function create() { return view('pages.admin.create'); }

    public function store(Request $request) {
        $request->validate([
            'certificate_number' => 'required|unique:certificates',
            'recipient_name'     => 'required',
            'recipient_identity' => 'required',
            'event_name'         => 'required',
            'issue_date'         => 'required|date',
            'template'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $templatePath = $request->hasFile('template') ? $request->file('template')->store('templates', 'public') : null;

        $certificate = Certificate::create([
            'certificate_number' => $request->certificate_number,
            'recipient_name'     => $request->recipient_name,
            'recipient_identity' => $request->recipient_identity,
            'institution'        => $request->institution ?? 'Diskominfo',
            'event_name'         => $request->event_name,
            'role'               => $request->role ?? 'Peserta',
            'issue_date'         => $request->issue_date,
            'template_path'      => $templatePath,
            'qr_token'           => Str::random(16),
        ]);

        return redirect()->route('admin.certificate.editor', $certificate->id);
    }

    public function editor($id) {
        $certificate = Certificate::findOrFail($id);
        return view('pages.admin.editor', compact('certificate'));
    }

    public function updatePositions(Request $request, $id) {
        $certificate = Certificate::findOrFail($id);
        $certificate->update([
            'pos_name_x' => $request->pos_name_x, 'pos_name_y' => $request->pos_name_y,
            'pos_number_x' => $request->pos_number_x, 'pos_number_y' => $request->pos_number_y,
            'pos_qr_x' => $request->pos_qr_x, 'pos_qr_y' => $request->pos_qr_y,
        ]);
        return response()->json(['status' => 'success']);
    }
}
