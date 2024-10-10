<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\TaskProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProjectController extends Controller
{

    public function getProject(Request $request, $slug)
    {
        $projects = Project::with(['tasks.task_members.member'])
            ->where('projects.slug', $slug)
            ->first();

        return response(['data' => $projects], 200);
    }

    public function index(Request $request)
    {
        $query = $request->get('query');
        $projects = Project::with(['task_progress']);

        if (!is_null($query) && $query !== '') {
            $projects->where('name', 'like', '%' . $query . '%')
                ->orderBy('id', 'desc');

            return response(['data' => $projects->paginate(10)], 200);
        }

        return response(['data' => $projects->paginate(10)], 200);
    }

    public function store(request $request)
    {
        return DB::transaction(function () use ($request) {
            $fields = $request->all();

            $errors = Validator::make($fields, [
                'name' => 'required',
                'startDate' => 'required',
                'endDate' => 'required',
                'status' => 'required',
            ]);

            if ($errors->fails()) {
                return response($errors->errors()->all(), 422);
            }

            $projects = Project::create([
                'name' => $fields['name'],
                'slug' => Project::createSlug($fields['name']),
                'startDate' => $fields['startDate'],
                'endDate' => $fields['endDate'],
                'status' => Project::NOT_STARTED,
            ]);

            TaskProgress::create([
                'project_id' => $projects->id,
                'pinned_on_dashboard' => TaskProgress::NOT_PINNED_ON_DASHBOARD,
                'progress' => TaskProgress::INITIAL_PROJECT_PERCENT
            ]);

            return response(['message' => 'project created'], 200);
        });
    }

    public function update(request $request)
    {
        $fields = $request->all();
        $errors = Validator::make($fields, [
            'id' => 'required',
            'name' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'status' => 'required',
        ]);

        if ($errors->fails()) {
            return response($errors->errors()->all(), 422);
        }

        $user = Project::where('id', $fields['id'])->update([
            'name' => $fields['name'],
            'slug' => Project::createSlug($fields['name']),
            'startDate' => $fields['startDate'],
            'endDate' => $fields['endDate'],
        ]);

        return response(['message' => 'project updated'], 200);
    }

    public function pinnedProject(Request $request)
    {
        $fields = $request->all();

        $errors = Validator::make($fields, [
            'project_id' => 'required|numeric',
        ]);

        if ($errors->fails()) {
            return response($errors->errors()->all(), 422);
        }

        TaskProgress::where('project_id', $fields['project_id'])
            ->update([
                'pinned_on_dashboard' => TaskProgress::PINNED_ON_DASHBOARD
            ]);

        return response(['message' => 'project pinned on dashboard'], 200);
    }

    public function countProject()
    {
        $counts = Project::count();
        return response(['count' => $counts]);
    }
}
