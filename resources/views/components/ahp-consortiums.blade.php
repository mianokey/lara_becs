@props(['projects'])

@php
if (!function_exists('getStatusColor')) {
    function getStatusColor($status) {
        return match($status) {
            'planning' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'active' => 'bg-blue-100 text-blue-800 border-blue-200',
            'on_hold' => 'bg-gray-100 text-gray-800 border-gray-200',
            'completed' => 'bg-green-100 text-green-800 border-green-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }
}

$consortiumNames = ['Consortium 1', 'Consortium 2', 'Consortium 3', 'Consortium 4', 'Consortium 5'];

$projectsByConsortium = [];
foreach ($consortiumNames as $name) {
    $number = preg_replace('/[^0-9]/', '', $name);
    $projectsByConsortium[$name] = $projects->filter(fn($p) => preg_replace('/[^0-9]/', '', $p->consortium) == $number);
}
@endphp

<div class="space-y-4 bg-secondary p-6 rounded-lg shadow mb-3">
    <h2 class="text-2xl font-bold mb-2">AHP Consortiums</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
        @foreach($consortiumNames as $name)
            @php $consortiumProjects = $projectsByConsortium[$name] ?? collect(); @endphp
            <div class="bg-white rounded-lg shadow border-l-4 border-becs-blue">
                <button
                    type="button"
                    class="w-full px-4 py-3 text-left flex justify-between items-center focus:outline-none consortium-btn"
                    data-target="consortium-{{ Str::slug($name) }}"
                >
                    <span class="font-semibold">{{ $name }} ({{ $consortiumProjects->count() }})</span>
                    <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="consortium-{{ Str::slug($name) }}" style="overflow-y:auto; max-height:150px;" class="px-4 py-2 space-y-2 hidden">
                    @forelse($consortiumProjects as $project)
                        @php $tasks = $project->tasks ?? collect(); @endphp
                        <div class="border-l-4 p-2 rounded {{ getStatusColor($project->status) }}">
                            <div class="flex justify-between items-center">
                                <div class="flex-1 flex flex-col truncate">
                                    <h3 class="font-semibold text-becs-text-primary truncate">{{ $project->name }}</h3>
                                    <p class="text-gray-600 text-sm truncate">{{ $project->description ?? 'No description' }}</p>
                                    {{-- Inline tasks --}}
                                    @if($tasks->count())
                                        <div class="mt-1 overflow-x-auto whitespace-nowrap flex gap-1">
                                            @foreach($tasks as $task)
                                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium {{ getStatusColor($task->status) }}">
                                                    {{ $task->title }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-gray-500 text-xs mt-1">No tasks assigned.</p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ getStatusColor($project->status) }}">
                                        {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                                    </span>
                                    <a href="{{ route('projects.show', $project->id) }}" class="text-becs-blue hover:text-becs-navy">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm text-center py-4">No projects in this consortium.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
document.querySelectorAll('.consortium-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const targetId = btn.dataset.target;
        const target = document.getElementById(targetId);

        // Close all other consortiums
        document.querySelectorAll('[id^="consortium-"]').forEach(el => {
            if(el.id !== targetId) el.classList.add('hidden');
        });

        // Toggle this one
        target.classList.toggle('hidden');

        // Rotate icon
        document.querySelectorAll('.consortium-btn svg').forEach(svg => svg.classList.remove('rotate-180'));
        const icon = btn.querySelector('svg');
        if(icon && !target.classList.contains('hidden')) icon.classList.add('rotate-180');
    });
});
</script>
