@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
    <div class="space-y-6">
        {{-- Welcome Header --}}
        <div class="rounded-lg bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white shadow-lg">
            <h1 class="text-2xl font-bold">Welcome back, {{ auth()->user()->name }}!</h1>
            <p class="mt-1 text-blue-100">
                You're logged in as <span class="font-semibold capitalize bg-white/20 px-2 py-1 rounded-full">{{ auth()->user()->role }}</span>
            </p>
        </div>

        @if (auth()->user()->role === 'admin')
            {{-- Overview Statistics Section --}}
            <section class="space-y-4">
                <div>
                    <h2 class="text-xl font-bold text-white">Overview Statistics</h2>
                </div>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    {{-- Total Revenue --}}
                    <div class="group rounded-lg bg-gradient-to-br from-green-600/20 to-green-700/10 p-6 shadow-lg border border-green-500/30 transition-all duration-200 hover:border-green-500/50 hover:shadow-xl">
                        <div class="flex items-center justify-between mb-4">
                            <div class="rounded-lg bg-green-500/20 p-3 group-hover:scale-110 transition-transform duration-200">
                                <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-gray-400 mb-1">Total Revenue</p>
                        <p class="text-2xl font-bold text-white">Rp {{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</p>
                        <p class="mt-2 text-xs text-green-400">From completed jobs</p>
                    </div>

                    {{-- Total Users --}}
                    <div class="group rounded-lg bg-gradient-to-br from-purple-600/20 to-purple-700/10 p-6 shadow-lg border border-purple-500/30 transition-all duration-200 hover:border-purple-500/50 hover:shadow-xl">
                        <div class="flex items-center justify-between mb-4">
                            <div class="rounded-lg bg-purple-500/20 p-3 group-hover:scale-110 transition-transform duration-200">
                                <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-gray-400 mb-1">Total Users</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['total_users'] ?? 0 }}</p>
                        <p class="mt-2 text-xs text-purple-400">Active members</p>
                    </div>

                    {{-- Total Jobs --}}
                    <div class="group rounded-lg bg-gradient-to-br from-blue-600/20 to-blue-700/10 p-6 shadow-lg border border-blue-500/30 transition-all duration-200 hover:border-blue-500/50 hover:shadow-xl">
                        <div class="flex items-center justify-between mb-4">
                            <div class="rounded-lg bg-blue-500/20 p-3 group-hover:scale-110 transition-transform duration-200">
                                <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-gray-400 mb-1">Total Jobs</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['total_jobs'] ?? 0 }}</p>
                        <p class="mt-2 text-xs text-blue-400">All time jobs</p>
                    </div>

                    {{-- Completed Jobs --}}
                    <div class="group rounded-lg bg-gradient-to-br from-emerald-600/20 to-emerald-700/10 p-6 shadow-lg border border-emerald-500/30 transition-all duration-200 hover:border-emerald-500/50 hover:shadow-xl">
                        <div class="flex items-center justify-between mb-4">
                            <div class="rounded-lg bg-emerald-500/20 p-3 group-hover:scale-110 transition-transform duration-200">
                                <svg class="h-6 w-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-gray-400 mb-1">Completed Jobs</p>
                        <p class="text-2xl font-bold text-white">{{ $stats['completed_jobs'] ?? 0 }}</p>
                        <p class="mt-2 text-xs text-emerald-400">Successfully finished</p>
                    </div>
                </div>
            </section>

            {{-- Pending Actions Section --}}
            @if (($stats['pending_topups'] ?? 0) > 0 || ($stats['pending_reports'] ?? 0) > 0)
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white">⚠️ Pending Actions</h2>
                    <span class="text-sm text-orange-400 font-medium">Requires attention</span>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    @if (($stats['pending_topups'] ?? 0) > 0)
                    <a href="{{ route('admin.topups.index') }}" class="group relative rounded-lg bg-gradient-to-br from-orange-600/20 to-orange-700/10 p-6 shadow-lg border-2 border-orange-500/50 transition-all duration-200 hover:border-orange-500 hover:shadow-xl hover:scale-[1.02]">
                        <div class="absolute top-4 right-4">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-orange-500 text-xs font-bold text-white">
                                {{ $stats['pending_topups'] }}
                            </span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="rounded-lg bg-orange-500/20 p-4 group-hover:scale-110 transition-transform duration-200">
                                <svg class="h-8 w-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-white mb-1">Pending Topups</h3>
                                <p class="text-sm text-gray-400 mb-2">There are <span class="font-semibold text-orange-400">{{ $stats['pending_topups'] }}</span> topup requests waiting for approval</p>
                                <span class="inline-flex items-center text-sm text-orange-400 font-medium group-hover:translate-x-1 transition-transform duration-200">
                                    Review now
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                    @endif

                    @if (($stats['pending_reports'] ?? 0) > 0)
                    <a href="{{ route('admin.reports.index') }}" class="group relative rounded-lg bg-gradient-to-br from-red-600/20 to-red-700/10 p-6 shadow-lg border-2 border-red-500/50 transition-all duration-200 hover:border-red-500 hover:shadow-xl hover:scale-[1.02]">
                        <div class="absolute top-4 right-4">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-red-500 text-xs font-bold text-white">
                                {{ $stats['pending_reports'] }}
                            </span>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="rounded-lg bg-red-500/20 p-4 group-hover:scale-110 transition-transform duration-200">
                                <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-white mb-1">Pending Reports</h3>
                                <p class="text-sm text-gray-400 mb-2">There are <span class="font-semibold text-red-400">{{ $stats['pending_reports'] }}</span> reports waiting for review</p>
                                <span class="inline-flex items-center text-sm text-red-400 font-medium group-hover:translate-x-1 transition-transform duration-200">
                                    Review now
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </a>
                    @endif
                </div>
            </section>
            @endif

            {{-- Job Statistics Section --}}
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-white">Job Statistics</h2>
                    <a href="{{ route('jobs.index') }}" class="text-sm text-blue-400 hover:text-blue-300 transition-colors flex items-center gap-1">
                        View all jobs
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-lg bg-gray-800 p-6 shadow-lg border border-gray-700 hover:border-blue-500 transition-all duration-200 hover:shadow-xl">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-medium text-gray-400">Pending Jobs</p>
                            <div class="rounded-lg bg-blue-500/20 p-2">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-blue-400">{{ $stats['pending_jobs'] ?? 0 }}</p>
                        <p class="mt-2 text-xs text-gray-500">Awaiting assignment</p>
                    </div>
                    <div class="rounded-lg bg-gray-800 p-6 shadow-lg border border-gray-700 hover:border-green-500 transition-all duration-200 hover:shadow-xl">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-medium text-gray-400">Active Jobs</p>
                            <div class="rounded-lg bg-green-500/20 p-2">
                                <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-green-400">{{ $stats['active_jobs'] ?? 0 }}</p>
                        <p class="mt-2 text-xs text-gray-500">In progress</p>
                    </div>
                    <div class="rounded-lg bg-gray-800 p-6 shadow-lg border border-gray-700 hover:border-emerald-500 transition-all duration-200 hover:shadow-xl">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-medium text-gray-400">Completed Jobs</p>
                            <div class="rounded-lg bg-emerald-500/20 p-2">
                                <svg class="h-5 w-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-3xl font-bold text-emerald-400">{{ $stats['completed_jobs'] ?? 0 }}</p>
                        <p class="mt-2 text-xs text-gray-500">Successfully finished</p>
                    </div>
                </div>
            </section>

            {{-- Quick Actions Section --}}
            <section class="space-y-4">
                <div>
                    <h2 class="text-xl font-bold text-white">Quick Actions</h2>
                </div>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <a href="{{ route('jobs.index') }}" class="group rounded-lg bg-gray-800 p-6 shadow-lg border border-gray-700 transition-all duration-200 hover:border-blue-500 hover:shadow-xl hover:scale-[1.02]">
                        <div class="flex flex-col items-center text-center">
                            <div class="rounded-lg bg-blue-500/20 p-4 mb-4 group-hover:scale-110 transition-transform duration-200">
                                <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-white mb-1">Job Management</h3>
                            <p class="text-sm text-gray-400">View and manage all jobs</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.topups.index') }}" class="group rounded-lg bg-gray-800 p-6 shadow-lg border border-gray-700 transition-all duration-200 hover:border-green-500 hover:shadow-xl hover:scale-[1.02]">
                        <div class="flex flex-col items-center text-center">
                            <div class="rounded-lg bg-green-500/20 p-4 mb-4 group-hover:scale-110 transition-transform duration-200">
                                <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-white mb-1">Topup Control</h3>
                            <p class="text-sm text-gray-400">Approve topup requests</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.reports.index') }}" class="group rounded-lg bg-gray-800 p-6 shadow-lg border border-gray-700 transition-all duration-200 hover:border-orange-500 hover:shadow-xl hover:scale-[1.02]">
                        <div class="flex flex-col items-center text-center">
                            <div class="rounded-lg bg-orange-500/20 p-4 mb-4 group-hover:scale-110 transition-transform duration-200">
                                <svg class="h-8 w-8 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-white mb-1">Reports</h3>
                            <p class="text-sm text-gray-400">Handle user reports</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.users.index') }}" class="group rounded-lg bg-gray-800 p-6 shadow-lg border border-gray-700 transition-all duration-200 hover:border-purple-500 hover:shadow-xl hover:scale-[1.02]">
                        <div class="flex flex-col items-center text-center">
                            <div class="rounded-lg bg-purple-500/20 p-4 mb-4 group-hover:scale-110 transition-transform duration-200">
                                <svg class="h-8 w-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                            <h3 class="font-semibold text-white mb-1">User Management</h3>
                            <p class="text-sm text-gray-400">Manage all users</p>
                        </div>
                    </a>
                </div>
            </section>
        @else
            {{-- User Dashboard --}}
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                {{-- Balance Card --}}
                <div class="rounded-lg bg-gray-800 p-6 shadow-lg border border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-400">Balance</p>
                            <p class="mt-2 text-2xl font-bold text-white">Rp {{ number_format($stats['balance'] ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-full bg-green-500/20 p-3">
                            <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Jobs Created --}}
                <div class="rounded-lg bg-gray-800 p-6 shadow-lg border border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-400">Jobs Created</p>
                            <p class="mt-2 text-2xl font-bold text-white">{{ $stats['jobs_created'] ?? 0 }}</p>
                        </div>
                        <div class="rounded-full bg-blue-500/20 p-3">
                            <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Jobs Completed --}}
                <div class="rounded-lg bg-gray-800 p-6 shadow-lg border border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-400">Jobs Completed</p>
                            <p class="mt-2 text-2xl font-bold text-white">{{ $stats['jobs_completed'] ?? 0 }}</p>
                        </div>
                        <div class="rounded-full bg-green-500/20 p-3">
                            <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Total Earned --}}
                <div class="rounded-lg bg-gray-800 p-6 shadow-lg border border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-400">Total Earned</p>
                            <p class="mt-2 text-2xl font-bold text-white">Rp {{ number_format($stats['total_earned'] ?? 0, 0, ',', '.') }}</p>
                        </div>
                        <div class="rounded-full bg-yellow-500/20 p-3">
                            <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="grid gap-6 md:grid-cols-2">
                <a href="{{ route('jobs.index') }}" class="rounded-lg bg-gray-800 p-6 shadow-lg border border-gray-700 hover:border-blue-500 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-blue-500/20 p-3">
                            <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white">View Jobs</h3>
                            <p class="text-sm text-gray-400">Browse available jobs</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('topups.create') }}" class="rounded-lg bg-gray-800 p-6 shadow-lg border border-gray-700 hover:border-green-500 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="rounded-full bg-green-500/20 p-3">
                            <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-semibold text-white">Topup Balance</h3>
                            <p class="text-sm text-gray-400">Add funds to your account</p>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Additional Stats --}}
            <div class="grid gap-6 md:grid-cols-3">
                <div class="rounded-lg bg-gray-800 p-6 shadow-lg border border-gray-700">
                    <p class="text-sm font-medium text-gray-400">Jobs In Progress</p>
                    <p class="mt-2 text-3xl font-bold text-blue-400">{{ $stats['jobs_in_progress'] ?? 0 }}</p>
                </div>
                <div class="rounded-lg bg-gray-800 p-6 shadow-lg border border-gray-700">
                    <p class="text-sm font-medium text-gray-400">Available Jobs</p>
                    <p class="mt-2 text-3xl font-bold text-green-400">{{ $stats['available_jobs'] ?? 0 }}</p>
                </div>
                <div class="rounded-lg bg-gray-800 p-6 shadow-lg border border-gray-700">
                    <p class="text-sm font-medium text-gray-400">Jobs Taken</p>
                    <p class="mt-2 text-3xl font-bold text-purple-400">{{ $stats['jobs_taken'] ?? 0 }}</p>
                </div>
            </div>
        @endif
    </div>
@endsection
