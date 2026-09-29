<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $request->validate([
            'certificate_number_prefix' => ['nullable', 'string', 'max:100'],
            'certificate_number' => ['nullable', 'string', 'max:100'],
            'participants' => ['sometimes', 'array'],
            'participants.*.name' => ['nullable', 'string', 'max:255'],
            'participants.*.identity_number' => ['nullable', 'string', 'max:255'],
            'pos_number_x' => ['nullable', 'integer', 'between:0,800'],
            'pos_number_y' => ['nullable', 'integer', 'between:0,565'],
            'pos_name_x' => ['nullable', 'integer', 'between:0,800'],
            'pos_name_y' => ['nullable', 'integer', 'between:0,565'],
            'pos_event_x' => ['nullable', 'integer', 'between:0,800'],
            'pos_event_y' => ['nullable', 'integer', 'between:0,565'],
            'pos_qr_x' => ['nullable', 'integer', 'between:0,800'],
            'pos_qr_y' => ['nullable', 'integer', 'between:0,565'],
        ]);

        $prefix = trim((string) ($request->input('certificate_number_prefix')
            ?? $request->input('certificate_number')
            ?? 'SERT/'));
        $positions = array_merge([
            'pos_number_x' => 200,
            'pos_number_y' => 40,
            'pos_name_x' => 250,
            'pos_name_y' => 220,
            'pos_event_x' => 250,
            'pos_event_y' => 258,
            'pos_qr_x' => 680,
            'pos_qr_y' => 440,
        ], array_filter($request->only([
            'pos_number_x', 'pos_number_y', 'pos_name_x',
            'pos_name_y', 'pos_event_x', 'pos_event_y', 'pos_qr_x', 'pos_qr_y',
        ]), fn ($value) => $value !== null && $value !== ''));
        $eventName = $request->event_name ?? 'Kegiatan';
        $issueDate = $request->issue_date ?? date('Y-m-d');

        $participants = $request->input('participants');
        if (is_array($participants) && count($participants) > 0) {
            $participantsToCreate = [];
            $seenNames = [];
            $seenIdentities = [];

            foreach ($participants as $participant) {
                if (! is_array($participant)) {
                    continue;
                }

                $name = trim((string) ($participant['name'] ?? ''));
                if ($name === '') {
                    continue;
                }

                $identity = trim((string) ($participant['identity_number'] ?? '-'));
                $nameKey = Str::lower($name);
                $identityKey = $identity !== '' && $identity !== '-'
                    ? Str::lower($identity)
                    : null;

                if (isset($seenNames[$nameKey]) || ($identityKey !== null && isset($seenIdentities[$identityKey]))) {
                    return back()->withInput()->withErrors([
                        'participants' => "Nama atau NIK/NIP peserta tercatat lebih dari sekali pada seri {$prefix}.",
                    ]);
                }

                if ($this->alreadyIssuedInSeries($prefix, $name, $identity)) {
                    return back()->withInput()->withErrors([
                        'participants' => "Seri {$prefix} sudah pernah diterbitkan untuk nama atau NIK/NIP ini.",
                    ]);
                }

                $seenNames[$nameKey] = true;
                if ($identityKey !== null) {
                    $seenIdentities[$identityKey] = true;
                }

                $participantsToCreate[] = [$participant, $name, $identity ?: '-'];
            }

            $templatePath = $this->storeTemplate($request);

            foreach ($participantsToCreate as $index => [$participant, $name, $identity]) {
                Certificate::create([
                    'certificate_number' => $prefix . strtoupper(Str::random(5)) . '-' . ($index + 1),
                    'recipient_name' => $name,
                    'recipient_identity' => $identity,
                    'institution' => $participant['agency'] ?? 'Diskominfo',
                    'event_name' => $eventName,
                    'role' => $participant['role'] ?? 'Peserta',
                    'issue_date' => $issueDate,
                    'template_path' => $templatePath,
                    ...$positions,
                    'qr_token' => Str::uuid()->toString(),
                ]);
            }
        } else {
            $name = trim((string) $request->input('recipient_name', 'Peserta'));
            $identity = trim((string) $request->input('recipient_identity', '-'));

            if ($this->alreadyIssuedInSeries($prefix, $name, $identity)) {
                return back()->withInput()->withErrors([
                    'recipient_identity' => "Seri {$prefix} sudah pernah diterbitkan untuk nama atau NIK/NIP ini.",
                ]);
            }

            $templatePath = $this->storeTemplate($request);
            Certificate::create([
                'certificate_number' => $prefix . strtoupper(Str::random(6)),
                'recipient_name' => $name,
                'recipient_identity' => $identity ?: '-',
                'institution' => $request->institution ?? 'Diskominfo',
                'event_name'         => $eventName,
                'role' => $request->role ?? 'Peserta',
                'issue_date' => $issueDate,
                'template_path' => $templatePath,
                ...$positions,
                'qr_token' => Str::uuid()->toString(),
            ]);
        }

        // REDIRECT LANGSUNG KE HALAMAN CEK SERTIFIKAT
        return redirect()->route('admin.certificate.check')->with('success', 'Sertifikat berhasil dibuat!');
    }

    private function alreadyIssuedInSeries(string $prefix, string $name, string $identity): bool
    {
        $nameKey = Str::lower(trim($name));
        $identityKey = Str::lower(trim($identity));

        return Certificate::query()
            ->whereRaw('SUBSTR(certificate_number, 1, ?) = ?', [strlen($prefix), $prefix])
            ->where(function ($query) use ($nameKey, $identityKey): void {
                $query->whereRaw('LOWER(TRIM(recipient_name)) = ?', [$nameKey]);

                if ($identityKey !== '' && $identityKey !== '-') {
                    $query->orWhereRaw('LOWER(TRIM(recipient_identity)) = ?', [$identityKey]);
                }
            })
            ->exists();
    }

    private function storeTemplate(Request $request): string
    {
        if ($request->hasFile('template')) {
            return $request->file('template')->store('certificates/templates', 'public');
        }

        return $request->input('existing_template_path')
            ?? $request->input('template_path')
            ?? 'default-template.png';
    }

    public function draftEditor()
    {
        return view('pages.admin.editor_positions');
    }

    public function editor(int $id)
    {
        $certificate = Certificate::findOrFail($id);
        $templateUrl = $certificate->template_path
            ? Storage::disk('public')->url($certificate->template_path)
            : null;

        return view('pages.admin.editor', compact('certificate', 'templateUrl'));
    }

    public function updatePositions(Request $request, int $id)
    {
        $positions = $request->validate([
            'pos_number_x' => ['required', 'integer', 'between:0,800'],
            'pos_number_y' => ['required', 'integer', 'between:0,565'],
            'pos_name_x' => ['required', 'integer', 'between:0,800'],
            'pos_name_y' => ['required', 'integer', 'between:0,565'],
            'pos_event_x' => ['required', 'integer', 'between:0,800'],
            'pos_event_y' => ['required', 'integer', 'between:0,565'],
            'pos_qr_x' => ['required', 'integer', 'between:0,800'],
            'pos_qr_y' => ['required', 'integer', 'between:0,565'],
        ]);

        $certificate = Certificate::findOrFail($id);
        $certificate->update($positions);

        return redirect()->route('admin.certificate.editor', $id)
            ->with('success', 'Posisi sertifikat berhasil disimpan.');
    }

    public function showAll(Request $request)
    {
        $certificates = Certificate::query()
            ->when($request->query('token'), fn ($query, $token) => $query->where('qr_token', $token))
            ->when($request->filled('name'), fn ($query) => $query->where('recipient_name', 'like', '%'.$request->input('name').'%'))
            ->when($request->filled('identity_number'), fn ($query) => $query->where('recipient_identity', 'like', '%'.$request->input('identity_number').'%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.check_certificate', compact('certificates'));
    }

    public function showPublic(Request $request)
    {
        $request->validate([
            'token' => ['nullable', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'identity_number' => ['nullable', 'string', 'max:255'],
        ]);

        $hasSearch = $request->filled('token')
            || $request->filled('name')
            || $request->filled('identity_number');

        $certificates = Certificate::query()
            ->when(! $hasSearch, fn ($query) => $query->whereRaw('1 = 0'))
            ->when($request->filled('token'), fn ($query) => $query->where('qr_token', $request->query('token')))
            ->when($request->filled('name'), fn ($query) => $query->where('recipient_name', 'like', '%'.$request->query('name').'%'))
            ->when($request->filled('identity_number'), fn ($query) => $query->where('recipient_identity', 'like', '%'.$request->query('identity_number').'%'))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pages.public.certificate_search', compact('certificates', 'hasSearch'));
    }

    public function download(int $id)
    {
        return $this->downloadCertificate(Certificate::findOrFail($id));
    }

    public function downloadPublic(string $token)
    {
        $certificate = Certificate::where('qr_token', $token)->firstOrFail();

        return $this->downloadCertificate($certificate);
    }

    private function downloadCertificate(Certificate $certificate)
    {
        $disk = Storage::disk('public');
        $templateDataUri = null;

        if ($certificate->template_path && $disk->exists($certificate->template_path)) {
            $mimeType = $disk->mimeType($certificate->template_path) ?: 'image/png';
            $templateDataUri = 'data:'.$mimeType.';base64,'.base64_encode($disk->get($certificate->template_path));
        }

        $renderer = new ImageRenderer(new RendererStyle(200), new SvgImageBackEnd());
        $qrCode = base64_encode((new Writer($renderer))->writeString(
            route('public.certificate.check', ['token' => $certificate->qr_token]),
        ));

        return Pdf::loadView('pages.pdf_template', compact('certificate', 'templateDataUri', 'qrCode'))
            ->setPaper('a4', 'landscape')
            ->download('certificate-'.$certificate->id.'.pdf');
    }

    // METHOD HAPUS SERTIFIKAT
    public function destroy($id)
    {
        $certificate = Certificate::findOrFail($id);
        
        // Hapus file template jika ada
        $disk = Storage::disk('public');
        if ($certificate->template_path && $disk->exists($certificate->template_path)) {
            $disk->delete($certificate->template_path);
        }

        $certificate->delete();

        return redirect()->route('admin.certificate.check')->with('success', 'Sertifikat berhasil dihapus!');
    }
}