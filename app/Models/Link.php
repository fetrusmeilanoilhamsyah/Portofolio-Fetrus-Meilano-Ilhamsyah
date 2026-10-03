<?php

namespace App\Models;

use App\Enums\LinkGroup;
use App\Models\Concerns\DispatchesContentChanged;
use Database\Factories\LinkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

#[Fillable([
    'group',
    'label',
    'url',
    'icon',
    'note',
    'is_published',
    'show_on_cv',
    'sort_order',
])]
class Link extends Model
{
    /** @use HasFactory<LinkFactory> */
    use DispatchesContentChanged, HasFactory, HasTranslations;

    public array $translatable = ['note'];

    protected $attributes = [
        'is_published' => false,
        'show_on_cv' => false,
        'sort_order' => 0,
    ];

    protected function casts(): array
    {
        return [
            'group' => LinkGroup::class,
            'is_published' => 'boolean',
            'show_on_cv' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(LinkHighlight::class);
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
