<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RemoteServer extends Model
{
    protected $fillable = [
        'name',
        'host',
        'port',
        'username',
        'password',
        'private_key',
        'auth_type',
        'is_active',
        'is_default'
    ];

    protected $hidden = [
        'password',
        'private_key'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean'
    ];

    public function histories()
    {
        return $this->hasMany(CommandHistory::class);
    }

    public static function getDefault()
    {
        return self::where('is_default', true)->where('is_active', true)->first();
    }

    public static function getActiveServers()
    {
        return self::where('is_active', true)->get();
    }
}