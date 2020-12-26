<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

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
        'address',
        'phone',
        'nif'
    ];

    /**
     * The timestamps are created on insert or update
     *
     * @var boolean
     */
    public $timestamps = true;

    /**
     * The row is soft deletable
     *
     * @var boolean
     */
    protected $softDelete = true;

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'id');
    }
}
