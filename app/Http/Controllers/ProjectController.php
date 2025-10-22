<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\User;

class ProjectController extends Controller
{
    // Show all projects
    public function index()
    {
        $projects = Project::with('manager')->latest()->get();
        return view('projects.index', compact('projects'));
    }

    // Show create form
    public function create()
    {
        $staffUsers = User::all(); // for project manager selection
        return view('projects.create', compact('staffUsers'));
    }

    // Store new project
public function store(Request $request)
{
    // Validate the request
    $request->validate([
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:50|unique:projects,code',
        'type' => 'required|in:AHP,Private',
        'consortium' => 'nullable|string',
        'clientName' => 'nullable|string',
        'description' => 'nullable|string',
        'status' => 'required|in:planning,active,on_hold,completed',
        'startDate' => 'nullable|date',
        'endDate' => 'nullable|date',
    ]);

    // Create the project
    Project::create([
        'name' => $request->name,
        'code' => $request->code,
        'type' => $request->type,
        'consortium' => $request->consortium,
        'clientName' => $request->clientName, // matches Blade input
        'description' => $request->description,
        'status' => $request->status,
        'startDate' => $request->startDate,
        'endDate' => $request->endDate,
    ]);

    return redirect()->back()->with('success', 'Project created successfully!');
}

    // Show single project
    public function show(Project $project)
    {
        return view('projects.show', compact('project'));
    }

    // Show edit form
    public function edit(Project $project)
    {
        $staffUsers = User::all();
        return view('projects.edit', compact('project', 'staffUsers'));
    }

    // Update project
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:projects,code,' . $project->id,
            'name' => 'required|string|max:255',
            'manager_id' => 'required|exists:users,id',
            'status' => 'required|in:planned,ongoing,completed',
            'description' => 'nullable|string',
        ]);

        $project->update([
            'code' => $request->code,
            'name' => $request->name,
            'manager_id' => $request->manager_id,
            'status' => $request->status,
            'description' => $request->description,
        ]);

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully!');
    }

    // Delete project
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')
            ->with('success', 'Project deleted successfully!');
    }
}
