<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class JournalEntry extends Model
{
    use HasUuids;

    protected $fillable = [
        'entry_date',
        'reference_number',
        'description',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function ($entry) {
            if (empty($entry->reference_number)) {
                $entry->reference_number = 'JE-'.date('Y').'-'.strtoupper(Str::random(6));
            }
        });
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalLine::class);
    }
}
