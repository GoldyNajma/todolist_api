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
            if ($request->input('completed') !== null) {
                $task->completed = $request->input('completed');
            }
            $task->save();

            $user = Auth::user();
            $user->tasks()->attach($task->id);
            $task = Task::find($task->id);

            return response()->json([
                'message' => 'Task stored successfully.',
                'task' => $task, 
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to store task.',
            ], 409);
        }
    }

    public function index($completed = null) {
        if ($completed !== null) {
            $tasks = Auth::user()->tasks()
                ->where('completed', $completed)
                ->orderBy('created_at')
                ->get();
        } else {
            $tasks = Auth::user()->tasks()
                ->orderBy('created_at')
                ->get();
        }

        return response()->json([
            'message' => 'Success.',
            'tasks' => $tasks,
        ], 200);
    }
    
    public function indexCompleted() {
        return $this->index(true);
    }

    public function indexUncompleted() {
        return $this->index(false);
    }

    public function indexDeleted() {
        $tasks = Auth::user()->tasks()
            ->onlyTrashed()
            ->orderBy('deleted_at')
            ->get();

        return response()->json([
            'message' => 'Success.',
            'tasks' => $tasks,
        ], 200);
    }

    private function getUserTask($task_id) {
        $task = Auth::user()->tasks()
            ->where('id', $task_id)
            ->first();

        return $task;
    }

    public function show($id) {
        if (($task = $this->getUserTask($id)) === null) {
            return response()->json([
                'message' => 'Task not found.',
            ], 404);
        }

        return response()->json([
            'message' => 'Success.',
            'tasks' => $task,
        ], 200);
    }

    public function update(Request $request, $id) {
        $this->validate($request, Task::$updatingRules);
        
        if (($task = $this->getUserTask($id)) === null) {
            return response()->json([
                'message' => 'Task not found.',
            ], 404);
        }

        try {
            $task->title = $request->input('title');
            $task->description = $request->input('description');
            $task->image_path = $request->input('image_path');
            $task->completed = $request->input('completed');
            $task->save();

            $task = Task::find($task->id);
            
            return response()->json([
                'message' => 'Task updated successfully.',
                'task' => $task, 
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to update task.',
            ], 409);
        }
    }

    public function softDelete($id) {
        if (($task = $this->getUserTask($id)) === null) {
            return response()->json([
                'message' => 'Task not found.',
            ], 404);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task soft deleted successfully.',
            'deleted_at' => $task->deleted_at,
        ], 200);
    }

    public function restore($id) {
        $softDeletedTask = Auth::user()->tasks()
            ->onlyTrashed()
            ->where('id', $id)
            ->first();

        if ($softDeletedTask === null) {
            return response()->json([
                'message' => 'Soft deleted task not found.',
            ], 404);
        }

        $softDeletedTask->restore();
        $softDeletedTask = $this->getUserTask($softDeletedTask->id);

        return response()->json([
            'message' => 'Task restored successfully.',
            'task' => $softDeletedTask,
        ], 200);
    }

    public function forceDelete($id) {
        $task = Auth::user()->tasks()
            ->withTrashed()
            ->where('id', $id)
            ->first();

        if ($task === null) {
            return response()->json([
                'message' => 'Task not found.',
            ], 404);
        }

        $task->forceDelete();

        $task = Auth::user()->tasks()
            ->withTrashed()
            ->where('id', $id)
            ->first();

        if ($task === null) {
            return response()->json([
                'message' => 'Task deleted successfully.',
            ], 200);
        }

        return response()->json([
            'message' => 'Failed to delete task.',
        ], 409);
    }
}
