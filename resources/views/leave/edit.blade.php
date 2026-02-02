@extends('layouts.app')

@section('content')
<x-header />
<div class="max-w-2xl mx-auto mt-2 bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
    <h2 class="text-2xl font-semibold mb-4 text-gray-800 dark:text-gray-100">
        Adjust Leave Balances for <b> <u>{{ $user->name }} </u> </b>
    </h2>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('leave.adjust.update') }}" method="POST" class="space-y-5">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">

        @foreach($leaveTypes as $type)
            <div>
                <label class="block text-gray-700 dark:text-gray-200 font-medium mb-1">
                    {{ $type->name }}
                </label>
                <input 
                    type="number" 
                    name="balances[{{ $type->id }}]" 
                    value="{{ $userBalances[$type->id] ?? 0 }}" 
                    min="0"
                    class="w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 rounded-lg p-2 focus:ring-2 focus:ring-blue-500 focus:outline-none transition"
                >
            </div>
        @endforeach

         <div class="flex justify-end gap-3 pt-3">
                    <a href="{{ route('leave_balances.index') }}"
                        class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                        <i class="fa-solid fa-arrow-left"></i> Cancel
                    </a>
                    <button type="submit"
                        class="bg-becs-navy text-white px-5 py-2 rounded hover:bg-blue-900 transition flex items-center gap-2">
                        <i class="fa-solid fa-edit"></i> Save Changes
                    </button>
                </div>
    </form>
</div>
@endsection
