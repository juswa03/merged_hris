@extends('employee.layouts.app')

@section('title', 'Biometric Status')
@section('subtitle', 'Fingerprint & RFID Enrollment Status')

@section('content')
<div class="space-y-6">
    <!-- Status Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Fingerprint Status -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-blue-500 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Fingerprint Status</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ $biometricStatus->fingerprint_enrolled ? 'Enrolled' : 'Not Enrolled' }}
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        @if($biometricStatus->fingerprint_enrolled)
                            <i class="fas fa-check-circle text-green-500 mr-1"></i>
                            Last used: {{ $biometricStatus->last_fingerprint_used ? $biometricStatus->last_fingerprint_used->diffForHumans() : 'Never' }}
                        @else
                            <i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i>
                            Enrollment required for biometric access
                        @endif
                    </p>
                </div>
                <div class="p-3 rounded-full {{ $biometricStatus->fingerprint_enrolled ? 'bg-green-100' : 'bg-yellow-100' }}">
                    <i class="fas fa-fingerprint text-2xl {{ $biometricStatus->fingerprint_enrolled ? 'text-green-600' : 'text-yellow-600' }}"></i>
                </div>
            </div>
        </div>

        <!-- RFID Status -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-purple-500 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">RFID Card Status</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ $biometricStatus->rfid_assigned ? 'Active' : 'Not Assigned' }}
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        @if($biometricStatus->rfid_assigned)
                            <i class="fas fa-check-circle text-green-500 mr-1"></i>
                            Card: {{ $biometricStatus->rfid_code }}
                        @else
                            <i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i>
                            RFID card not yet assigned
                        @endif
                    </p>
                </div>
                <div class="p-3 rounded-full {{ $biometricStatus->rfid_assigned ? 'bg-green-100' : 'bg-yellow-100' }}">
                    <i class="fas fa-id-card text-2xl {{ $biometricStatus->rfid_assigned ? 'text-green-600' : 'text-yellow-600' }}"></i>
                </div>
            </div>
        </div>

        <!-- Overall Status -->
        <div class="bg-white rounded-lg shadow-sm border-l-4 border-green-500 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Overall Biometric Status</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        {{ $biometricStatus->is_fully_enrolled ? 'Fully Enrolled' : 'Pending Enrollment' }}
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        @if($biometricStatus->is_fully_enrolled)
                            <i class="fas fa-check-circle text-green-500 mr-1"></i>
                            Ready for biometric access
                        @else
                            <i class="fas fa-clock text-yellow-500 mr-1"></i>
                            Complete enrollment required
                        @endif
                    </p>
                </div>
                <div class="p-3 rounded-full {{ $biometricStatus->is_fully_enrolled ? 'bg-green-100' : 'bg-yellow-100' }}">
                    <i class="fas fa-user-check text-2xl {{ $biometricStatus->is_fully_enrolled ? 'text-green-600' : 'text-yellow-600' }}"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Fingerprint Enrollment -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Fingerprint Enrollment</h3>
                    <p class="text-sm text-gray-600 mt-1">Enroll your fingerprint for biometric authentication</p>
                </div>
                <div class="p-2 bg-blue-100 rounded-full">
                    <i class="fas fa-fingerprint text-blue-600 text-xl"></i>
                </div>
            </div>

            @if(!$biometricStatus->fingerprint_enrolled)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-yellow-800">Fingerprint not enrolled</p>
                        <p class="text-sm text-yellow-700 mt-1">You need to enroll your fingerprint to use biometric devices.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <button onclick="startFingerprintEnrollment()" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-semibold flex items-center justify-center transition-colors">
                    <i class="fas fa-fingerprint mr-2"></i>
                    Start Fingerprint Enrollment
                </button>
                
                <div class="text-center">
                    <button type="button" 
                            onclick="showEnrollmentInstructions()"
                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        <i class="fas fa-info-circle mr-1"></i>
                        View Enrollment Instructions
                    </button>
                </div>
            </div>
            @else
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-green-800">Fingerprint enrolled</p>
                        <p class="text-sm text-green-700 mt-1">
                            Your fingerprint is successfully enrolled. 
                            @if($biometricStatus->last_fingerprint_used)
                                Last used {{ $biometricStatus->last_fingerprint_used->diffForHumans() }}.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <button onclick="reEnrollFingerprint()" 
                        class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-4 rounded-lg font-semibold flex items-center justify-center transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Re-enroll Fingerprint
                </button>
                
                <button onclick="testFingerprint()" 
                        class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg font-semibold flex items-center justify-center transition-colors">
                    <i class="fas fa-vial mr-2"></i>
                    Test Fingerprint
                </button>
            </div>
            @endif
        </div>

        <!-- RFID Management -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">RFID Card</h3>
                    <p class="text-sm text-gray-600 mt-1">Manage your RFID card access</p>
                </div>
                <div class="p-2 bg-purple-100 rounded-full">
                    <i class="fas fa-id-card text-purple-600 text-xl"></i>
                </div>
            </div>

            @if(!$biometricStatus->rfid_assigned)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-yellow-800">RFID card not assigned</p>
                        <p class="text-sm text-yellow-700 mt-1">You need to request an RFID card for access control.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <button onclick="requestRFIDCard()" 
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 px-4 rounded-lg font-semibold flex items-center justify-center transition-colors">
                    <i class="fas fa-id-card-alt mr-2"></i>
                    Request RFID Card
                </button>
                
                <div class="text-center">
                    <button type="button" 
                            onclick="showRFIDInstructions()"
                            class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                        <i class="fas fa-info-circle mr-1"></i>
                        RFID Card Information
                    </button>
                </div>
            </div>
            @else
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-3"></i>
                    <div>
                        <p class="text-sm font-medium text-green-800">RFID card active</p>
                        <p class="text-sm text-green-700 mt-1">
                            Card Number: <span class="font-mono font-bold">{{ $biometricStatus->rfid_code }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <button onclick="reportLostRFID()" 
                        class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded-lg font-semibold flex items-center justify-center transition-colors">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Report Lost/Stolen Card
                </button>
                
                <button onclick="testRFID()" 
                        class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg font-semibold flex items-center justify-center transition-colors">
                    <i class="fas fa-vial mr-2"></i>
                    Test RFID Card
                </button>
            </div>
            @endif
        </div>
    </div>

    <!-- Recent Biometric Logs -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Recent Biometric Activities</h3>
            <p class="text-sm text-gray-600 mt-1">Your recent fingerprint and RFID usage</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentLogs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $log->timestamp->format('M d, Y h:i A') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $log->type === 'fingerprint' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                <i class="fas {{ $log->type === 'fingerprint' ? 'fa-fingerprint' : 'fa-id-card' }} mr-1"></i>
                                {{ ucfirst($log->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $log->device_name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $log->location ?? 'Main Office' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $log->status === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <i class="fas {{ $log->status === 'success' ? 'fa-check' : 'fa-times' }} mr-1"></i>
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                            <i class="fas fa-history text-2xl mb-2 text-gray-300"></i>
                            <p>No biometric activities recorded yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($recentLogs->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $recentLogs->links() }}
        </div>
        @endif
    </div>

    <!-- Support Information -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-headset text-blue-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h4 class="text-lg font-semibold text-blue-900">Need Help with Biometrics?</h4>
                <p class="text-blue-800 mt-1">Contact the IT Support Team for assistance with fingerprint enrollment or RFID card issues.</p>
                <div class="mt-3 flex flex-wrap gap-4">
                    <div class="flex items-center text-sm text-blue-700">
                        <i class="fas fa-phone mr-2"></i>
                        <span>IT Helpdesk: (053) 123-4567</span>
                    </div>
                    <div class="flex items-center text-sm text-blue-700">
                        <i class="fas fa-envelope mr-2"></i>
                        <span>itsupport@bipsu.edu.ph</span>
                    </div>
                    <div class="flex items-center text-sm text-blue-700">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        <span>IT Office, Main Building</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enrollment Instructions Modal -->
<div id="enrollmentInstructionsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center pb-3 border-b">
                <h3 class="text-xl font-semibold text-gray-900">Fingerprint Enrollment Instructions</h3>
                <button onclick="closeModal('enrollmentInstructionsModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mt-4 space-y-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-blue-900 mb-2">Before You Start:</h4>
                    <ul class="list-disc list-inside text-blue-800 space-y-1">
                        <li>Ensure your fingers are clean and dry</li>
                        <li>Remove any gloves or finger coverings</li>
                        <li>Position yourself comfortably in front of the biometric device</li>
                    </ul>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-start">
                        <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mt-1 flex-shrink-0">1</span>
                        <div class="ml-3">
                            <p class="font-medium">Place your finger on the scanner</p>
                            <p class="text-sm text-gray-600 mt-1">Use your preferred finger (usually thumb or index finger)</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mt-1 flex-shrink-0">2</span>
                        <div class="ml-3">
                            <p class="font-medium">Follow the prompts</p>
                            <p class="text-sm text-gray-600 mt-1">The device will guide you through multiple scans for better accuracy</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm font-bold mt-1 flex-shrink-0">3</span>
                        <div class="ml-3">
                            <p class="font-medium">Wait for confirmation</p>
                            <p class="text-sm text-gray-600 mt-1">The system will confirm when enrollment is complete</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-yellow-900 mb-2">Important Notes:</h4>
                    <ul class="list-disc list-inside text-yellow-800 space-y-1">
                        <li>Enrollment typically takes 1-2 minutes</li>
                        <li>You can enroll multiple fingers for backup</li>
                        <li>Contact IT support if you encounter any issues</li>
                    </ul>
                </div>
            </div>
            
            <div class="flex justify-end mt-6">
                <button onclick="closeModal('enrollmentInstructionsModal')" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                    Got it, Start Enrollment
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Modal functions
function showEnrollmentInstructions() {
    document.getElementById('enrollmentInstructionsModal').classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.id === 'enrollmentInstructionsModal') {
        closeModal('enrollmentInstructionsModal');
    }
});

// Biometric functions
function startFingerprintEnrollment() {
    // This would integrate with your biometric device API
    showToast('Connecting to biometric device...', 'info');
    
    // Simulate device connection
    setTimeout(() => {
        showToast('Please place your finger on the scanner', 'info');
        // Actual enrollment logic would go here
    }, 2000);
}

function reEnrollFingerprint() {
    if (confirm('This will replace your current fingerprint enrollment. Continue?')) {
        startFingerprintEnrollment();
    }
}

function testFingerprint() {
    showToast('Testing fingerprint... Please place your finger on the scanner', 'info');
    // Actual test logic would go here
}

function requestRFIDCard() {
    showToast('RFID card request submitted to admin', 'success');
    // Actual request logic would go here
}

function reportLostRFID() {
    if (confirm('Report this RFID card as lost/stolen? This will immediately deactivate the card.')) {
        showToast('RFID card reported as lost/stolen. Please contact IT for replacement.', 'warning');
        // Actual report logic would go here
    }
}

function testRFID() {
    showToast('Please tap your RFID card on the reader', 'info');
    // Actual test logic would go here
}

function showRFIDInstructions() {
    showToast('Visit the IT office to request an RFID card. Bring your employee ID.', 'info');
}

// Toast notification function
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-4 py-2 rounded-md shadow-md text-white z-50 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' : 
        'bg-blue-500'
    }`;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('opacity-0', 'transition-opacity', 'duration-300');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
}
</script>
@endpush