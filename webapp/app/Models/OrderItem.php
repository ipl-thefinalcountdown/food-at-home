<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

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
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'sub_total_price'
    ];

    /**
     * The timestamps are created on insert or update
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Get the post that owns the comment.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
