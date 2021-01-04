<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Traits\Enums;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Enums;

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
        'email',
        'password',
        'photo_url',
        'type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'logged_at' => 'datetime',
        'available_at' => 'datetime'
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
        'C',  // Customer
        'EC', // Employee-Cook
        'ED', // Employee-Deliveryman
        'EM'  // Employee-Manager
    ];
}
