<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommandHistory extends Model
{
    protected $fillable=[
        'command',
        'output'
    ];
}