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
// Filter only private projects
$privateProjects = $projects->filter(fn($p) => $p->type === 'Private');

@endphp

<div class="space-y-4 bg-secondary p-6 rounded-lg shadow mb-3">
    <h2 class="text-2xl font-bold mb-2">Private Projects</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        @forelse($privateProjects as $project)
            @php $tasks = $project->tasks ?? collect(); @endphp
            <div class="bg-white rounded-lg shadow border-l-4 border-becs-blue">
                <button
                    type="button"
                    class="w-full px-4 py-3 text-left flex justify-between items-center focus:outline-none project-btn"
                    data-target="project-{{ $project->id }}"
                >
                    <span class="font-semibold">{{ $project->name }} ({{ $tasks->count() }} tasks)</span>
                    <svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="project-{{ $project->id }}" style="overflow-y:auto; max-height:200px;" class="px-4 py-2 space-y-2 hidden">
                    <p class="text-gray-600 text-sm truncate">{{ $project->description ?? 'No description' }}</p>
                    <p class="text-gray-500 text-sm">Client: {{ $project->client_name ?? 'N/A' }}</p>
                    <p class="text-gray-500 text-sm">Status: <span class="font-semibold {{ getStatusColor($project->status) }}">{{ ucfirst(str_replace('_',' ',$project->status)) }}</span></p>
                    <p class="text-gray-500 text-sm">Start Date: {{ $project->startDate ?? 'N/A' }}</p>
                    <p class="text-gray-500 text-sm">End Date: {{ $project->endDate ?? 'N/A' }}</p>

                    @if($tasks->count())
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach($tasks as $task)
                                <span class="inline-block px-2 py-0.5 rounded text-xs font-medium {{ getStatusColor($task->status) }}">
                                    {{ $task->title }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-xs mt-1">No tasks assigned.</p>
                    @endif

                    <a href="{{ route('projects.show', $project->id) }}" class="text-becs-blue hover:text-becs-navy text-sm mt-2 inline-block">View Project Details</a>
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-sm text-center py-4">No private projects found.</p>
        @endforelse
    </div>
</div>

<script>
document.querySelectorAll('.project-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const targetId = btn.dataset.target;
        const target = document.getElementById(targetId);

        // Close all other project details
        document.querySelectorAll('[id^="project-"]').forEach(el => {
            if(el.id !== targetId) el.classList.add('hidden');
        });

        // Toggle this one
        target.classList.toggle('hidden');

        // Rotate icon
        document.querySelectorAll('.project-btn svg').forEach(svg => svg.classList.remove('rotate-180'));
        const icon = btn.querySelector('svg');
        if(icon && !target.classList.contains('hidden')) icon.classList.add('rotate-180');
    });
});
</script>
