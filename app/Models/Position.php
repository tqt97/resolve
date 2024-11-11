<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    /** @use HasFactory<\Database\Factories\PositionFactory> */
    use HasFactory;
    protected $fillable = [
        'title',
        'department_id',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'position_user');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
