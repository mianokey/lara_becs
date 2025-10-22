@extends('layouts.app')

@section('content')
<x-header />

<div class="min-h-screen bg-becs-warm-cream py-8">
    <div class="container mx-auto px-6">
        <h1 class="text-3xl font-bold text-becs-navy mb-6">Assign New Task</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('tasks.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow space-y-6">
            @csrf

            <!-- 3-column grid for Task Title, Assign To, Reviewer -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Task Title -->
                <div>
                    <label class="block font-medium text-sm mb-1">Task Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="w-full border rounded px-3 py-2" required>
                    @error('title') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Assign To -->
                <div>
                    <label class="block font-medium text-sm mb-1">Assign To</label>
                    <select id="assignee" name="assignee_id" class="tom-select w-full border rounded px-3 py-2" required>
                        <option value="">Select Staff Member</option>
                        @foreach($staffUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->id }} - {{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('assignee_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Reviewer -->
                <div>
                    <label class="block font-medium text-sm mb-1">Reviewer (Optional)</label>
                    <select id="reviewer" name="reviewer_id" class="tom-select w-full border rounded px-3 py-2">
                        <option value="">Select Reviewer</option>
                        @foreach($reviewers as $user)
                            <option value="{{ $user->id }}">{{ $user->id }} - {{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('reviewer_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- 2-column grid for Project and Due Date -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Project -->
                <div>
                    <label class="block font-medium text-sm mb-1">Project</label>
                    <select id="project" name="project_id" class="tom-select w-full border rounded px-3 py-2" required>
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->code }} - {{ $project->name }} [{{ ucfirst($project->status) }}]</option>
                        @endforeach
                    </select>
                    @error('project_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Due Date -->
                <div>
                    <label class="block font-medium text-sm mb-1">Due Date</label>
                    <input type="date" name="target_completion_date" value="{{ old('target_completion_date') }}" class="w-full border rounded px-3 py-2" required>
                    @error('target_completion_date') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Priority -->
            <div>
                <label class="block font-medium text-sm mb-1">Priority</label>
                <select name="priority" class="w-full border rounded px-3 py-2">
                    <option value="low" {{ old('priority')=='low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority')=='medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority')=='high' ? 'selected' : '' }}>High</option>
                </select>
            </div>

            <!-- Description -->
            <div>
                <label class="block font-medium text-sm mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
            </div>

            <!-- Weekly Deliverable -->
            <input type="hidden" name="is_weekly_deliverable" value="0">
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_weekly_deliverable" id="weekly" value="1" class="h-4 w-4">
                <label for="weekly" class="text-sm font-medium">Weekly Deliverable</label>
            </div>


            <!-- Submit -->
            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-becs-blue text-white px-4 py-2 rounded bg-becs-navy">Assign Task</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<!-- Tom Select -->
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

<style>
/* Ensure dropdowns appear above everything */
.ts-dropdown {
    z-index: 9999 !important;
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.tom-select').forEach((select) => {
        new TomSelect(select, {
            create: false,
            sortField: 'text',
            dropdownParent: 'body', // ensures dropdown renders above everything
        });
    });
});

</script>
@endsection
