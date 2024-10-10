<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    public function createTask(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $fields = $request->all();

            $errors = Validator::make($fields, [
                'name' => 'required',
                'project_id' => 'required|numeric',
                'member_id' => 'required|array',
                'member_id.*' => 'numeric',
            ]);

            if ($errors->fails()) {
                return response($errors->errors()->all(), 422);
            }

            $task = Task::create([
                'name' => $fields['name'],
                'project_id' => $fields['project_id'],
                'status' => Task::NOT_STARTED,
            ]);

            $members = $fields['member_id'];
            for ($i = 0; $i < count($members); $i++) {

                TaskMember::create([
                    'project_id' => $fields['project_id'],
                    'task_id' => $task->id,
                    'member_id' => $members[$i]
                ]);
            }

            return response(['message' => 'Taks created successfully']);
        });
    }

    public function TaskToNotStartedToPending(Request $request)
    {
        Task::changeTaskStatus($request->task_id, Task::PENDING);
        return response(['message' => 'Taks move to pending']);
    }

    public function TaskToNotStartedToComplated(Request $request)
    {
        Task::changeTaskStatus($request->task_id, Task::COMPLATED);
        return response(['message' => 'Taks move to complated']);
    }

    public function TaskToPendingToComplated(Request $request)
    {
        Task::changeTaskStatus($request->task_id, Task::COMPLATED);
        return response(['message' => 'Taks move to complated']);
    }

    public function TaskToPendingToNotStarted(Request $request)
    {
        Task::changeTaskStatus($request->task_id, Task::NOT_STARTED);
        return response(['message' => 'Taks move to not started']);
    }

    public function TaskToComplatedToPending(Request $request)
    {
        Task::changeTaskStatus($request->task_id, Task::PENDING);
        return response(['message' => 'Taks move to pending']);
    }

    public function TaskToComplatedToNotStarted(Request $request)
    {
        Task::changeTaskStatus($request->task_id, Task::NOT_STARTED);
        return response(['message' => 'Taks move to not started']);
    }
}
