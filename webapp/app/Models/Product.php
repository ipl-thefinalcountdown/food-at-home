<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\Enums;

class Product extends Model
{
    use HasFactory, SoftDeletes, Enums;

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
        'name',
        'description',
        'photo_url',
        'price',
        'type'
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

    /**
     * The type attribute enumeration
     */
    protected $enumTypes = [
        'hot dish',
        'cold dish',
        'drink',
        'dessert'
    ];
}
