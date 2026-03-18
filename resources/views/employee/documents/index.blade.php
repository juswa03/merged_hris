@extends('employee.layouts.app')

@section('title', 'My Documents')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-800">My Documents</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        {{-- Personal Data Sheet --}}
        <a href="{{ route('employee.pds.index') }}"
           class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:border-blue-300 hover:shadow-md transition-all">
            <div class="p-3 bg-blue-50 rounded-lg">
                <i class="fas fa-id-card text-blue-600 text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Personal Data Sheet</p>
                <p class="text-sm text-gray-500 mt-1">CSC Form 212 — Personal information record</p>
            </div>
            <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
        </a>

        {{-- SALN --}}
        <a href="{{ route('employee.saln.index') }}"
           class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:border-blue-300 hover:shadow-md transition-all">
            <div class="p-3 bg-emerald-50 rounded-lg">
                <i class="fas fa-file-invoice-dollar text-emerald-600 text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">SALN</p>
                <p class="text-sm text-gray-500 mt-1">Statement of Assets, Liabilities and Net Worth</p>
            </div>
            <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
        </a>

        {{-- Daily Time Record --}}
        <a href="{{ route('employee.dtr.index') }}"
           class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:border-blue-300 hover:shadow-md transition-all">
            <div class="p-3 bg-amber-50 rounded-lg">
                <i class="fas fa-clock text-amber-600 text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Daily Time Record</p>
                <p class="text-sm text-gray-500 mt-1">View and export your CS Form 48 DTR</p>
            </div>
            <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
        </a>

        {{-- Payslips --}}
        <a href="{{ route('employee.payroll.payslips') }}"
           class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:border-blue-300 hover:shadow-md transition-all">
            <div class="p-3 bg-purple-50 rounded-lg">
                <i class="fas fa-file-invoice text-purple-600 text-xl"></i>
            </div>
            <div>
                <p class="font-semibold text-gray-800">Payslips</p>
                <p class="text-sm text-gray-500 mt-1">Download your monthly payslips</p>
            </div>
            <i class="fas fa-chevron-right ml-auto text-gray-400"></i>
        </a>

    </div>
</div>
@endsection
