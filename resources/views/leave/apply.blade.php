@extends('layouts.app')

@section('content')
<x-header title="Apply for Leave" />

<div class="max-w-3xl mx-auto bg-white shadow-lg rounded-lg p-6 mt-8">
    <h2 class="text-xl font-semibold mb-4 text-blue-800">Leave Application Form</h2>
    {{-- Flash Messages --}}
    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        {{ session('error') }}
    </div>
    @endif


    <!-- Applicant Details -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div>
            <label class="block text-gray-700"> <b>Name:</b> {{ auth()->user()->name }}</label>
        </div>
        <div>
            <label class="block text-gray-700"><b>Department:</b> {{ auth()->user()->department ?? 'N/A' }}</label>
        </div>
        <div>
            <label class="block text-gray-700"><b>Designation:</b> {{ auth()->user()->designation ?? 'N/A' }}</label>
        </div>
        <div>
            <label class="block text-gray-700"><b>Application Date:</b> {{ now()->format('d M Y') }} </label>
        </div>
    </div>

    <!-- Show Balances -->
    <div class="border border-gray-200 rounded-lg p-4 mb-6">
        <h3 class="font-semibold text-gray-700 mb-2">Available Leave Balances ({{ now()->year }})</h3>
        <table class="w-full border text-sm">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="p-2 text-left">Leave Type</th>
                    <th class="p-2 text-center">Total</th>
                    <th class="p-2 text-center">Used</th>
                    <th class="p-2 text-center">Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach($balances as $balance)
                <tr class="border-t">
                    <td class="p-2">{{ $balance->leaveType->name }}</td>
                    <td class="p-2 text-center">{{ $balance->total_days }}</td>
                    <td class="p-2 text-center">{{ $balance->used_days }}</td>
                    <td
                        class="p-2 text-center font-semibold {{ $balance->balance_days <= 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ $balance->balance_days }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Leave Application Form -->
    <form action="{{ route('leaves.apply.store') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label for="leave_type_id" class="block text-gray-700">Leave Type</label>


            <select name="leave_type_id" id="leave_type_id" class="tom-select  w-full border rounded px-3 py-2"
                required>
                <option value="">-- Select Leave Type --</option>
                @foreach($leaveTypes as $type)
                <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700">Start Date</label>
                <input type="date" name="start_date" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block text-gray-700">End Date</label>
                <input type="date" name="end_date" class="w-full border border-gray-300 rounded px-3 py-2" required>
            </div>
        </div>

        <div>
            <label class="block text-gray-700">Reason for Leave</label>
            <textarea name="reason" rows="3" class="w-full border border-gray-300 rounded px-3 py-2"
                placeholder="Briefly explain your reason" required></textarea>
        </div>

        <!-- Dynamic Extra Fields Placeholder -->
        <div id="extra-fields">
            {{-- Later, you can inject dynamic partials here like @include('leave.partials.maternity') --}}
        </div>

        <div class="flex justify-end">
            <div class="flex justify-end gap-3 pt-3">
                <a href="{{ route('leaves.history') }}"
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition">
                    <i class="fa-solid fa-arrow-left"></i> View My Leave History
                </a>
                <button type="submit"
                    class="bg-becs-navy text-white px-5 py-2 rounded hover:bg-blue-900 transition flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Submit Request
                </button>
            </div>
        </div>
    </form>
</div>
@endsection