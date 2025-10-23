@extends('layouts.app')

@section('content')
<x-header />

<div class="min-h-screen bg-gray-50 py-10">
    <div class="container mx-auto px-4">
        <!-- Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-becs-navy text-white px-6 py-4 flex items-center gap-3">
                <i class="fa-solid fa-user-shield text-xl"></i>
                <h2 class="text-xl font-semibold">Edit Role & Permissions</h2>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-3 m-4 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Edit Role Form -->
            <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" class="px-6 py-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Role Name -->
                <div>
                    <label for="name" class="block text-gray-700 font-medium mb-1">
                        Role Name <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="name" id="name" placeholder="Enter role name"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-becs-navy"
                           value="{{ old('name', $role->name) }}" required>
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Permissions -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">
                        Assign Permissions
                    </h3>

                    <div class="grid md:grid-cols-4 gap-3">
                        @foreach($permissions as $permission)
                            <label class="flex items-center space-x-2 border border-gray-200 rounded p-2 hover:bg-gray-50 transition">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                    class="rounded text-becs-navy focus:ring-becs-navy"
                                    {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                <span class="text-gray-700">{{ $permission->name }}</span>
                            </label>
                        @endforeach
                    </div>

                    @if($permissions->isEmpty())
                        <p class="text-gray-500 italic mt-2">No permissions available. Create some first.</p>
                    @endif
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-3 pt-3">
                    <a href="{{ route('admin.roles.index') }}"
                       class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                        <i class="fa-solid fa-arrow-left"></i> Back
                    </a>
                    <button type="submit"
                            class="bg-becs-navy text-white px-6 py-2 rounded hover:bg-blue-900 transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
