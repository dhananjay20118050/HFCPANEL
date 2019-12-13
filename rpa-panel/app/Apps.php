<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Apps extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = [
        'name', 'db_username', 'db_password','db_host','db_port','db_name','config','sftp_config'
    ];

    public $timestamps = false;

}

