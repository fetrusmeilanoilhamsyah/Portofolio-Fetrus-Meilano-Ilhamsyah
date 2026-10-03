<?php

namespace App\Models;

use App\Enums\ExperienceKind;
use App\Models\Concerns\DispatchesContentChanged;
use Database\Factories\ExperienceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

#[Fillable([
    'kind',
    'title',
    'organization',
    'location',
    'description',
    'logo',
    'started_at',
    'ended_at',
    'is_published',
    'show_on_cv',
    'sort_order',
])]
class Experience extends Model
{
    /** @use HasFactory<ExperienceFactory> */
    use DispatchesContentChanged, HasFactory, HasTranslations;

    public array $translatable = ['title', 'description'];

    protected $attributes = [
        'is_published' => false,
        'show_on_cv' => true,
        'sort_order' => 0,
    ];

    protected function casts(): array
    {
        return [
            'kind' => ExperienceKind::class,
            'started_at' => 'date',
            'ended_at' => 'date',
            'is_published' => 'boolean',
            'show_on_cv' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('started_at', 'desc');
    }
}
