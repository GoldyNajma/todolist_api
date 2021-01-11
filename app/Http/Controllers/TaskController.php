<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Create a new TaskController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request) {
        $this->validate($request, Task::$storingRules);

        try {
            $task = new Task;
            $task->title = $request->input('title');
            $task->description = $request->input('description');
            $task->image_path = $request->input('image_path');
            if ($request->input('completed') != null) {
                $task->completed = $request->input('completed');
            }
            $task->save();

            $user = Auth::user();
            $user->tasks()->attach($task->id);
            $task = Task::find($task->id);

            return response()->json([
                'task' => $task, 
                'message' => 'Task stored successfully.',
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to store task.',
            ], 409);
        }
    }
}
