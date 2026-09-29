<?php

namespace App\Models;

use App\Enums\MediaKind;
use App\Models\Concerns\DispatchesContentChanged;
use Database\Factories\ProjectMediaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

#[Fillable([
    'project_id',
    'kind',
    'path',
    'url',
    'caption',
    'alt',
    'poster',
    'sort_order',
])]
class ProjectMedia extends Model
{
    use DispatchesContentChanged;

    /** @use HasFactory<ProjectMediaFactory> */
    use HasFactory, HasTranslations;

    public array $translatable = ['caption', 'alt'];

    protected function casts(): array
    {
        return [
            'kind' => MediaKind::class,
            'sort_order' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }
}
