@extends('layouts.app')

@section('content')
<x-header />

<div class="min-h-screen bg-gray-50  mt-2 py-10">
    <div class="container mx-auto px-4">
        <!-- Card Header -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
            <div class="bg-becs-navy text-white px-6 py-4 flex items-center gap-3">
                <i class="fa-solid fa-users text-xl"></i>
                <h2 class="text-xl font-semibold">All Users</h2>
            </div>

            <!-- Success Message -->
            @if(session('success'))
            <div class="bg-green-100 text-green-800 p-3 m-4 rounded">
                {{ session('success') }}
            </div>
            @endif

            <!-- Users Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse">
                    <thead class="bg-becs-navy text-white">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Roles</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-100 even:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-medium">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $user->email }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                @if($user->roles->isEmpty())
                                <span class="text-gray-400 italic">No role</span>
                                @else
                                @foreach($user->roles as $role)
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">
                                    {{ $role->name }}
                                </span>
                                @endforeach
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 text-sm flex gap-2">
                                <a href="{{ route('users.edit', $user->id) }}"
                                    class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                    <i class="fa-solid fa-user-pen"></i> Edit Roles
                                </a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to archive this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">🗑️ Archive</button>
                                </form>
                                <form action="{{ route('users.resetPassword', $user->id) }}" method="POST"
                                    onsubmit="return confirm('Are you sure you want to reset this user’s password?');">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:text-blue-800">
                                        🔄 Reset Password
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
</div>
@endsection