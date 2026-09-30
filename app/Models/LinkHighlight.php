<?php

namespace App\Models;

use App\Models\Concerns\DispatchesContentChanged;
use Database\Factories\LinkHighlightFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

#[Fillable([
    'link_id',
    'title',
    'summary',
    'url',
    'highlighted_at',
    'is_published',
    'sort_order',
])]
class LinkHighlight extends Model
{
    /** @use HasFactory<LinkHighlightFactory> */
    use DispatchesContentChanged, HasFactory, HasTranslations;

    public array $translatable = ['title', 'summary'];

    protected $attributes = [
        'is_published' => false,
        'sort_order' => 0,
    ];

    protected function casts(): array
    {
        return [
            'highlighted_at' => 'date',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class);
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }
}
