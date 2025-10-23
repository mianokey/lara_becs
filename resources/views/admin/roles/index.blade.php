@extends('layouts.app')

@section('content')
<x-header />

<div class="min-h-screen bg-gray-50 mt-2 py-10">
    <div class="container mx-auto px-4">
        <!-- Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
            <!-- Card Header -->
            <div class="bg-becs-navy text-white px-6 py-4 flex items-center gap-3">
                <i class="fa-solid fa-user-shield text-xl"></i>
                <h2 class="text-xl font-semibold">
                    {{ isset($roleToEdit) ? 'Edit Role' : 'Create Role' }}
                </h2>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-3 m-4 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form -->
            <form action="{{ isset($roleToEdit) ? route('admin.roles.update', $roleToEdit->id) : route('admin.roles.store') }}"
                  method="POST" class="px-6 py-6 space-y-4">
                @csrf
                @if(isset($roleToEdit))
                    @method('PUT')
                @endif

                <div>
                    <label for="name" class="block text-gray-700 font-medium mb-1">
                        Role Name <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="name" id="name" placeholder="Enter role name (e.g., Manager)"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-becs-navy"
                           value="{{ $roleToEdit->name ?? old('name') }}" required>
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full bg-becs-navy text-white py-2 rounded hover:bg-blue-900 transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid {{ isset($roleToEdit) ? 'fa-pen' : 'fa-plus' }}"></i>
                    {{ isset($roleToEdit) ? 'Update Role' : 'Create Role' }}
                </button>
            </form>
        </div>

        <!-- Roles Table -->
        <div class="bg-white shadow-lg rounded-lg overflow-x-auto">
            <div class="bg-becs-navy text-white px-6 py-4 flex items-center gap-3">
                <i class="fa-solid fa-table-list text-xl"></i>
                <h2 class="text-xl font-semibold">All Roles</h2>
            </div>

            <table class="min-w-full table-auto border-collapse">
                <thead class="bg-becs-navy text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Role Name</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Permissions</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Created At</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Updated At</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                        <tr class="hover:bg-gray-100 even:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $role->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $role->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                @if($role->permissions->count())
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($role->permissions as $perm)
                                            <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded">
                                                {{ $perm->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-gray-500 italic">No permissions assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $role->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $role->updated_at->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 flex gap-2">
                                <a href="{{ route('admin.roles.edit', $role->id) }}" class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>
                                <form action="{{ route('admin.roles.delete', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                    @csrf
                                    @method('DELETE')
                                    <button style="background-color: red" type="submit" class="text-white px-3 py-1 rounded hover:bg-red-700">
                                        <i class="fa-solid fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
