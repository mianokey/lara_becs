@extends('layouts.app')

@section('content')
<x-header />

<div class="max-w-xl mx-auto mt-10 bg-white shadow-md rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">Select User to Adjust Leave Balances</h2>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 border border-green-400 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Error Message --}}
    @if (session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-800 border border-red-400 rounded">
            {{ session('error') }}
        </div>
    @endif

    <form id="selectUserForm" method="GET" onsubmit="return goToEdit(event)">
        <div class="mb-4">
            <label for="user" class="block text-gray-700">Select User</label>
            <select name="user" id="user" class="tom-select w-full border border-gray-300 rounded p-2">
                <option value="">-- Select User --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex justify-end gap-3 pt-3">
                    <a href="{{ route('home') }}"
                        class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                        <i class="fa-solid fa-arrow-left"></i> Back
                    </a>
                    <button type="submit"
                        class="bg-becs-navy text-white px-5 py-2 rounded hover:bg-blue-900 transition flex items-center gap-2">
                        <i class="fa-solid fa-edit"></i> Edit Leave Balances
                    </button>
                </div>
    </form>
</div>

<script>
function goToEdit(event) {
    event.preventDefault();
    const userId = document.getElementById('user').value;
    if (userId) {
        window.location.href = `/leave-balances/${userId}/edit`;
    } else {
        alert('Please select a user first.');
    }
}
</script>
@endsection
