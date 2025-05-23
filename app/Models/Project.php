<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'is_domain',
        'enabled',
        'description',
        'links',
        'parent_id',
        'vhi_project_id',
        'vhi_domain_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
