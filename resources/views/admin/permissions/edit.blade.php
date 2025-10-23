@extends('layouts.app')

@section('content')
<x-header />

<div class="min-h-screen bg-gray-50 mt-2 py-10">
    <div class="container mx-auto px-4">

        <!-- Edit Permission Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
            <!-- Header -->
            <div class="bg-becs-navy text-white px-6 py-4 flex items-center gap-3">
                <i class="fa-solid fa-pen-to-square text-xl"></i>
                <h2 class="text-xl font-semibold">Edit Permission</h2>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-3 m-4 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Edit Form -->
            <form action="{{ route('admin.permissions.update', $permission->id) }}" method="POST" class="px-6 py-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-gray-700 font-medium mb-1">
                        Permission Name <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="name" id="name"
                        placeholder="Enter permission name (e.g., approve petty cash)"
                        class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-becs-navy"
                        value="{{ old('name', $permission->name) }}" required>
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-3">
                    <a href="{{ route('admin.permissions.index') }}"
                       class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                        <i class="fa-solid fa-arrow-left"></i> Back
                    </a>
                    <button type="submit"
                            class="bg-becs-navy text-white px-5 py-2 rounded hover:bg-blue-900 transition flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Update Permission
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection