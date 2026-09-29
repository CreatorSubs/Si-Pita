<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class CertificateFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_admin_sidebar_displays_logout_confirmation(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.certificate.create'))
            ->assertOk()
            ->assertSee('logoutConfirmModal')
            ->assertSee('Logout?')
            ->assertSee('Tidak')
            ->assertSee('Ya');
    }

    public function test_draft_position_editor_opens_without_a_saved_certificate(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.certificate.editor.draft'))
            ->assertOk()
            ->assertSee('certificateCanvas')
            ->assertSee('dragCertNumber');

        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_draft_position_editor_opens_before_certificate_is_saved(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.certificate.editor.draft'))
            ->assertOk()
            ->assertSee('certificateCanvas')
            ->assertSee('draft_cert_image');

        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_draft_positions_are_saved_with_new_certificate(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.certificate.store'), [
            'certificate_number_prefix' => 'SERT/DRAFT/2026',
            'recipient_name' => 'Draft Recipient',
            'recipient_identity' => 'DRAFT-001',
            'event_name' => 'Draft Event',
            'issue_date' => '2026-09-28',
            'pos_number_x' => 120,
            'pos_number_y' => 36,
            'pos_name_x' => 240,
            'pos_name_y' => 210,
            'pos_event_x' => 230,
            'pos_event_y' => 310,
            'pos_qr_x' => 650,
            'pos_qr_y' => 420,
        ])->assertRedirect(route('admin.certificate.check'));

        $this->assertDatabaseHas('certificates', [
            'recipient_identity' => 'DRAFT-001',
            'pos_number_x' => 120,
            'pos_number_y' => 36,
            'pos_name_x' => 240,
            'pos_name_y' => 210,
            'pos_event_x' => 230,
            'pos_event_y' => 310,
            'pos_qr_x' => 650,
            'pos_qr_y' => 420,
        ]);
    }

    public function test_public_certificate_search_filters_by_name_and_identity(): void
    {
        Certificate::create([
            'certificate_number' => 'SERT/SEARCH/001',
            'recipient_name' => 'Ayu Lestari',
            'recipient_identity' => 'ID-1001',
            'event_name' => 'Test Event',
            'issue_date' => '2026-09-28',
            'template_path' => 'template.png',
            'qr_token' => (string) Str::uuid(),
        ]);

        Certificate::create([
            'certificate_number' => 'SERT/SEARCH/002',
            'recipient_name' => 'Budi Santoso',
            'recipient_identity' => 'ID-2002',
            'event_name' => 'Test Event',
            'issue_date' => '2026-09-28',
            'template_path' => 'template.png',
            'qr_token' => (string) Str::uuid(),
        ]);

        $this->get(route('certificate.search', [
            'name' => 'Ayu',
            'identity_number' => '1001',
        ]))
            ->assertOk()
            ->assertSee('Ayu Lestari')
            ->assertDontSee('Budi Santoso')
            ->assertDontSee('ID-1001')
            ->assertDontSee('Buat Sertifikat Baru')
            ->assertDontSee('admin.certificate.create')
            ->assertSee(route('public.certificate.download', Certificate::first()->qr_token));
    }

    public function test_public_search_without_filters_does_not_list_certificates(): void
    {
        Certificate::create([
            'certificate_number' => 'SERT/PRIVATE/001',
            'recipient_name' => 'Private Recipient',
            'recipient_identity' => 'ID-PRIVATE',
            'event_name' => 'Test Event',
            'issue_date' => '2026-09-28',
            'template_path' => 'template.png',
            'qr_token' => (string) Str::uuid(),
        ]);

        $this->get(route('certificate.search'))
            ->assertOk()
            ->assertSee('Masukkan nama atau nomor identitas')
            ->assertDontSee('Private Recipient')
            ->assertDontSee('Buat Sertifikat Baru');
    }

    public function test_guests_cannot_open_admin_certificate_pages(): void
    {
        $this->get(route('admin.certificate.create'))
            ->assertRedirect(route('login'));

        $this->get(route('admin.certificate.show_all'))
            ->assertRedirect(route('login'));
    }

    public function test_public_download_includes_uploaded_design_and_certificate_data(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put(
            'certificates/templates/design.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/8eYAAAAASUVORK5CYII='),
        );

        $certificate = Certificate::create([
            'certificate_number' => 'SERT/100/2026-ABCDE',
            'recipient_name' => 'Dina Pratama',
            'recipient_identity' => 'ID-1000',
            'institution' => 'Diskominfo',
            'event_name' => 'Pelatihan Digital',
            'role' => 'Peserta',
            'issue_date' => '2026-09-28',
            'template_path' => 'certificates/templates/design.png',
            'qr_token' => (string) Str::uuid(),
        ]);

        $response = $this->get(route('public.certificate.download', $certificate->qr_token))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_public_download_returns_not_found_for_unknown_token(): void
    {
        $this->get(route('public.certificate.download', (string) Str::uuid()))
            ->assertNotFound();
    }

    public function test_same_person_cannot_receive_the_same_certificate_series_twice(): void
    {
        $this->actingAs(User::factory()->create());

        Certificate::create([
            'certificate_number' => 'SERT/001/2026-ABCDE-1',
            'recipient_name' => 'Ayu Lestari',
            'recipient_identity' => 'NIK-1001',
            'event_name' => 'Test Event',
            'issue_date' => '2026-09-28',
            'template_path' => 'template.png',
            'qr_token' => (string) Str::uuid(),
        ]);

        $duplicateIdentity = [
            'certificate_number_prefix' => 'SERT/001/2026',
            'event_name' => 'Test Event',
            'issue_date' => '2026-09-28',
            'participants' => [
                ['name' => 'Nama Berbeda', 'identity_number' => 'NIK-1001'],
            ],
        ];

        $this->from(route('admin.certificate.create'))
            ->post(route('admin.certificate.store'), $duplicateIdentity)
            ->assertRedirect(route('admin.certificate.create'))
            ->assertSessionHasErrors('participants');

        $duplicateName = $duplicateIdentity;
        $duplicateName['participants'][0] = [
            'name' => 'Ayu Lestari',
            'identity_number' => 'NIK-2002',
        ];

        $this->from(route('admin.certificate.create'))
            ->post(route('admin.certificate.store'), $duplicateName)
            ->assertRedirect(route('admin.certificate.create'))
            ->assertSessionHasErrors('participants');

        $differentSeries = $duplicateIdentity;
        $differentSeries['certificate_number_prefix'] = 'SERT/002/2026';

        $this->from(route('admin.certificate.create'))
            ->post(route('admin.certificate.store'), $differentSeries)
            ->assertRedirect(route('admin.certificate.check'));

        $this->assertDatabaseCount('certificates', 2);
    }

    public function test_duplicate_people_in_one_batch_are_rejected_before_insert(): void
    {
        $this->actingAs(User::factory()->create());

        $this->from(route('admin.certificate.create'))
            ->post(route('admin.certificate.store'), [
                'certificate_number_prefix' => 'SERT/003/2026',
                'event_name' => 'Test Event',
                'issue_date' => '2026-09-28',
                'participants' => [
                    ['name' => 'Budi Santoso', 'identity_number' => 'NIK-3003'],
                    ['name' => 'Budi Santoso', 'identity_number' => 'NIK-4004'],
                ],
            ])
            ->assertRedirect(route('admin.certificate.create'))
            ->assertSessionHasErrors('participants');

        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_single_certificate_creation_also_rejects_a_reused_series(): void
    {
        $this->actingAs(User::factory()->create());

        Certificate::create([
            'certificate_number' => 'SERT/005/2026-ABCDE',
            'recipient_name' => 'Siti Aminah',
            'recipient_identity' => 'NIK-5005',
            'event_name' => 'Test Event',
            'issue_date' => '2026-09-28',
            'template_path' => 'template.png',
            'qr_token' => (string) Str::uuid(),
        ]);

        $this->from(route('admin.certificate.create'))
            ->post(route('admin.certificate.store'), [
                'certificate_number' => 'SERT/005/2026',
                'recipient_name' => 'Nama Berbeda',
                'recipient_identity' => 'NIK-5005',
                'event_name' => 'Test Event',
                'issue_date' => '2026-09-28',
            ])
            ->assertRedirect(route('admin.certificate.create'))
            ->assertSessionHasErrors('recipient_identity');

        $this->assertDatabaseCount('certificates', 1);
    }

    public function test_positions_from_draft_are_saved_on_created_certificate(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('admin.certificate.store'), [
            'certificate_number_prefix' => 'SERT/DRAFT/2026',
            'recipient_name' => 'Draft Recipient',
            'recipient_identity' => 'DRAFT-001',
            'event_name' => 'Draft Event',
            'issue_date' => '2026-09-28',
            'template_path' => 'certificates/templates/draft.png',
            'pos_number_x' => 120,
            'pos_number_y' => 36,
            'pos_name_x' => 240,
            'pos_name_y' => 210,
            'pos_event_x' => 230,
            'pos_event_y' => 310,
            'pos_qr_x' => 650,
            'pos_qr_y' => 420,
        ])->assertRedirect(route('admin.certificate.check'));

        $this->assertDatabaseHas('certificates', [
            'recipient_identity' => 'DRAFT-001',
            'pos_number_x' => 120,
            'pos_number_y' => 36,
            'pos_name_x' => 240,
            'pos_name_y' => 210,
            'pos_event_x' => 230,
            'pos_event_y' => 310,
            'pos_qr_x' => 650,
            'pos_qr_y' => 420,
        ]);
    }

    public function test_editor_positions_persist_and_certificate_downloads_as_pdf(): void
    {
        $this->actingAs(User::factory()->create());

        $certificate = Certificate::create([
            'certificate_number' => 'SERT/001/2026',
            'recipient_name' => 'Test Recipient',
            'event_name' => 'Test Event',
            'issue_date' => '2026-09-28',
            'template_path' => 'template.png',
            'qr_token' => (string) Str::uuid(),
        ]);

        $this->get(route('admin.certificate.editor', $certificate))
            ->assertOk();

        $positions = [
            'pos_number_x' => 210,
            'pos_number_y' => 45,
            'pos_name_x' => 260,
            'pos_name_y' => 230,
            'pos_event_x' => 270,
            'pos_event_y' => 310,
            'pos_qr_x' => 680,
            'pos_qr_y' => 440,
        ];

        $this->post(route('admin.certificate.update_positions', $certificate), $positions)
            ->assertRedirect(route('admin.certificate.editor', $certificate));

        $this->assertDatabaseHas('certificates', $positions + ['id' => $certificate->id]);

        $this->get(route('admin.certificate.download', $certificate))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}