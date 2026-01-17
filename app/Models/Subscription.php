<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'package_id',
        'router_id',
        'mikrotik_username',
        'mikrotik_password',
        'price',
        'due_date',
        'status',
    ];

    // Relasi ke Customer
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi ke Package
    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    // Relasi ke Router
    public function router(): BelongsTo
    {
        return $this->belongsTo(Router::class);
    }
}