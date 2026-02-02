@extends('layouts.app')

@section('content')
<x-header title="Manage Leave Applications" />

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

    {{-- Leave Applications --}}
    <div class="space-y-4">
        @forelse($leaves as $leave)
            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-800">{{ $leave->user->name }} - {{ $leave->leaveType->name }}</h3>
                        <p class="text-gray-600 text-sm">
                            <strong>Start:</strong> {{ \Carbon\Carbon::parse($leave->start_date)->format('d M Y') }} &nbsp;|&nbsp;
                            <strong>End:</strong> {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }} &nbsp;|&nbsp;
                            <strong>Days:</strong> {{ $leave->days_requested }}
                        </p>
                        <p class="text-gray-600 text-sm"><strong>Reason:</strong> {{ $leave->reason }}</p>
                    </div>

                    <div class="mt-3 md:mt-0">
                        @if($leave->status === 'pending')
                            <form action="{{ route('leaves.manage.update', $leave->id) }}" method="POST" class="flex flex-col md:flex-row md:items-center gap-2">
                                @csrf
                                @method('PUT')

                                {{-- Approve Button --}}
                                <button type="submit" name="status" value="approved"
                                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                                    Approve
                                </button>

                                {{-- Decline Button --}}
                                <button type="button" 
                                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition decline-btn"
                                    data-leave-id="{{ $leave->id }}">
                                    Decline
                                </button>

                                {{-- Hidden Decline Message --}}
                                <div class="mt-2 hidden decline-message" id="decline-message-{{ $leave->id }}">
                                    <textarea name="decline_reason" rows="2" class="w-full border rounded px-2 py-1" placeholder="Enter reason for declining"></textarea>
                                    <button type="submit" name="status" value="rejected"
                                        class="mt-1 bg-red-800 text-white px-3 py-1 rounded hover:bg-red-900 transition">
                                        Submit Decline
                                    </button>
                                </div>
                            </form>
                        @else
                            <span class="px-3 py-1 rounded-full 
                                {{ $leave->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-sm font-semibold">
                                {{ ucfirst($leave->status) }}
                            </span>
                            @if($leave->status === 'rejected' && $leave->remarks)
                                <p class="text-red-700 text-sm mt-1"><strong>Reason:</strong> {{ $leave->remarks }}</p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-600">No leave applications found.</p>
        @endforelse
    </div>
</div>

{{-- Script to toggle decline message --}}
<script>
    document.querySelectorAll('.decline-btn').forEach(button => {
        button.addEventListener('click', function() {
            const leaveId = this.dataset.leaveId;
            const msgDiv = document.getElementById('decline-message-' + leaveId);
            msgDiv.classList.toggle('hidden');
        });
    });
</script>
@endsection
