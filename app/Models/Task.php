<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

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

    public static $updatingRules = [
        'title' => 'required|string',
        'description' => 'present|string|nullable',
        'image_path' => 'present|string|nullable',
        'completed' => 'required|bool',
    ];

    /**
     * The users that belongs to the task.
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}