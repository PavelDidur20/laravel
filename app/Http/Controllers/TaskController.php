<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    protected function validationRules(bool $isUpdate = false): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:pending,in_progress,completed'
        ];

        return $isUpdate ? array_map(fn($rule) => "sometimes|$rule", $rules) : $rules;
    }

    public function index()
    {
        return response()->json(Task::all());
    }
    
    public function store(Request $request)
    {
        $data = $request->validate($this->validationRules());
        $task = Task::create($data);
        return response()->json($task, 201);
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate($this->validationRules());
        $task->update($data);
        return response()->json($task);

    }

    public function show(Task $task)
    {
        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(null, 204);
    }

}