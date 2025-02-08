@extends('shared.dashboard')
@section('content')
<div class="page-content">
    <div class="mb-3 page-breadcrumb d-none d-sm-flex align-items-center">
        <div class="breadcrumb-title pe-3">Admin Dashboard</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="p-0 mb-0 breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Admin Dashboard</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container">
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 gap-4 mb-8 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Total Tickets -->
                    <div class="p-6 bg-white rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-500 rounded-full">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold">Total Tickets</h3>
                                <p class="text-2xl font-bold">{{ $totalTickets }}</p>
                                <p class="text-sm text-gray-600">{{ $openTickets }} Open</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Contracts -->
                    <div class="p-6 bg-white rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-500 rounded-full">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold">Active Contracts</h3>
                                <p class="text-2xl font-bold">{{ $activeContracts }}</p>
                                <p class="text-sm text-gray-600">{{ $totalContracts }} Total</p>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue -->
                    <div class="p-6 bg-white rounded-lg shadow-sm">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-500 rounded-full">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold">Monthly Revenue</h3>
                                <p class="text-2xl font-bold">{{ number_format($monthlyRevenue, 2) }}</p>
                                <p class="text-sm text-gray-600">{{ number_format($yearlyRevenue, 2) }} Yearly</p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                        <!-- Recent Tickets -->
                        <div class="p-6 bg-white rounded-lg shadow-sm">
                            <h3 class="mb-4 text-lg font-semibold">Recent Tickets</h3>
                            <div class="space-y-4">
                                @foreach($recentTickets as $ticket)
                                <div class="p-4 rounded-lg border">
                                    <div class="flex justify-between">
                                        <h4 class="font-medium">{{ $ticket->title }}</h4>
                                        <span
                                            class="px-2 py-1 text-xs rounded-full {{ $ticket->status === 'open' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-600">Created {{
                                        $ticket->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                            <a href="{{ url('tickets.index') }}"
                                class="block mt-4 text-sm text-blue-600 hover:underline">View all tickets →</a>
                        </div>

                        <!-- Recent Contracts -->
                        <div class="p-6 bg-white rounded-lg shadow-sm">
                            <h3 class="mb-4 text-lg font-semibold">Recent Contracts</h3>
                            <div class="space-y-4">
                                @foreach($recentContracts as $contract)
                                <div class="p-4 rounded-lg border">
                                    <div class="flex justify-between">
                                        <h4 class="font-medium">{{ $contract->client_name }}</h4>
                                        <span class="text-sm text-gray-600">{{ number_format($contract->value, 2)
                                            }}</span>
                                    </div>
                                    {{-- <p class="mt-1 text-sm text-gray-600">Expires {{ $contract->start_date->format('M d, Y')
                                        }}</p> --}}
                                </div>
                                @endforeach
                            </div>
                            <a href="{{ url('contracts.index') }}"
                                class="block mt-4 text-sm text-blue-600 hover:underline">View all contracts →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection