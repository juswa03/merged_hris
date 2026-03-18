<!DOCTYPE html>
<html>
<head>
    <title>Leave Application Form - CS Form</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .form-header { text-align: center; margin-bottom: 30px; }
        .form-section { margin-bottom: 20px; }
        .form-field { margin-bottom: 10px; }
        .label { font-weight: bold; }
        .signature-box { 
            border: 1px solid #000; 
            height: 100px; 
            width: 300px; 
            margin-top: 20px;
        }
        .table { width: 100%; border-collapse: collapse; }
        .table td, .table th { border: 1px solid #000; padding: 8px; }
    </style>
</head>
<body>
    <div class="form-header">
        <h2>OFFICIAL LEAVE APPLICATION FORM</h2>
        <p>Civil Service Form No. 6</p>
    </div>

    <div class="form-section">
        <div class="form-field">
            <span class="label">Name:</span>
            {{ $leave->user->name ?? 'N/A' }}
        </div>
        
        <div class="form-field">
            <span class="label">Position:</span>
            {{ $leave->user->position ?? 'N/A' }}
        </div>
        
        <div class="form-field">
            <span class="label">Office/Department:</span>
            {{ $leave->user->department ?? 'N/A' }}
        </div>
        
        <div class="form-field">
            <span class="label">Salary:</span>
            {{ $leave->user->salary ?? 'N/A' }}
        </div>
    </div>

    <div class="form-section">
        <table class="table">
            <thead>
                <tr>
                    <th>Type of Leave</th>
                    <th>Where Leave will be spent</th>
                    <th>Number of Days</th>
                    <th>Inclusive Dates</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $leave->leave_type }}</td>
                    <td>{{ $leave->location ?? 'N/A' }}</td>
                    <td>{{ $leave->number_of_days }}</td>
                    <td>{{ $leave->start_date->format('M d, Y') }} - {{ $leave->end_date->format('M d, Y') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="form-section">
        <div class="form-field">
            <span class="label">Details of Leave:</span><br>
            {{ $leave->details ?? 'N/A' }}
        </div>
    </div>

    <div class="form-section">
        <div class="form-field">
            <span class="label">Signature of Applicant:</span>
            @if($leave->signature)
                <div class="signature-box">
                    <img src="{{ $leave->signature }}" style="max-width: 100%; max-height: 80px;" alt="Signature">
                </div>
            @else
                <div class="signature-box"></div>
            @endif
        </div>
    </div>

    <div class="form-section">
        <div class="form-field">
            <span class="label">Date Filed:</span>
            {{ $leave->created_at->format('F d, Y') }}
        </div>
    </div>

    <!-- Add more sections for approval authorities if needed -->
</body>
</html>