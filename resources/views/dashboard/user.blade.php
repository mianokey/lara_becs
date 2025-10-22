@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-becs-warm-cream">
    <x-header />
        <div class="container mx-auto px-6 py-8">
        <div class="mb-4 flex justify-between items-center">
            <h1 class="text-4xl font-bold text-becs-text-primary mb-2">
                My <span class="text-becs-maroon">Dashboard</span>
                <p class="text-becs-text-secondary text-lg">
                    Welcome back, {{ $user->name }}! Here's your project and tasks overview.
                </p>

            </h1>
            <a href="{{ route('tasks.create') }}"><button
                class="bg-becs-navy hover:bg-becs-navy-dark text-white px-6 py-3 rounded-lg font-medium flex items-center gap-2 shadow-sm">
                <i class="fa fa-calendar"></i>
                New Reminder
            </button>
            </a>


        </div>
       <x-weekly-tasks :incompleteTasks="$incompleteTasks" :completedTasks="$completedTasks" />
    </div>

</div>

@endsection