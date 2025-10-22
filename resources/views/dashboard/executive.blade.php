@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-becs-warm-cream">
    <x-header />
    <div class="container mx-auto px-6 py-8">
<div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center">
    <h1 class="text-2xl sm:text-4xl font-bold text-becs-text-primary mb-2 sm:mb-0">
        Executive 
        <span class="text-becs-maroon">Dashboard</span>
        <p class="text-becs-text-secondary text-sm sm:text-lg mt-1">
            Welcome back, {{ $user->name }}! Here's your project and task overview.
        </p>
    </h1>

    <a href="{{ route('tasks.create') }}" class="mt-3 sm:mt-0">
        <button
            class="bg-becs-navy hover:bg-becs-navy-dark text-white px-3 sm:px-6 py-2 sm:py-3 rounded-lg font-medium flex items-center gap-2 shadow-sm">
            
        
            <!-- Full text only on sm+ screens -->
            <span class=" items-center gap-2">
                <i class="fa fa-tasks"> </i>
                <i class="fa fa-calendar"></i>
                Assign Weekly Task
            </span>
        </button>
    </a>
</div>


        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-3">
            <div class="p-6 bg-white rounded-lg shadow border-l-4 border-l-becs-maroon">
                <h4 class="text-becs-text-primary text-sm font-semibold">Active Projects</h4>
                <p class="text-becs-maroon text-2xl font-bold mt-2">{{ $activeProjectsCount }}</p>
            </div>
            <div class="p-6 bg-white rounded-lg shadow border-l-4 border-l-becs-navy">
                <h4 class="text-becs-text-primary text-sm font-semibold">Weekly Tasks</h4>
                <p class="text-becs-maroon text-2xl font-bold mt-2">{{ $weeklyTasksCount }}</p>
            </div>
            <div class="p-6 bg-white rounded-lg shadow border-l-4 border-l-becs-red">
                <h4 class="text-becs-text-primary text-sm font-semibold">Staff Attendance</h4>
                <p class="text-becs-maroon text-2xl font-bold mt-2">{{ $staffAttendanceCount }}</p>
            </div>
            <div class="p-6 bg-white rounded-lg shadow border-l-4 border-l-becs-gold">
                <h4 class="text-becs-text-primary text-sm font-semibold">Online Staff</h4>
                <p class="text-becs-maroon text-2xl font-bold mt-2">{{ $onlineStaffCount ?? 0 }}</p>
            </div>
        </div>
        <x-ahp-consortiums :projects="$ahpProjects" />
        <x-weekly-tasks :incompleteTasks="$incompleteTasks" :completedTasks="$completedTasks" />
        <x-private-projects :projects="$privateProjects" class="" />
    </div>
</div>
@endsection