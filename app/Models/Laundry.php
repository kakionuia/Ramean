<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Laundry extends Model
{
    protected $fillable = [
        'user_id',
        'store_name',
        'store_phone',
        'address',
        'description',
        'logo',
        'verification_document_path',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
