<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditCard extends Model
{
    use HasFactory;

    protected $table = "credit_cards";

    protected $fillable = [
        'type',
        'number',
        'name',
        'expiration_date',
        'user_id',
    ];

    protected $casts = [];

    public function user(): BelongsTo
    {

        return $this->belongsTo(User::class);

    }
}
