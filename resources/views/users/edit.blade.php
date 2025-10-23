@extends('layouts.app')

@section('content')
<x-header />

<div class="min-h-screen bg-becs-warm-cream py-8">
    <div class="container mx-auto px-6">
        <h1 class="text-3xl font-bold text-becs-navy mb-6">Edit User</h1>

        @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow space-y-4 max-w-lg mx-auto">
            <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <!-- Full Name -->
                <div>
                    <label class="block font-medium text-sm mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="w-full border rounded px-3 py-2" required>
                    @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label class="block font-medium text-sm mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full border rounded px-3 py-2" required>
                    @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Mobile -->
                <!-- Mobile -->
                <div>
                    <label class="block font-medium text-sm mb-1">Mobile Number</label>
                    <input type="text" name="mobile" id="mobile"
                        value="{{ old('mobile', $userDetails['mobile'] ?? '') }}" placeholder="+254712345678"
                        pattern="^\+\d{1,3}\d{9}$"
                        title="Enter number in the format: +<country_code> followed by 9 digits (e.g., +254712345678)"
                        class="w-full border rounded px-3 py-2" required>
                    @error('mobile') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- WhatsApp -->
                <div>
                    <label class="block font-medium text-sm mb-1">WhatsApp Number</label>
                    <input type="text" id="whatsapp" name="whatsapp"
                        value="{{ old('whatsapp', $userDetails['whatsapp'] ?? '') }}" pattern="^\+\d{1,3}\d{9}$"
                        title="Enter number in the format: +<country_code> followed by 9 digits (e.g., +254712345678)"
                        class="w-full border rounded px-3 py-2">
                    @error('whatsapp') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>


                <!-- Role -->
                <div>
                    <label class="block font-medium text-sm mb-1">Assign Role</label>
                    <select id="roles" name="roles[]" class="w-full border rounded px-3 py-2">
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ $user->roles->pluck('name')->contains($role->name) ?
                            'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                        @endforeach
                    </select>
                    @error('roles') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-3">
                    <a href="{{ route('users.index') }}"
                        class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                        <i class="fa-solid fa-arrow-left"></i> Back
                    </a>
                    <button type="submit"
                        class="bg-becs-navy text-white px-5 py-2 rounded hover:bg-blue-900 transition flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Update User
                    </button>
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
            maxItems: 1, // single role only
            dropdownParent: 'body'
        });

        const mobileInput = document.getElementById('mobile');
        const whatsappInput = document.getElementById('whatsapp');

        mobileInput.addEventListener('input', () => {
            if (!whatsappInput.value || whatsappInput.value === '') {
                whatsappInput.value = mobileInput.value;
            }
        });
    });
</script>
@endsection