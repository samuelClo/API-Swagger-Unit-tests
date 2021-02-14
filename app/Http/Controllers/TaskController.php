<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        if (!Auth::user())
        {
            return response()->json(["message" => "Unauthorized"], 401);
        }

        if ($request->query('completed'))
        {
            $tasks = Auth::user()->tasks()->where('completed', true)->orderBy("id", "desc")->get();
        }
        else
        {
            $tasks = Auth::user()->tasks()->orderBy("id", "ASC")->get();
        }

        return response()->json(["tasks" => $tasks], 200);
    }

    public function create(Request $request)
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'body' => 'required|max:250',
            ]);

            $task = new Task();
            $task->body = $request->input('body');
            $task->user_id = Auth::user()->id;
            $task->save();

            return response()->json([
                "task" => $task,
                "message" => "Task has been created successfully",
            ],200);
        }
        catch (Exception $error){
            return response()->json([
                "message" => "Error in creation",
                "error" => $error,
            ],422);
        }
    }

    public function show($id)
    {
        try {
            $task = Auth::user()->tasks()->findOrFail($id);

            if (!Auth::user())
            {
                return response()->json([
                    "message" => "Unauthorized"
                ], 401);
            }

            return response()->json([
                "task" => $task,
            ],200);
        }
        catch (Exception $error){
            return response()->json([
                "message" => "Unauthorized",
            ],401);
        }
    }

    public function update($id)
    {
        try {
            Auth::user()->tasks()->findOrFail($id)->update(["completed" => true]);

            return response()->json([
                "task" => Auth::user()->tasks()->findOrFail($id)
            ],200);
        }
        catch (Exception $error){
            return response()->json([
                "message" => "Unauthorized",
            ],401);
        }
    }

    public function destroy($id)
    {
        try {
            Auth::user()->tasks()->findOrFail($id)->delete();

            return response()->json([
                'status_code' => 200,
                'message' => 'Task deleted successfully!'
            ]);
        }catch (Exception $error)
        {
            return response()->json([
                "message" => "Unauthorized",
            ],401);
        }
    }
}
