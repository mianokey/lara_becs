@extends('layouts.app')

@section('content')
<x-header />

<div class="min-h-screen bg-becs-warm-cream py-8">
    <div class="container mx-auto px-6">
        <h1 class="text-3xl font-bold text-becs-navy mb-6">Create New Project</h1>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('projects.store') }}" method="POST" class="bg-white p-6 rounded-lg shadow space-y-6">
            @csrf

            <!-- Project Name & Code -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-sm mb-1" for="name">Project Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Enter project name" class="w-full border rounded px-3 py-2" required>
                    @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-medium text-sm mb-1" for="code">Project Code *</label>
                    <input type="text" id="code" name="code" value="{{ old('code') }}" placeholder="e.g., AHP-C1-2025" class="w-full border rounded px-3 py-2" required>
                    @error('code') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Project Type & Consortium -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium text-sm mb-1" for="type">Project Type *</label>
                    <select id="type" name="type" class="w-full border rounded px-3 py-2" required>
                        <option value="AHP" {{ old('type')=='AHP' ? 'selected' : '' }}>AHP Project</option>
                        <option value="Private" {{ old('type')=='Private' ? 'selected' : '' }}>Private Project</option>
                    </select>
                    @error('type') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-medium text-sm mb-1" for="consortium">Consortium</label>
                    <select id="consortium" name="consortium" class="w-full border rounded px-3 py-2">
                        <option value="">Select consortium</option>
                        <option value="1" {{ old('consortium')=='1' ? 'selected' : '' }}>Consortium 1</option>
                        <option value="2" {{ old('consortium')=='2' ? 'selected' : '' }}>Consortium 2</option>
                        <option value="3" {{ old('consortium')=='3' ? 'selected' : '' }}>Consortium 3</option>
                        <option value="4" {{ old('consortium')=='4' ? 'selected' : '' }}>Consortium 4</option>
                        <option value="5" {{ old('consortium')=='5' ? 'selected' : '' }}>Consortium 5</option>
                    </select>
                    @error('consortium') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Client Name (only for Private) -->
            <div id="clientDiv" class="{{ old('type')=='Private' ? '' : 'hidden' }}">
                <label class="block font-medium text-sm mb-1" for="clientName">Client Name</label>
                <input type="text" id="clientName" name="clientName" value="{{ old('clientName') }}" placeholder="Enter client name" class="w-full border rounded px-3 py-2">
                @error('clientName') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block font-medium text-sm mb-1" for="description">Description</label>
                <textarea id="description" name="description" rows="3" placeholder="Enter project description" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Status, Start Date, End Date -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block font-medium text-sm mb-1" for="status">Status</label>
                    <select id="status" name="status" class="w-full border rounded px-3 py-2" required>
                        <option value="planning" {{ old('status')=='planning' ? 'selected' : '' }}>Planning</option>
                        <option value="active" {{ old('status')=='active' ? 'selected' : '' }}>Active</option>
                        <option value="on_hold" {{ old('status')=='on_hold' ? 'selected' : '' }}>On Hold</option>
                        <option value="completed" {{ old('status')=='completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    @error('status') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-medium text-sm mb-1" for="startDate">Start Date</label>
                    <input type="date" id="startDate" name="startDate" value="{{ old('startDate') }}" class="w-full border rounded px-3 py-2">
                    @error('startDate') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-medium text-sm mb-1" for="endDate">End Date</label>
                    <input type="date" id="endDate" name="endDate" value="{{ old('endDate') }}" class="w-full border rounded px-3 py-2">
                    @error('endDate') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end pt-2">
                <button type="submit" class="bg-becs-blue text-white px-4 py-2 rounded bg-becs-navy">Create Project</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const typeSelect = document.getElementById('type');
    const clientDiv = document.getElementById('clientDiv');

    typeSelect.addEventListener('change', function () {
        if (typeSelect.value === 'Private') {
            clientDiv.classList.remove('hidden');
        } else {
            clientDiv.classList.add('hidden');
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.tom-select').forEach((select) => {
        new TomSelect(select, {
            create: false,
            sortField: 'text',
            dropdownParent: 'body',
        });
    });
});
</script>
@endsection
