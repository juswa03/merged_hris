<!-- resources/views/pds/employment_separation.blade.php -->

<tr>
    <td colspan="42" class="pds-label align-middle" style="font-size:11px;">
        37. Have you ever been separated from the service in any of the following modes: 
        resignation, retirement, dropped from the rolls, dismissal, termination, end of term, 
        finished contract or phased out (abolition) in the public or private sector?

        @foreach (['yes' => 'YES', 'no' => 'NO'] as $value => $label)
            <div class="form-check form-check-inline ms-2">
                <input class="form-check-input employment-separation-radio" type="radio" 
                       name="has_been_separated" value="{{ $value }}"
                       {{ old('has_been_separated', $employmentSeparation->has_been_separated ?? '') == $value ? 'checked' : '' }}>
                <label class="form-check-label">{{ $label }}</label>
            </div>
        @endforeach
    </td>

    <td colspan="20" class="align-top" style="font-size:11px;">
    <label>If YES, give details:</label>
    <textarea id="employment-separation-details" name="separation_details" 
          class="form-control form-control-sm mt-1" rows="2"
          {{ old('has_been_separated', $employmentSeparation->has_been_separated ?? '') !== 'yes' ? 'disabled' : '' }}
    >{{ old('separation_details', $employmentSeparation->details ?? '') }}</textarea>
    </td>
</tr>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('.employment-separation-radio');
        const textarea = document.getElementById('employment-separation-details');

        function toggleTextarea() {
            const isYesSelected = [...radios].some(radio => radio.checked && radio.value === 'yes');
            
            if (isYesSelected) {
                textarea.disabled = false;
            } else {
                textarea.value = ''; // clear value if NO
                textarea.disabled = true;
            }
        }

        radios.forEach(radio => {
            radio.addEventListener('change', toggleTextarea);
        });

        // Run once on page load
        toggleTextarea();
    });
</script>
@endpush
