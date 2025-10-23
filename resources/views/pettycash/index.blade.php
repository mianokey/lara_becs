@extends('layouts.app')

@section('content')
<x-header />

<div class="min-h-screen bg-gray-50 mt-4 py-10">
    <div class="container mx-auto px-4" x-data="{ tab: 'all' }">

        <!-- Tabs -->
        <div class="mb-4 flex space-x-2">
            <button @click="tab = 'all'"
                :class="tab === 'all' ? 'bg-becs-navy text-white' : 'bg-gray-200 text-gray-700'"
                class="px-4 py-2 rounded font-semibold transition-colors">
                All Requests
            </button>
            <button @click="tab = 'pending'"
                :class="tab === 'pending' ? 'bg-becs-navy text-white' : 'bg-gray-200 text-gray-700'"
                class="px-4 py-2 rounded font-semibold transition-colors">
                Pending Approvals
            </button>
        </div>

        <!-- Petty Cash Table -->
        <div class="bg-white shadow-lg rounded-lg overflow-x-auto">

            <!-- Table Header -->
            <div class="bg-becs-navy text-white px-6 py-4 flex items-center gap-3">
                <i class="fa-solid fa-wallet text-xl"></i>
                <h2 class="text-xl font-semibold"
                    x-text="tab === 'all' ? 'All Petty Cash Requests' : 'Pending Approvals'"></h2>
            </div>

            <table class="min-w-full table-auto border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium uppercase">ID</th>
                        <th class="px-4 py-2 text-left text-sm font-medium uppercase">Requestor</th>
                        <th class="px-4 py-2 text-left text-sm font-medium uppercase">Request Type</th>
                        <th class="px-4 py-2 text-left text-sm font-medium uppercase">Expense Type</th>
                        <th class="px-4 py-2 text-left text-sm font-medium uppercase">Project / Ops</th>
                        <th class="px-4 py-2 text-left text-sm font-medium uppercase">Amount</th>
                        <th class="px-4 py-2 text-left text-sm font-medium uppercase">Purpose</th>
                        <th class="px-4 py-2 text-left text-sm font-medium uppercase">Expense Date</th>
                        <th class="px-4 py-2 text-left text-sm font-medium uppercase">Payee</th>
                        <th class="px-4 py-2 text-left text-sm font-medium uppercase">Payment Mode</th>
                        <th class="px-4 py-2 text-left text-sm font-medium uppercase">Account</th>
                        <th class="px-4 py-2 text-left text-sm font-medium uppercase">Receipt</th>
                        <th class="px-4 py-2 text-left text-sm font-medium uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-sm font-medium uppercase">Approved At</th>
                        <th class="px-4 py-2 text-left text-sm font-medium uppercase">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($pettyCashes as $cash)
                    <tr x-show="tab === 'all' || (tab === 'pending' && '{{ $cash->status }}' === 'pending')"
                        class="hover:bg-gray-50 even:bg-gray-50">
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $cash->id }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $cash->user->name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $cash->request_type }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $cash->expense_type }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $cash->project?->name ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $cash->currency }} {{
                            number_format($cash->amount,2) }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $cash->purpose }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{
                            \Carbon\Carbon::parse($cash->expense_date)->format('Y-m-d') }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $cash->payee_name }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $cash->payment_mode }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $cash->account_details ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700">
                            @if($cash->receipt)
                            <a href="{{ asset('storage/'.$cash->receipt) }}" target="_blank"
                                class="text-blue-600 underline">View</a>
                            @else
                            -
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700 capitalize">
                            @if($cash->status === 'approved')
                            Approved by {{ $cash->approver?->name ?? '-' }}
                            @elseif($cash->status === 'rejected')
                            Rejected by {{ $cash->approver?->name ?? '-' }}
                            @else
                            Pending
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-700">{{ $cash->approved_at?->format('Y-m-d H:i') ?? '-'
                            }}</td>
                        <td class="px-4 py-2 text-sm text-gray-700 flex gap-2">
                            @can('approve petty cash')
                            @if($cash->status === 'pending')
                            <form action="{{ route('pettycash.approve', $cash) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="bg-green-600 text-white px-2 py-1 rounded text-sm">Accept</button>
                            </form>
                            <form action="{{ route('pettycash.reject', $cash) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" style="background-color: red"
                                    class="bg-red-600 text-white px-2 py-1 rounded text-sm">Decline</button>
                            </form>
                            @endif
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
</div>
@endsection