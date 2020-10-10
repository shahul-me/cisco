<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Router extends Model
{
    use SoftDeletes;

    protected $table = 'routers';

    protected $fillable = [
        'sapid', 'hostname','loopback', 'mac','type',
    ];
}
