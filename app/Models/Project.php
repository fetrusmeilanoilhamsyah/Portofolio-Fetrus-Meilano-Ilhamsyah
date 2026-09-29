<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Models\Concerns\DispatchesContentChanged;
use Database\Factories\ProjectFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

#[Fillable([
    'slug',
    'type',
    'title',
    'summary',
    'body',
    'stack',
    'cover_image',
    'cover_alt',
    'telegram_url',
    'site_url',
    'demo_url',
    'repo_url',
    'started_at',
    'ended_at',
    'is_featured',
    'status',
    'published_at',
    'sort_order',
])]
class Project extends Model
{
    use DispatchesContentChanged;

    /** @use HasFactory<ProjectFactory> */
    use HasFactory, HasTranslations;

    public array $translatable = ['title', 'summary', 'body', 'cover_alt'];

    /**
     * Default attribute values — mencerminkan default kolom di migration.
     * Penting agar Eloquent mengisi nilai ini sebelum save, tidak hanya
     * mengandalkan database default yang baru aktif setelah refresh().
     */
    protected $attributes = [
        'status' => 'draft',
        'is_featured' => false,
        'sort_order' => 0,
    ];

    protected function casts(): array
    {
        return [
            'type' => ProjectType::class,
            'status' => ProjectStatus::class,
            'stack' => 'array',
            'started_at' => 'date',
            'ended_at' => 'date',
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Project $project) {
            // Buat slug otomatis dari judul bahasa Indonesia jika belum ada
            if (empty($project->slug)) {
                $titleId = $project->getTranslation('title', 'id', false);
                $project->slug = static::generateUniqueSlug($titleId, $project->id);
            }

            // published_at diisi sekali saja saat pertama kali diterbitkan
            if (
                $project->status === ProjectStatus::Published
                && $project->published_at === null
            ) {
                $project->published_at = now();
            }

            // Slug tidak berubah jika proyek sudah pernah terbit
            if ($project->exists && $project->published_at !== null && $project->isDirty('slug')) {
                $project->slug = $project->getOriginal('slug');
            }
        });
    }

    /**
     * Buat slug unik dari teks. Jika ada bentrok, tambah akhiran angka.
     */
    public static function generateUniqueSlug(?string $text, ?int $excludeId = null): string
    {
        $base = Str::slug($text ?? '');
        if (empty($base)) {
            $base = 'project-'.Str::random(6);
        }
        $slug = $base;
        $counter = 1;

        while (
            static::where('slug', $slug)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = $base.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProjectMedia::class);
    }

    /**
     * Scope: hanya proyek dengan status 'published'.
     * Tidak memakai jadwal (published_at); terbit = status published.
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('status', ProjectStatus::Published);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('published_at', 'desc');
    }

    public function scopeFeatured(Builder $query): void
    {
        $query->where('is_featured', true);
    }
}
