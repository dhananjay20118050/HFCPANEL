<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'ip', 'port','process_id'
    ];

}