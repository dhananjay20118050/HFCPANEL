<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'ip', 'port'
    ];

}