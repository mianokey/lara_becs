@extends('layouts.app')

@php
function taskBorderColor($status) {
return match($status) {
'not_started' => 'border-gray-400',
'in_progress' => 'border-yellow-500',
'under_review' => 'border-blue-500',
'completed' => 'border-green-600',
default => 'border-gray-300',
};
}

@endphp

@section('content')
<div class="min-h-screen bg-becs-warm-cream flex flex-col">
    <x-header />

    <div class="container mx-auto px-6 py-8 flex-1">

        {{-- ========= NORMAL USER VIEW ========= --}}
        @if(!$user->hasAnyRole(['admin', 'director']))
        <h2 class="text-2xl font-bold text-becs-text-primary mb-4">My Tasks</h2>

        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full text-sm text-left text-gray-700 border-collapse">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Task Id</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Title</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Project</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Type</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Task Desc</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Priority</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Weekly Deliverable</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Target Date</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Reviewer</th>
                        <th class="px-4 py-3 text-right text-sm font-semibold">Actions</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold">Update</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($tasks as $task)
                    <tr class="
                        {{ 
                            $task->status === 'submitted' ? 'bg-yellow-100 ' : 
                            ($task->status === 'completed' ? 'bg-green-100 ' : 
                            ($task->status === 'review_task' ? 'bg-blue-100 ' : 'bg-gray-100 '))
                        }}

                    ">

                        <td class="px-4 py-3 font-medium">{{ $task->id }}</td>
                        <td class="px-4 py-3 font-medium">{{ $task->title }}</td>
                        <td class="px-4 py-3">{{ $task->project->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $task->project->type ?? '-' }}</td>
                        <td class="px-4 py-3 font-medium">{{ $task->description }}</td>
                        <td class="px-4 py-3">{{ $task->priority ?? '-' }}</td>
                        <td
                            class="px-4 py-3 {{ $task->status === 'submitted' ? 'bg-yellow-100 text-gray-700 font-medium' : '' }}">
                            {{ $task->is_weekly_deliverable == 1 ? 'Yes' : '-' }}
                        </td>

                        <td class="px-4 py-3">{{ $task->status ?? '-' }}</td>

                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($task->target_completion_date)->format('M d, Y')
                            }}</td>
                        <td class="px-4 py-3">{{ $task->reviewer_id ? $task->reviewer->name : '-' }}</td>
                        <td class="px-4 py-3 text-right">
                            <button onclick="openTaskModal({{ $task->id }})" class="text-blue-600 text-blue-800"><i
                                    class="fas fa-eye"></i></button>
                        </td>
                        <td class="px-4 py-3">
                            <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                @php
                                $disabled = in_array($task->status, ['submitted', 'completed']);
                                $isAdmin = auth()->user()->hasAnyRole(['admin', 'director']); // adjust roles
                                @endphp

                                @if($disabled)
                                <p class="text-sm font-medium text-gray-600 capitalize">{{ $task->status }}</p>
                                @else
                                <select name="status"
                                    class="border-gray-300 text-sm rounded px-2 py-1 bg-white focus:ring focus:ring-becs-navy/30"
                                    onchange="this.form.submit()">

                                    <option value="not_started" {{ $task->status == 'not_started' ? 'selected' : ''
                                        }}>Not Started</option>
                                    <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' : ''
                                        }}>In Progress</option>

                                    {{-- Review Task: visible but not selectable for non-admins --}}
                                    <option value="review_task" {{ $task->status == 'review_task' ? 'selected' : '' }}
                                        {{ !$isAdmin ? 'disabled class=text-gray-400' : '' }}>
                                        Review Task
                                    </option>

                                    <option value="submitted" {{ $task->status == 'submitted' ? 'selected' : ''
                                        }}>Submitted</option>
                                </select>
                                @endif
                            </form>



                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-6 text-gray-500">No tasks yet</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        {{-- ========= ADMIN / DIRECTOR VIEW ========= --}}
        @role('admin|director')
        <div x-data="{ tab: 'all' }" class="mt-8">
            <div class="flex gap-4 border-b border-gray-300 mb-4">
                <button @click="tab='review'" :class="tab==='review'?'border-b-2 border-becs-maroon font-bold':''"
                    class="px-3 py-2">Review Tasks</button>
                <button @click="tab='all'" :class="tab==='all'?'border-b-2 border-becs-maroon font-bold':''"
                    class="px-3 py-2">All Tasks</button>
                <button @click="tab='byUser'" :class="tab==='byUser'?'border-b-2 border-becs-maroon font-bold':''"
                    class="px-3 py-2">By Employee</button>

            </div>

            {{-- ALL TASKS TAB --}}
            <div x-show="tab==='all'" class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full text-sm text-left text-gray-700 border-collapse">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left">Title</th>
                            <th class="px-4 py-3 text-left">Project</th>
                            <th class="px-4 py-3 text-left">Code</th>
                            <th class="px-4 py-3 text-left">Type</th>
                            <th class="px-4 py-3 text-left">Description</th>
                            <th class="px-4 py-3 text-left">Assignee</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Target Date</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($tasks as $task)
                        <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-4 py-3 font-medium">{{ $task->title }}</td>
                            <td class="px-4 py-3">{{ $task->project->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $task->project->code ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $task->project->type ?? '-' }}</td>
                            <td class="px-4 py-3">{{ Str::limit($task->project->description, 40) ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $task->assignee->name ?? 'Unassigned' }}</td>
                            <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $task->status)) }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($task->target_completion_date)->format('M d,
                                Y') }}</td>
                            <td class="px-4 py-3 text-right flex gap-2 justify-end">
                                <button onclick="openTaskModal({{ $task->id }})"
                                    class="text-blue-600 hover:text-blue-800"><i class="fas fa-eye"></i></button>
                                <a href="{{ route('tasks.edit', $task->id) }}"
                                    class="text-yellow-600 hover:text-yellow-800"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- BY USER TAB --}}
            <div x-show="tab==='byUser'" class="mt-4 space-y-6">
                <div class="flex justify-end mb-3">
                    <select id="userFilter" class="border-gray-300 rounded p-2 text-sm">
                        <option value="">All Employees</option>
                        @foreach($users as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="userTaskCards" class="space-y-4">
                    @foreach($users as $employee)
                    @php
                    $totalTasks = $employee->tasks->count();
                    $completedTasks = $employee->tasks->where('status', 'completed')->count();
                    $inProgressTasks = $employee->tasks->where('status', 'in_progress')->count();
                    $underReviewTasks = $employee->tasks->where('status', 'under_review')->count();

                    // Weight calculation: in_progress = 50%, under_review = 100%, completed = 100%
                    $weightedScore = ($completedTasks + $underReviewTasks + ($inProgressTasks * 0.5));
                    $performance = $totalTasks > 0 ? round(($weightedScore / $totalTasks) * 100) : 0;

                    @endphp

                    <div class="employee-card bg-white shadow-md rounded-lg p-4 w-full"
                        data-user-id="{{ $employee->id }}">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="text-lg font-bold text-becs-navy">{{ $employee->name }}</h3>
                            <span class="text-sm text-gray-500">
                                Performance: <b class="text-green-700">{{ $performance }}%</b>
                            </span>
                        </div>

                        @if($totalTasks > 0)
                        <ul class="divide-y">
                            @foreach($employee->tasks as $task)
                            <li class="pl-3 py-2 mb-2 mt-2 border-l-4 {{ taskBorderColor($task->status) }}">
                                <div class="flex justify-between p-3 items-center flex-wrap gap-2">
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $task->title }}</p>
                                        <p class="text-sm text-gray-500">
                                            Project: {{ $task->project->name ?? '-' }}<br>
                                            <span class="italic text-xs text-gray-400">
                                                Status: {{ ucfirst(str_replace('_',' ',$task->status)) }}
                                            </span>
                                        </p>
                                    </div>

                                    {{-- Only allow status updates for the task assignee --}}
                                    @if(auth()->id() === $task->assignee_id)
                                    <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST"
                                        class="flex-shrink-0">
                                        @csrf
                                        @method('PUT')
                                        <select name="status"
                                            class="text-sm border-gray-300 rounded-md focus:ring-becs-navy focus:border-becs-navy"
                                            onchange="this.form.submit()">
                                            <option value="not_started" {{ $task->status == 'not_started' ? 'selected' :
                                                '' }}>Not Started</option>
                                            <option value="in_progress" {{ $task->status == 'in_progress' ? 'selected' :
                                                '' }}>In Progress</option>
                                            <option value="under_review" {{ $task->status == 'review_task' ? 'selected'
                                                : '' }}>Review Task</option>
                                        </select>
                                    </form>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <p class="text-gray-400 text-sm">No tasks assigned.</p>
                        @endif
                    </div>
                    @endforeach
                </div>

            </div>

            <div x-show="tab==='review'" class="mt-4 bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full text-sm text-left text-gray-700 border-collapse">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left">Title</th>
                            <th class="px-4 py-3 text-left">Project</th>
                            <th class="px-4 py-3 text-left">Assignee</th>
                            <th class="px-4 py-3 text-left">Status</th>
                            <th class="px-4 py-3 text-left">Target Date</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($tasks->where('status', 'submitted') as $task)
                        <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="px-4 py-3 font-medium">{{ $task->title }}</td>
                            <td class="px-4 py-3">{{ $task->project->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $task->assignee->name ?? 'Unassigned' }}</td>
                            <td class="px-4 py-3 text-blue-700 font-semibold">{{ ucfirst(str_replace('_',' ',
                                $task->status)) }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($task->target_completion_date)->format('M d,
                                Y') }}</td>
                            <td class="px-4 py-3 text-right flex justify-end gap-2">
                                <button onclick="openTaskModal({{ $task->id }})"
                                    class="text-blue-600 hover:text-blue-800"><i class="fas fa-eye"></i></button>

                                {{-- Optional: Approve or Send Back --}}
                                <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="text-green-600 hover:text-green-800"><i
                                            class="fas fa-check"></i></button>
                                </form>

                                <form action="{{ route('tasks.updateStatus', $task->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="review_task">
                                    <button type="submit" class="text-red-600 hover:text-red-800"><i
                                            class="fas fa-undo"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-gray-500">No tasks under review</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @endrole
    </div>
</div>

<!-- Task Chat Modal -->
<div id="taskModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl flex flex-col h-[80vh]">
        <!-- Header -->
        <div class="flex justify-between items-center border-b px-4 py-2 bg-becs-blue text-white rounded-t-lg">
            <h2 class="text-xl font-bold" id="modalTitle">Task Chat</h2>
            <button onclick="closeTaskModal()" style="color: brown" class="">&times;</button>
        </div>

        <!-- Task Info -->
        <div class="p-4 border-b flex flex-col gap-1 bg-gray-50">
            <p><strong>Project:</strong> <span id="modalProject"></span></p>
            <p><strong>Assigned To:</strong> <span id="modalAssignee"></span></p>
            <p><strong>Status:</strong> <span id="modalStatus"></span></p>
            <p><strong>Due Date:</strong> <span id="modalDueDate"></span></p>
        </div>

        <!-- Chat messages -->
        <div id="chatBox" style="max-height:150px;overflow-x:auto;" class="flex-1 p-4 space-y-2 bg-gray-100">
            <!-- Messages will be dynamically added here -->
        </div>

        <!-- Message input -->
        <div class="flex p-4 gap-2 border-t bg-white">
            <input type="text" id="newMessage" class="flex-1 border rounded-full px-4 py-2 focus:outline-none"
                placeholder="Type a message...">
            <button id="sendButton" class="bg-becs-navy text-white px-4 py-2 rounded-full flex items-center gap-2"
                onclick="sendMessage()">
                <span id="sendText">Send</span>
                <i id="sendIcon" class="fa fa-paper-plane"></i>
                <i id="loadingIcon" style="display: none" class="fa fa-spinner fa-spin hidden"></i>
            </button>
        </div>
    </div>
</div>




<script>
    document.getElementById('userFilter')?.addEventListener('change', function() {
    const userId = this.value;
    document.querySelectorAll('.employee-card').forEach(card => {
        card.style.display = !userId || card.dataset.userId === userId ? '' : 'none';
    });
});

const tasks = @json($tasks);

function openTaskModal(taskId) {
    const task = tasks.find(t => t.id === taskId);
    if (!task) return;

    document.getElementById('modalTitle').textContent = task.title;
    document.getElementById('modalProject').textContent = task.project?.name ?? 'N/A';
    document.getElementById('modalAssignee').textContent = task.assignee?.name ?? 'Unassigned';
    document.getElementById('modalStatus').textContent = task.status.replace('_',' ').toUpperCase();
    document.getElementById('modalDueDate').textContent = new Date(task.target_completion_date).toLocaleDateString();

    loadMessages(taskId);

    // Show modal
    const modal = document.getElementById('taskModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeTaskModal() {
    const modal = document.getElementById('taskModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function loadMessages(taskId) {
    const chatBox = document.getElementById('chatBox');
    chatBox.innerHTML = '';

    fetch(`/tasks/${taskId}/messages`)
        .then(res => res.json())
        .then(data => {
            data.forEach(msg => {
                const bubble = document.createElement('div');
                bubble.classList.add('chat-bubble');

                if (msg.user_id === {{ auth()->id() }}) {
                    bubble.classList.add('user');
                    bubble.innerHTML = `${msg.message} ${msg.read_at ? '<span class="tick">&#10003;&#10003;</span>' : ''}`;
                } else {
                    bubble.classList.add('other');
                    bubble.innerHTML = `<strong>${msg.user_name}:</strong> ${msg.message}`;
                }

                chatBox.appendChild(bubble);
            });

            chatBox.scrollTop = chatBox.scrollHeight;
        });
}


function sendMessage() {
    const input = document.getElementById('newMessage');
    const message = input.value.trim();
    if (!message) return;

    const sendBtn = document.getElementById('sendButton');
    const sendText = document.getElementById('sendText');
    const sendIcon = document.getElementById('sendIcon');
    const loadingIcon = document.getElementById('loadingIcon');

    // Show loading state
    sendBtn.disabled = true;
    sendText.textContent = 'Sending...';
    sendIcon.style.display = 'none'; // hide
    loadingIcon.style.display = 'inline-block';

    const taskId = tasks.find(t => t.title === document.getElementById('modalTitle').textContent).id;

    fetch(`/tasks/${taskId}/messages`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message })
    })
    .then(res => res.json())
    .then(() => {
        loadMessages(taskId);
        input.value = '';
    })
    .catch(err => console.error(err))
    .finally(() => {
        // Hide loading state
        sendBtn.disabled = false;
        sendText.textContent = 'Send';
        sendIcon.style.display = 'inline-block'; // show
        loadingIcon.style.display = 'none'; // hide
    });
}
</script>
@endsection