<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'is_member',
        'discount_percent',
    ];

    protected function casts(): array
    {
        return [
            'is_member' => 'boolean',
            'discount_percent' => 'decimal:2',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function getTotalPiutangAttribute()
    {
        return $this->transactions()
            ->where('payment_status', 'due')
            ->sum('grand_total');
    }
}
