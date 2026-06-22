<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CashFlow extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_type',
        'reference_type',
        'reference_id',
        'amount',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
