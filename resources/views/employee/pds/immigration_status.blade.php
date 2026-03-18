<tr>
    <td colspan="42" class="pds-label align-middle" style="font-size:11px;">
        39. Do you have immigrant or permanent resident status in another country?

        @foreach (['yes' => 'YES', 'no' => 'NO'] as $value => $label)
            <div class="form-check form-check-inline ms-2">
                <input class="form-check-input immigration-status-radio" type="radio" 
                       name="has_immigrant_status" value="{{ $value }}"
                       {{ old('has_immigrant_status', $personalInfo->immigrationStatus->has_immigrant_status ?? '') == $value ? 'checked' : '' }}>
                <label class="form-check-label">{{ $label }}</label>
            </div>
        @endforeach
    </td>

    <td colspan="20" class="align-top" style="font-size:11px;">
        <label>Select Country:</label>
        <select name="country_id" id="immigration-country" class="form-select form-select-sm mt-1">
            <option value="">-- Select Country --</option>
            @foreach($countries as $country)
                <option value="{{ $country->id }}"
                    {{ old('country_id', $personalInfo->immigrationStatus->country_id ?? '') == $country->id ? 'selected' : '' }}>
                    {{ $country->name }}
                </option>
            @endforeach
        </select>
    </td>
</tr>



@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('.immigration-status-radio');
        const countrySelect = document.getElementById('immigration-country');

        function toggleCountry() {
            const isYes = [...radios].some(radio => radio.checked && radio.value === 'yes');
            countrySelect.disabled = !isYes;
            if (!isYes) countrySelect.value = ''; // clear if NO
        }

        radios.forEach(radio => radio.addEventListener('change', toggleCountry));
        toggleCountry(); // run on page load
    });
</script>
@endpush
