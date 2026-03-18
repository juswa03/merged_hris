@extends('employee.layouts.app')



@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="{{ asset('css/pds.css') }}" rel="stylesheet"> 
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<div class="wrapper">
    <!-- resources/views/employees/pds/index.blade.php -->


    <div class="pds-container bg-white shadow-sm p-4">
        <h2 class="mb-4 text-center">Personal Data Sheet (PDS)</h2>

        <form method="POST" action="{{ route('pds.update') }}">
            @csrf

            <table class="pds-table section-table">
                @include('employees.pds.personal_information')
            </table>

            <table class="pds-table section-table">
                @include('employees.pds.family_background')
            </table>

            <table class="pds-table section-table">
                @include('employees.pds.education')
            </table>

            <table class="pds-table section-table">
                @include('employees.pds.civil_service_eligibility')
            </table>

            <table class="pds-table section-table">
                @include('employees.pds.work_experience')
            </table>

            <table class="pds-table section-table">
                @include('employees.pds.membership_associations')
            </table>

            <table class="pds-table section-table">
                @include('employees.pds.learning_development')
            </table>

            <table class="pds-table section-table">
                @include('employees.pds.special_skills_hobbies')
            </table>

            <table class="pds-table section-table">
                @include('employees.pds.last_page')
            </table>

            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('pds.download') }}" target="_blank" class="btn btn-success">
                    Download PDF
                </a>

                <!-- Submit PDS Button -->
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#submitPDSModal">
                    Submit PDS
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal for Submission (moved outside scaled container) -->
<div class="modal fade" id="submitPDSModal" tabindex="-1" aria-labelledby="submitPDSModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('pds.submit') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="submitPDSModalLabel">Submit PDS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Recipients -->
                    <div class="mb-3">
                        <label for="recipients" class="form-label">Select Recipients</label>
                        @php
                        // Fetch all HR and Admin users as possible recipients using Spatie permissions
                        $recipients = \App\Models\User::with('personalInformation')
                            ->whereHas('roles', function ($query) {
                                $query->whereIn('name', ['HR Manager', 'Super Admin']);
                            })
                            ->get();
                        @endphp

                        <select name="recipients[]" id="recipients" class="form-select" multiple required>
                            @foreach($recipients as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->first_name }} {{ $user->last_name }} ({{ implode(', ', $user->getRoleNames()->toArray()) }})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple.</small>
                    </div>

                    <!-- Document Type -->
                    <div class="mb-3">
                        <label for="document_type" class="form-label">Document Type</label>
                        <select name="document_type" id="document_type" class="form-select" required>
                            <option value="PDS" selected>PDS</option>
                            <option value="SALN">SALN</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
