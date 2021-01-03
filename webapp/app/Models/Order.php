<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\Enums;

class Order extends Model
{
    use HasFactory, Enums;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'notes',
        'total_price',
        'date'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'current_status_at' => 'datetime'
    ];

    /**
     * The type attribute enumeration
     */
    protected $enumStatuses = [
        'H', // Holding
        'P', // Preparing
        'R', // Ready
        'T', // In Transit
        'D', // Delivered
        'C'  // Cancelled
    ];

    /**
     * Get the comments for the blog post.
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
