<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TaskMember extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function member(): HasOne
    {
        return $this->hasOne(Member::class, 'id', 'member_id');
    }
}
