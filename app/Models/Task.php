<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    const NOT_STARTED = 0;
    const PENDING = 1;
    const COMPLATED = 2;

    protected $guarded = [];

    public function task_members(): HasMany
    {
        return $this->hasMany(TaskMember::class, 'task_id', 'id');
    }

    public static function changeTaskStatus($taskId, $status)
    {
        Task::where('id', $taskId)
            ->update(['status' => $status]);
    }
}
