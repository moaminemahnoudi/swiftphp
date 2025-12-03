<?php

namespace App\Controllers;

use App\Models\Task;
use SwiftPHP\Core\Controller;
use SwiftPHP\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        return $this->json($tasks);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|min:3'
        ]);

        $task = Task::create($data);
        return $this->json($task, 201);
    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id);
        if (!$task) {
            return $this->json(['error' => 'Task not found'], 404);
        }

        $task->update($request->all());
        return $this->json($task);
    }

    public function destroy($id)
    {
        Task::destroy($id);
        return $this->json(['message' => 'Task deleted']);
    }
}
