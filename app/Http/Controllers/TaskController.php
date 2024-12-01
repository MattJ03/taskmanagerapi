<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\User;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
      $user = Auth::user();
      $query = $user->tasks();

      if($request->has('search') && !empty($request->search)){
          $query->where(function ($query) use ($request) {
              $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
          });
      }
      if($request->has('product_id') && !empty($request->product_id)) {

          $query->where('product_id', $request->product_id);
      }

      $tasks = $query->orderBy('created_at', 'desc')->paginate(10);
      return response()->json($tasks,200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'description' => 'required|string|max:300',

        ]);

        $task = Auth::user()->tasks()->create([
            'name' => $request->name,
            'description' => $request->description,
        ]);
        return response()->json($task);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Auth::user()->tasks()->find($id);
        if(!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        return response()->json($task);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Auth::user()->tasks()->find($id);
        if(!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }
        $task->update($request->all());
        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Auth::user()->tasks()->find($id);
        if(!$task) {
            return response()->json(['message' => 'Task not found'], 403);
        }
        $task->delete();
        return response()->json(['message' => 'Task deleted']);

    }
}

