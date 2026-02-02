@extends('layouts.app')

@section('content')
<x-header title="My Leave History" />

<div class="max-w-4xl mx-auto mt-8">
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

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Leave History Card --}}
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h2 class="text-2xl font-semibold text-blue-800 mb-6">Leave Applications</h2>

        @if($leaves->isEmpty())
            <p class="text-gray-600">You have not applied for any leaves yet.</p>
        @else
            <div class="space-y-4">
                @foreach($leaves as $leave)
                    <div class="border border-gray-200 rounded-lg p-4 flex flex-col md:flex-row md:justify-between md:items-center hover:shadow-md transition">
                        <div class="flex-1">
                            <h3 class="font-semibold text-gray-800">{{ $leave->leaveType->name }}</h3>
                            <p class="text-gray-600 text-sm">
                                <strong>Start:</strong> {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }} &nbsp;|&nbsp;
                                <strong>End:</strong> {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }} &nbsp;|&nbsp;
                                <strong>Return:</strong> {{ \Carbon\Carbon::parse($leave->return_date)->format('d M Y') }}
                            </p>
                            <p class="text-gray-600 text-sm"><strong>Days Requested:</strong> {{ $leave->days_requested }}</p>
                            <p class="text-gray-600 text-sm"><strong>Reason:</strong> {{ $leave->reason }}</p>
                        </div>
                        <div class="mt-3 md:mt-0 text-right">
                            @if($leave->status === 'approved')
                                <span class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-sm font-semibold">Approved</span>
                            @elseif($leave->status === 'pending')
                                <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-sm font-semibold">Pending</span>
                            @else
                                <span class="px-3 py-1 rounded-full bg-red-100 text-red-800 text-sm font-semibold">Rejected</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
