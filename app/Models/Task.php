<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'image_path', 'completed'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'completed' => 'boolean',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    public static $storingRules = [
        'title' => 'required|string',
        'description' => 'string',
        'image_path' => 'string',
        'completed' => 'bool',
    ];

    /**
     * The users that have the task.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}