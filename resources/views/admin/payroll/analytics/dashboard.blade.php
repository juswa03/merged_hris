@extends('layouts.app')

@section('title', 'Payroll Analytics')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Payroll Analytics Dashboard</h1>
                <p class="text-gray-600 mt-1">Comprehensive payroll insights and trends</p>
            </div>
            <div class="flex gap-4">
                <form method="GET" class="flex gap-2 items-center">
                    <select name="year" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @for($y = 2020; $y <= now()->year; $y++)
                            <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
                <a href="{{ route('payroll.analytics.export', ['year' => $year]) }}" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Export Report
                </a>
            </div>
        </div>
    </div>

    <!-- Key Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Gross Income -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-blue-100 text-sm font-semibold">Total Gross Income</p>
                    <p class="text-3xl font-bold mt-2">₱{{ number_format($keyMetrics['total_gross_income'], 2) }}</p>
                </div>
                <div class="text-blue-200 text-3xl">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8m3.5-9c.83 0 1.5-.67 1.5-1.5S16.33 8 15.5 8 14 8.67 14 9.5s.67 1.5 1.5 1.5m-7 0c.83 0 1.5-.67 1.5-1.5S9.33 8 8.5 8 7 8.67 7 9.5 7.67 11 8.5 11m3.5 6.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Net Income -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-green-100 text-sm font-semibold">Total Net Income</p>
                    <p class="text-3xl font-bold mt-2">₱{{ number_format($keyMetrics['total_net_income'], 2) }}</p>
                </div>
                <div class="text-green-200 text-3xl">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8m.31-8.86c-1.77-1.39-4.02-2.2-6.46-2.2-5.08 0-9.27 4.19-9.27 9.27s4.19 9.27 9.27 9.27 9.27-4.19 9.27-9.27c0-2.44-.81-4.63-2.15-6.39L12.31 9.14z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Tax Withheld -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-red-100 text-sm font-semibold">Total Tax Withheld</p>
                    <p class="text-3xl font-bold mt-2">₱{{ number_format($keyMetrics['total_tax_withheld'], 2) }}</p>
                    <p class="text-red-100 text-xs mt-2">{{ number_format($keyMetrics['effective_tax_rate'], 2) }}% of gross</p>
                </div>
                <div class="text-red-200 text-3xl">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Employees -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-purple-100 text-sm font-semibold">Total Employees</p>
                    <p class="text-3xl font-bold mt-2">{{ $keyMetrics['total_employees'] }}</p>
                    <p class="text-purple-100 text-xs mt-2">₱{{ number_format($keyMetrics['average_salary'], 2) }} avg</p>
                </div>
                <div class="text-purple-200 text-3xl">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm8 0c1.66 0 2.99-1.34 2.99-3S25.66 5 24 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-4 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8-2c.33 0 .66-.04 1-.1.39 1.41 1.04 2.63 1.9 3.6.5.57 1.45 1.5 1.45 1.5v2.5h6V12h-2.45z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Mandatory Deductions</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">SSS</span>
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
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Summary Stats</h3>
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
                    <span class="text-gray-600">Year</span>
                    <span class="font-semibold">{{ $year }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Year-over-Year</h3>
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
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Monthly Trends Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Monthly Trends - {{ $year }}</h3>
            <canvas id="monthlyTrendsChart" height="80"></canvas>
        </div>

        <!-- Salary Distribution Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Salary Distribution</h3>
            <canvas id="salaryDistributionChart" height="80"></canvas>
        </div>
    </div>

    <!-- Department Breakdown -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Department Breakdown</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Department</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">Employees</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">Gross Income</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">Net Income</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">Tax Withheld</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">Average Salary</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departmentBreakdown as $dept)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $dept['department'] }}</td>
                            <td class="px-6 py-4 text-right">{{ $dept['employee_count'] }}</td>
                            <td class="px-6 py-4 text-right">₱{{ number_format($dept['gross_income'], 2) }}</td>
                            <td class="px-6 py-4 text-right">₱{{ number_format($dept['net_income'], 2) }}</td>
                            <td class="px-6 py-4 text-right">₱{{ number_format($dept['tax_withheld'], 2) }}</td>
                            <td class="px-6 py-4 text-right">₱{{ number_format($dept['average_salary'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No department data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Earners -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Top 10 Earners - {{ $year }}</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b-2 border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Rank</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Employee Name</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-700">Department</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">Total Gross</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">Total Net</th>
                        <th class="px-6 py-3 text-right font-semibold text-gray-700">Payrolls</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topEarners as $index => $earner)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white text-xs font-semibold">
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
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No earner data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Deductions vs Allowances -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Deductions vs Allowances Breakdown</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Deductions Chart -->
            <div>
                <h4 class="font-semibold text-gray-700 mb-4">Total Deductions: ₱{{ number_format($deductionsAllowances['total_deductions'], 2) }}</h4>
                <canvas id="deductionsChart" height="80"></canvas>
            </div>
            <!-- Allowances Summary -->
            <div class="flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-8">
                <div class="text-center">
                    <p class="text-gray-600 text-sm mb-2">Total Allowances</p>
                    <p class="text-4xl font-bold text-blue-600">₱{{ number_format($deductionsAllowances['allowances'], 2) }}</p>
                    <p class="text-gray-600 text-xs mt-4">of total payroll budget</p>
                </div>
            </div>
        </div>
    </div>
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
