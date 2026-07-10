<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommandHistory extends Model
{
    protected $fillable = [
        'server_id',
        'command',
        'output',
        'exit_code'
    ];

    public function server()
    {
        return $this->belongsTo(RemoteServer::class);
    }

    public function getFormattedOutputAttribute()
    {
        return nl2br(e($this->output));
    }

    public function getStatusColorAttribute()
    {
        return $this->exit_code === 0 ? 'text-green-500' : 'text-red-500';
    }
}