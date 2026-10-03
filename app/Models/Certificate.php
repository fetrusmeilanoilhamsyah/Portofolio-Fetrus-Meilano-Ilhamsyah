<?php

namespace App\Models;

use App\Models\Concerns\DispatchesContentChanged;
use Database\Factories\CertificateFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

#[Fillable([
    'title',
    'issuer',
    'category',
    'issued_at',
    'expires_at',
    'credential_url',
    'image',
    'file',
    'alt',
    'is_published',
    'show_on_cv',
    'sort_order',
])]
class Certificate extends Model
{
    /** @use HasFactory<CertificateFactory> */
    use DispatchesContentChanged, HasFactory, HasTranslations;

    public array $translatable = ['alt'];

    protected $attributes = [
        'is_published' => false,
        'show_on_cv' => true,
        'sort_order' => 0,
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'expires_at' => 'date',
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
        $query->orderBy('sort_order')->orderBy('issued_at', 'desc');
    }
}
