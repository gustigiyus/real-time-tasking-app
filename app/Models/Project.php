<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    const NOT_STARTED = 0;
    const PENDING = 1;
    const COMPLATED = 2;

    protected $guarded = [];

    public static function createSlug($name)
    {
        $code = Str::random(10) . time();
        $slug = Str::slug($name) . '-' . $code;
        return $slug;
    }

    public function task_progress(): HasOne
    {
        return $this->hasOne(TaskProgress::class, 'project_id', 'id');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_id', 'id');
    }
}
