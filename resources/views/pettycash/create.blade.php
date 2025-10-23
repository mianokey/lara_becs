@extends('layouts.app')

@section('content')
<x-header />

<div class="min-h-screen bg-gray-50 mt-2 py-10">
    <div class="container mx-auto px-4">

        <!-- Request Form Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
            <div class="bg-becs-navy text-white px-6 py-4 flex items-center gap-3">
                <i class="fa-solid fa-wallet text-xl"></i>
                <h2 class="text-xl font-semibold">Request Petty Cash</h2>
            </div>

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-3 m-4 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('pettycash.store') }}" method="POST" enctype="multipart/form-data" class="px-6 py-6 space-y-4">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Request Type -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Request Type <span class="text-red-600">*</span></label>
                        <select name="request_type" id="request_type" class="w-full border border-gray-300 rounded px-3 py-2" required>
                            <option value="">Select Type</option>
                            <option value="Operational">Operational</option>
                            <option value="Project">Project</option>
                        </select>
                    </div>

                    <!-- Expense Type -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Expense Type <span class="text-red-600">*</span></label>
                        <select name="expense_type" class="w-full border border-gray-300 rounded px-3 py-2" required>
                            <option value="">Select Expense Type</option>
                            <option value="Supplies">Supplies</option>
                            <option value="Travel">Travel</option>
                            <option value="Transport">Transport</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Project / Ops Code -->
                    <div id="project_div">
                        <label class="block text-gray-700 font-medium mb-1">Project / Ops Code <span class="text-red-600">*</span></label>
                        <select name="project_id" class="w-full border border-gray-300 rounded px-3 py-2">
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Amount <span class="text-red-600">*</span></label>
                        <input type="number" name="amount" class="w-full border border-gray-300 rounded px-3 py-2" min="1" required>
                    </div>

                    <!-- Currency -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Currency <span class="text-red-600">*</span></label>
                        <select name="currency" class="w-full border border-gray-300 rounded px-3 py-2" required>
                            <option value="KES">KES</option>
                            <option value="USD">USD</option>
                        </select>
                    </div>

                    <!-- Payee Name -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Payee Name <span class="text-red-600">*</span></label>
                        <input type="text" name="payee_name" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Name of person to pay" required>
                    </div>

                    <!-- Payment Mode -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Payment Mode <span class="text-red-600">*</span></label>
                        <select name="payment_mode" class="w-full border border-gray-300 rounded px-3 py-2" required>
                            <option value="Cash">Cash</option>
                            <option value="Bank">Bank</option>
                            <option value="Mobile Money">Mobile Money</option>
                        </select>
                    </div>

                    <!-- Account Details -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Account Details</label>
                        <input type="text" name="account_details" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Bank or mobile account">
                    </div>

                    <!-- Expense Date -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Expense Date <span class="text-red-600">*</span></label>
                        <input type="date" name="expense_date" class="w-full border border-gray-300 rounded px-3 py-2" required>
                    </div>
                </div>

                <!-- Purpose / Description -->
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Purpose / Description <span class="text-red-600">*</span></label>
                    <textarea name="purpose" rows="3" class="w-full border border-gray-300 rounded px-3 py-2" required></textarea>
                </div>

                <!-- Additional Notes -->
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Additional Notes</label>
                    <textarea name="additional_notes" rows="2" class="w-full border border-gray-300 rounded px-3 py-2"></textarea>
                </div>

                <!-- Attach Receipt -->
                <div>
                    <label class="block text-gray-700 font-medium mb-1">Attach Receipt</label>
                    <input type="file" name="receipt" class="w-full border border-gray-300 rounded px-3 py-2">
                </div>

                <button type="submit" class="w-full bg-becs-navy text-white py-2 rounded hover:bg-blue-900 transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus"></i> Submit Request
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const requestType = document.getElementById('request_type');
        const projectDiv = document.getElementById('project_div');

        function toggleProject() {
            if(requestType.value === 'Operational') {
                projectDiv.style.display = 'none';
            } else {
                projectDiv.style.display = 'block';
            }
        }

        requestType.addEventListener('change', toggleProject);
        toggleProject(); // initialize on load
    });
</script>
@endsection
