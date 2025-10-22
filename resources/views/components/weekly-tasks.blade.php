<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-3">

    {{-- Pending Tasks --}}
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-becs-text-primary mb-4 flex items-center gap-2">
            <i class="fas fa-tasks text-blue-500"></i> Pending Weekly Tasks
        </h3>

        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full text-sm text-left text-gray-700 border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold text-gray-700">Title</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Target Date</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Assignee</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Priority</th>
                        <th class="px-4 py-3 font-semibold text-gray-700">Status</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($incompleteTasks as $index => $task)
                    <tr class="hover:bg-gray-50 {{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $task->title }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ optional($task->target_completion_date)->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $task->assignee->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $task->priority }}</td>
                        <td class="px-4 py-3 capitalize text-gray-700">{{ $task->status ?? 'pending' }}</td>
                        <td class="px-4 py-3 text-right flex flex-wrap justify-end gap-2">
                            {{-- View --}}
                            <a href="{{ route('tasks.show', $task->id) }}"
                                class="text-blue-600 hover:text-blue-800" title="View Task">
                                <i class="fas fa-eye"></i>
                            </a>

                            @role('admin|director')
                            {{-- Edit --}}
                            <a href="{{ route('tasks.edit', $task->id) }}"
                                class="text-yellow-600 hover:text-yellow-800" title="Edit Task">
                                <i class="fas fa-edit"></i>
                            </a>

                            {{-- Delete --}}
                            <form method="POST" action="{{ route('tasks.destroy', $task->id) }}"
                                onsubmit="return confirm('Are you sure you want to delete this task?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" title="Delete Task">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @else
                            {{-- Normal user status dropdown --}}
                            <form method="POST" action="{{ route('tasks.updateStatus', $task->id) }}">
                                @csrf @method('PATCH')
                                <select name="status" onchange="this.form.submit()"
                                    class="text-sm border-gray-300 rounded-md bg-gray-50 text-gray-700 focus:ring focus:ring-blue-200 cursor-pointer hover:bg-gray-100 transition">
                                    <option disabled selected>{{ ucfirst($task->status ?? 'pending') }}</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="submitted">Submit</option>
                                    @role('admin|director')
                                    <option value="completed">Completed</option>
                                    @endrole
                                </select>
                            </form>
                            @endrole
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                            No pending tasks 🎯
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Completed Tasks --}}
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-becs-text-primary mb-4 flex items-center gap-2">
            <i class="fas fa-check-circle text-green-500"></i> Completed This Week
        </h3>

        <div class="space-y-2">
            @forelse($completedTasks as $task)
            <div class="border-b py-2">
                <p class="font-medium text-gray-700">{{ $task->title }}</p>
                <p class="text-sm text-gray-500">Completed {{ $task->updated_at->diffForHumans() }}</p>
                @role('admin|director')
                <p class="text-xs text-gray-400">Assignee: {{ $task->assignee->name ?? 'N/A' }}</p>
                @endrole
            </div>
            @empty
            <p class="text-gray-500 text-sm">No completed tasks yet ✅</p>
            @endforelse
        </div>
    </div>

</div>
