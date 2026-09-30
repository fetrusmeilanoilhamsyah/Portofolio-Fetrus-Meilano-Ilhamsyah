<?php

namespace Tests\Feature;

use App\Filament\Resources\Certificates\Pages\CreateCertificate;
use App\Filament\Resources\Certificates\Pages\EditCertificate;
use App\Filament\Resources\Certificates\Pages\ListCertificates;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CertificateResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['email' => config('portfolio.admin_email')]));
    }

    public function test_can_render_list_certificates()
    {
        Certificate::factory()->create();

        Livewire::test(ListCertificates::class)
            ->assertSuccessful();
    }

    public function test_can_create_certificate()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('cert.jpg', 100, 100);

        Livewire::test(CreateCertificate::class)
            ->fillForm([
                'title' => 'Sertifikat A',
                'issuer' => 'Penerbit A',
                'category' => 'Kursus',
                'issued_at' => '2023-01-01',
                'image' => $file,
                'alt.id' => 'Gambar Sertifikat A',
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('certificates', [
            'title' => 'Sertifikat A',
            'issuer' => 'Penerbit A',
        ]);

        $certificate = Certificate::first();
        $this->assertEquals('Gambar Sertifikat A', $certificate->getTranslation('alt', 'id'));
    }

    public function test_can_edit_certificate()
    {
        $certificate = Certificate::factory()->create([
            'image' => 'cert.jpg',
            'alt' => ['id' => 'Alt Lama', 'en' => 'Old Alt'],
        ]);

        Livewire::test(EditCertificate::class, ['record' => $certificate->getRouteKey()])
            ->assertSuccessful()
            ->assertFormSet([
                'alt.id' => 'Alt Lama',
                'alt.en' => 'Old Alt',
            ])
            ->fillForm([
                'alt.id' => 'Alt Baru',
                'image' => UploadedFile::fake()->image('cert.jpg', 100, 100),
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertEquals('Alt Baru', $certificate->refresh()->getTranslation('alt', 'id'));
    }

    public function test_regression_certificate_file_retained_on_edit_without_changes()
    {
        Storage::fake('public');

        $certificate = Certificate::factory()->create([
            'image' => 'certificates/fake-image.jpg',
            'file' => 'certificates/fake-file.pdf',
        ]);

        Storage::disk('public')->put('certificates/fake-image.jpg', 'content');
        Storage::disk('public')->put('certificates/fake-file.pdf', 'content');

        Livewire::test(EditCertificate::class, ['record' => $certificate->getRouteKey()])
            ->assertSuccessful()
            ->assertFormSet([
                'image' => 'certificates/fake-image.jpg',
                'file' => 'certificates/fake-file.pdf',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $certificate->refresh();
        $this->assertEquals('certificates/fake-image.jpg', $certificate->image);
        $this->assertEquals('certificates/fake-file.pdf', $certificate->file);

        Storage::disk('public')->assertExists('certificates/fake-image.jpg');
        Storage::disk('public')->assertExists('certificates/fake-file.pdf');
    }
}
