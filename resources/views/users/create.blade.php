@extends('layouts.app')

@section('content')
<x-header />

<div class="min-h-screen bg-becs-warm-cream py-8">
    <div class="container mx-auto px-6">
        <h1 class="text-3xl font-bold text-becs-navy mb-6">Create New User</h1>

        @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow space-y-4 max-w-lg mx-auto">
            <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Full Name -->
                <div>
                    <label class="block font-medium text-sm mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2"
                        required>
                    @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block font-medium text-sm mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2"
                        required>
                    @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Mobile -->
                <div>

                    <label class="block font-medium text-sm mb-1">Mobile Number</label>
                    <input type="text" name="mobile" id="mobile" value="{{ old('mobile') }}" placeholder="+254712345678"
                        pattern="^\+\d{1,3}\d{9}$"
                        title="Enter number in the format: +<country_code> followed by 9 digits (e.g., +254712345678)"
                        class="w-full border rounded px-3 py-2" required>
                         @error('mobile') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>


        <!-- WhatsApp -->
        <div>
            <label class="block font-medium text-sm mb-1">WhatsApp Number</label>
            <input type="text" id="whatsapp" name="whatsapp" pattern="^\+\d{1,3}\d{9}$"
                title="Enter number in the format: +<country_code> followed by 9 digits (e.g., +254712345678)"
                value="{{ old('whatsapp') }}" class="w-full border rounded px-3 py-2">
            @error('whatsapp') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <!-- Roles -->
        <div>
            <label class="block font-medium text-sm mb-1">Assign Role(s)</label>
            <select id="roles" name="roles[]" multiple class="w-full border rounded px-3 py-2">
                @foreach($roles as $role)
                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>
            @error('roles') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="bg-becs-blue text-white px-4 py-2 rounded bg-becs-navy">Create User</button>
        </div>
        </form>
    </div>
</div>
</div>
@endsection

@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
new TomSelect('#roles', {
    create: false,
    sortField: 'text',
    maxItems: 1,
    dropdownParent: 'body'
});
    const mobileInput = document.getElementById('mobile');
    const whatsappInput = document.getElementById('whatsapp');

    mobileInput.addEventListener('input', () => {
        whatsappInput.value = mobileInput.value;
    });
});
</script>
@endsection