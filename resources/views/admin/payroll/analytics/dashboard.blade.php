@extends('admin.layouts.app')

@section('title', 'Payroll Analytics')

@section('content')
<div class="container mx-auto px-4 py-6">

    <x-admin.page-header
        title="Payroll Analytics Dashboard"
        description="Comprehensive payroll insights and trends"
    >
        <x-slot name="actions">
            <form method="GET" class="flex items-center gap-2">
                <select name="year" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @for($y = 2020; $y <= now()->year; $y++)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
            <x-admin.action-button href="{{ route('admin.payroll.analytics.export', ['year' => $year]) }}" variant="success" icon="fas fa-download">
                Export Report
            </x-admin.action-button>
        </x-slot>
    </x-admin.page-header>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-admin.gradient-stat-card
            title="Total Gross Income"
            :value="'₱' . number_format($keyMetrics['total_gross_income'], 2)"
            icon="fas fa-money-bill-wave"
            gradientFrom="blue-500"
            gradientTo="blue-600"
        />
        <x-admin.gradient-stat-card
            title="Total Net Income"
            :value="'₱' . number_format($keyMetrics['total_net_income'], 2)"
            icon="fas fa-wallet"
            gradientFrom="green-500"
            gradientTo="green-600"
        />
        <x-admin.gradient-stat-card
            title="Total Tax Withheld"
            :value="'₱' . number_format($keyMetrics['total_tax_withheld'], 2)"
            icon="fas fa-shield-alt"
            gradientFrom="red-500"
            gradientTo="red-600"
        />
        <x-admin.gradient-stat-card
            title="Total Employees"
            :value="$keyMetrics['total_employees']"
            icon="fas fa-users"
            gradientFrom="purple-500"
            gradientTo="purple-600"
        />
    </div>

    <!-- Secondary Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-admin.card title="Mandatory Deductions">
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">SSS / GSIS</span>
                    <span class="font-semibold">₱{{ number_format($keyMetrics['total_sss'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">PhilHealth</span>
                    <span class="font-semibold">₱{{ number_format($keyMetrics['total_philhealth'], 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Pag-IBIG</span>
                    <span class="font-semibold">₱{{ number_format($keyMetrics['total_pagibig'], 2) }}</span>
                </div>
                <hr class="my-2">
                <div class="flex justify-between font-semibold text-lg">
                    <span>Total</span>
                    <span>₱{{ number_format($keyMetrics['total_sss'] + $keyMetrics['total_philhealth'] + $keyMetrics['total_pagibig'], 2) }}</span>
                </div>
            </div>
        </x-admin.card>

        <x-admin.card title="Summary Stats">
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Payrolls</span>
                    <span class="font-semibold">{{ $keyMetrics['total_payrolls'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Deduction Rate</span>
                    <span class="font-semibold">{{ number_format($keyMetrics['deduction_rate'], 2) }}%</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Tax Rate</span>
                    <span class="font-semibold">{{ number_format($keyMetrics['effective_tax_rate'], 2) }}%</span>
                </div>
                <hr class="my-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Avg Salary</span>
                    <span class="font-semibold">₱{{ number_format($keyMetrics['average_salary'], 2) }}</span>
                </div>
            </div>
        </x-admin.card>

        <x-admin.card title="Year-over-Year">
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500">Gross Income Change</p>
                    <p class="font-semibold">
                        @if($yoyComparison['gross_income_change'] >= 0)
                            <span class="text-green-600">+{{ number_format($yoyComparison['gross_income_change_percent'], 2) }}%</span>
                        @else
                            <span class="text-red-600">{{ number_format($yoyComparison['gross_income_change_percent'], 2) }}%</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Employee Count Change</p>
                    <p class="font-semibold">
                        @if($yoyComparison['employee_count_change'] >= 0)
                            <span class="text-green-600">+{{ $yoyComparison['employee_count_change'] }}</span>
                        @else
                            <span class="text-red-600">{{ $yoyComparison['employee_count_change'] }}</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Average Salary Change</p>
                    <p class="font-semibold">
                        @if($yoyComparison['average_salary_change'] >= 0)
                            <span class="text-green-600">+₱{{ number_format($yoyComparison['average_salary_change'], 2) }}</span>
                        @else
                            <span class="text-red-600">₱{{ number_format($yoyComparison['average_salary_change'], 2) }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </x-admin.card>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <x-admin.card title="Monthly Trends - {{ $year }}">
            <canvas id="monthlyTrendsChart" height="80"></canvas>
        </x-admin.card>

        <x-admin.card title="Salary Distribution">
            <canvas id="salaryDistributionChart" height="80"></canvas>
        </x-admin.card>
    </div>

    <!-- Department Breakdown -->
    <x-admin.card title="Department Breakdown" :padding="false" class="mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Department</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">Employees</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">Gross Income</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">Net Income</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">Tax Withheld</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">Average Salary</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($departmentBreakdown as $dept)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $dept['department'] }}</td>
                            <td class="px-6 py-4 text-right">{{ $dept['employee_count'] }}</td>
                            <td class="px-6 py-4 text-right">₱{{ number_format($dept['gross_income'], 2) }}</td>
                            <td class="px-6 py-4 text-right">₱{{ number_format($dept['net_income'], 2) }}</td>
                            <td class="px-6 py-4 text-right">₱{{ number_format($dept['tax_withheld'], 2) }}</td>
                            <td class="px-6 py-4 text-right">₱{{ number_format($dept['average_salary'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No department data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-admin.card>

    <!-- Top Earners -->
    <x-admin.card title="Top 10 Earners - {{ $year }}" :padding="false" class="mb-8">
        <div class="overflow-x-auto">
            <table class="w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Rank</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Employee Name</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Department</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">Total Gross</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">Total Net</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">Payrolls</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($topEarners as $index => $earner)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-semibold">
                                    {{ $index + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $earner['employee_name'] }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $earner['department'] }}</td>
                            <td class="px-6 py-4 text-right font-semibold">₱{{ number_format($earner['total_gross'], 2) }}</td>
                            <td class="px-6 py-4 text-right">₱{{ number_format($earner['total_net'], 2) }}</td>
                            <td class="px-6 py-4 text-right">{{ $earner['payroll_count'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No earner data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-admin.card>

    <!-- Deductions vs Allowances -->
    <x-admin.card title="Deductions vs Allowances Breakdown" class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h4 class="font-semibold text-gray-700 mb-4">Total Deductions: ₱{{ number_format($deductionsAllowances['total_deductions'], 2) }}</h4>
                <canvas id="deductionsChart" height="80"></canvas>
            </div>
            <div class="flex items-center justify-center bg-blue-50 rounded-lg p-8">
                <div class="text-center">
                    <p class="text-gray-600 text-sm mb-2">Total Allowances</p>
                    <p class="text-4xl font-bold text-blue-600">₱{{ number_format($deductionsAllowances['allowances'], 2) }}</p>
                    <p class="text-gray-600 text-xs mt-4">of total payroll budget</p>
                </div>
            </div>
        </div>
    </x-admin.card>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Monthly Trends Chart
    const monthlyCtx = document.getElementById('monthlyTrendsChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: @json(array_column($monthlyTrends, 'month')),
            datasets: [
                {
                    label: 'Gross Income',
                    data: @json(array_column($monthlyTrends, 'gross_income')),
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointBackgroundColor: '#3B82F6',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                },
                {
                    label: 'Net Income',
                    data: @json(array_column($monthlyTrends, 'net_income')),
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointBackgroundColor: '#10B981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Salary Distribution Chart
    const salaryCtx = document.getElementById('salaryDistributionChart').getContext('2d');
    new Chart(salaryCtx, {
        type: 'bar',
        data: {
            labels: @json(array_keys($salaryDistribution)),
            datasets: [{
                label: 'Number of Employees',
                data: @json(array_values($salaryDistribution)),
                backgroundColor: [
                    '#EF4444',
                    '#F97316',
                    '#FBBF24',
                    '#4ADE80',
                    '#3B82F6',
                    '#8B5CF6'
                ],
                borderRadius: 4,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Deductions Chart
    const deductionsCtx = document.getElementById('deductionsChart').getContext('2d');
    const deductionNames = @json(array_column($deductionsAllowances['deductions'], 'name'));
    const deductionAmounts = @json(array_column($deductionsAllowances['deductions'], 'amount'));
    new Chart(deductionsCtx, {
        type: 'doughnut',
        data: {
            labels: deductionNames,
            datasets: [{
                data: deductionAmounts,
                backgroundColor: [
                    '#8B5CF6',
                    '#3B82F6',
                    '#0EA5E9',
                    '#EF4444'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endsection
