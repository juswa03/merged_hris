<!-- resources/views/pds/relationship_and_legal.blade.php -->

<tr>
    <td colspan="42" class="pds-label">
        34. Are you related by consanguinity or affinity to the appointing or recommending authority,
        or to the chief of bureau or office or to the person who has immediate supervision over you
        in the Office, Bureau or Department where you will be appointed?
        <br>
        <div class="mt-1">
            a. within the third degree?
            @foreach (['yes' => 'YES', 'no' => 'NO'] as $value => $label)
                <div class="form-check form-check-inline ms-2">
                    <input class="form-check-input within-degree" type="radio" name="within_third_degree" value="{{ $value }}"
                        {{ old('within_third_degree', $relationshipToAuthority->within_third_degree ?? '') == $value ? 'checked' : '' }}>
                    <label class="form-check-label">{{ $label }}</label>
                </div>
            @endforeach
        </div>

        <div class="mt-1">
            b. within the fourth degree (for Local Government Unit - Career Employees)?
            @foreach (['yes' => 'YES', 'no' => 'NO'] as $value => $label)
                <div class="form-check form-check-inline ms-2">
                    <input class="form-check-input within-degree" type="radio" name="within_fourth_degree" value="{{ $value }}"
                        {{ old('within_fourth_degree', $relationshipToAuthority->within_fourth_degree ?? '') == $value ? 'checked' : '' }}>
                    <label class="form-check-label">{{ $label }}</label>
                </div>
            @endforeach
        </div>
    </td>

    <td colspan="20" class="align-top" style="font-size:11px;">
        <label>If YES, give details:</label>
        <textarea id="relationship-details" name="details_to_authority" class="form-control form-control-sm mt-1" rows="2"
            {{ 
                (old('within_third_degree', $relationshipToAuthority->within_third_degree ?? '') !== 'yes' && 
                 old('within_fourth_degree', $relationshipToAuthority->within_fourth_degree ?? '') !== 'yes') 
                 ? 'disabled' : '' 
            }}
        >{{ old('details_to_authority', $relationshipToAuthority->details_to_authority ?? '') }}</textarea>
    </td>
</tr>

<tr>
    <td colspan="42" class="pds-label" style="font-size:11px;">
        35.
        <div class="mt-1">
            a. Have you ever been found guilty of any administrative offense?
            @foreach (['yes' => 'YES', 'no' => 'NO'] as $value => $label)
                <div class="form-check form-check-inline ms-2">
                    <input class="form-check-input admin-offense" type="radio" 
                           name="has_admin_offense" value="{{ $value }}"
                           {{ old('has_admin_offense', $legalCase->has_admin_offense ?? '') == $value ? 'checked' : '' }}>
                    <label class="form-check-label">{{ $label }}</label>
                </div>
            @endforeach
        </div>

        <div class="mt-1">
            b. Have you been criminally charged before any court?
            @foreach (['yes' => 'YES', 'no' => 'NO'] as $value => $label)
                <div class="form-check form-check-inline ms-2">
                    <input class="form-check-input criminal-charge" type="radio" 
                           name="has_criminal_case" value="{{ $value }}"
                           {{ old('has_criminal_case', $legalCase->has_criminal_case ?? '') == $value ? 'checked' : '' }}>
                    <label class="form-check-label">{{ $label }}</label>
                </div>
            @endforeach
        </div>
    </td>

    <td colspan="20" class="align-top" style="font-size:11px;">
        <div class="mb-2">
            <label>If YES, give details:</label>
            <textarea id="admin-offense-details" name="offense_details" 
                class="form-control form-control-sm mt-1" rows="2">{{ old('offense_details', $legalCase->offense_details ?? '') }}</textarea>
        </div>

        <hr class="my-1">

        <div class="row">
            <div class="col-6">
                <label>Date Filed:</label>
                <input type="date" id="criminal-charge-date" name="date_filed" 
                    class="form-control form-control-sm"
                    value="{{ old('date_filed', $legalCase->date_filed ?? '') }}">
            </div>
            <div class="col-6">
                <label>Status of Case/s:</label>
                <input type="text" id="criminal-charge-status" name="status_of_case" 
                    class="form-control form-control-sm"
                    value="{{ old('status_of_case', $legalCase->status_of_case ?? '') }}">
            </div>
        </div>
    </td>
</tr>

<tr>
    <td colspan="42" class="pds-label">
        36. Have you ever been convicted of any crime or violation of any law, decree,
        ordinance or regulation by any court or tribunal?
        @foreach (['yes' => 'YES', 'no' => 'NO'] as $value => $label)
            <div class="form-check form-check-inline ms-2">
                <input class="form-check-input conviction-radio" type="radio" 
                       name="has_been_convicted" value="{{ $value }}"
                       {{ old('has_been_convicted', $legalCase->has_been_convicted ?? '') == $value ? 'checked' : '' }}>
                <label class="form-check-label">{{ $label }}</label>
            </div>
        @endforeach
    </td>
    <td colspan="20" class="align-top" style="font-size:11px;">
        <label>If YES, give details:</label>
        <textarea id="conviction-details" name="conviction_details" 
            class="form-control form-control-sm mt-1" rows="2">{{ old('conviction_details', $legalCase->conviction_details ?? '') }}</textarea>
    </td>
</tr>

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
        <textarea id="employment-separation-details" name="details_of_separation" 
                  class="form-control form-control-sm mt-1" rows="2"
                  {{ old('has_been_separated', $employmentSeparation->has_been_separated ?? '') !== 'yes' ? 'disabled' : '' }}
        >{{ old('details_of_separation', $employmentSeparation->details_of_separation ?? '') }}</textarea>
    </td>
</tr>

<tr>
    <td colspan="42" class="pds-label align-middle" style="font-size:11px;">
        38. a. Have you ever been a candidate in a national or local election (except Barangay election)?
        @foreach (['yes' => 'YES', 'no' => 'NO'] as $value => $label)
            <div class="form-check form-check-inline ms-2">
                <input class="form-check-input candidate-radio" type="radio" 
                       name="has_been_candidate" value="{{ $value }}"
                       {{ old('has_been_candidate', $politicalActivity->has_been_candidate ?? '') == $value ? 'checked' : '' }}>
                <label class="form-check-label">{{ $label }}</label>
            </div>
        @endforeach

        <div class="mt-2">
            b. Have you resigned from government service during the campaign period so you can run for public office?
            @foreach (['yes' => 'YES', 'no' => 'NO'] as $value => $label)
                <div class="form-check form-check-inline ms-2">
                    <input class="form-check-input campaign-radio" type="radio" 
                           name="has_resigned_for_campaigning" value="{{ $value }}"
                           {{ old('has_resigned_for_campaigning', $politicalActivity->has_resigned_for_campaigning ?? '') == $value ? 'checked' : '' }}>
                    <label class="form-check-label">{{ $label }}</label>
                </div>
            @endforeach
        </div>
    </td>

    <td colspan="20" class="align-top" style="font-size:11px;">
        <div class="mb-2">
            <label>If YES, give details:</label>
            <textarea id="election-details" name="election_details" 
                class="form-control form-control-sm mt-1" rows="2"
            >{{ old('election_details', $politicalActivity->election_details ?? '') }}</textarea>
        </div>

        <hr class="my-1">

        <div class="mb-2">
            <label>If YES, give details:</label>
            <textarea id="campaign-details" name="campaign_details" 
                class="form-control form-control-sm mt-1" rows="2"
            >{{ old('campaign_details', $politicalActivity->campaign_details ?? '') }}</textarea>
        </div>
    </td>
</tr>

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

<tr>
    <td colspan="42" class="pds-label" style="font-size:11px;">
        40. Special Statuses
        <div class="mt-1">
            a. Are you a member of an Indigenous group?
            @foreach(['yes' => 'YES', 'no' => 'NO'] as $value => $label)
                <div class="form-check form-check-inline ms-2">
                    <input class="form-check-input indigenous-radio" type="radio" 
                           name="is_indigenous_member" value="{{ $value }}"
                           {{ old('is_indigenous_member', $specialStatus->is_indigenous_member ?? '') == $value ? 'checked' : '' }}>
                    <label class="form-check-label">{{ $label }}</label>
                </div>
            @endforeach
        </div>

        <div class="mt-1">
            b. Are you a person with disability (PWD)?
            @foreach(['yes' => 'YES', 'no' => 'NO'] as $value => $label)
                <div class="form-check form-check-inline ms-2">
                    <input class="form-check-input pwd-radio" type="radio" 
                           name="is_person_with_disability" value="{{ $value }}"
                           {{ old('is_person_with_disability', $specialStatus->is_person_with_disability ?? '') == $value ? 'checked' : '' }}>
                    <label class="form-check-label">{{ $label }}</label>
                </div>
            @endforeach
        </div>

        <div class="mt-1">
            c. Are you a solo parent?
            @foreach(['yes' => 'YES', 'no' => 'NO'] as $value => $label)
                <div class="form-check form-check-inline ms-2">
                    <input class="form-check-input solo-parent-radio" type="radio" 
                           name="is_solo_parent" value="{{ $value }}"
                           {{ old('is_solo_parent', $specialStatus->is_solo_parent ?? '') == $value ? 'checked' : '' }}>
                    <label class="form-check-label">{{ $label }}</label>
                </div>
            @endforeach
        </div>
    </td>

    <td colspan="20" class="align-top" style="font-size:11px;">
        <div class="mb-2">
            <label>Indigenous Group Name:</label>
            <input type="text" id="indigenous-group-name" class="form-control form-control-sm mt-1" 
                   name="indigenous_group_name" 
                   value="{{ old('indigenous_group_name', $specialStatus->indigenous_group_name ?? '') }}">
        </div>

        <div class="mb-2">
            <label>PWD ID Number:</label>
            <input type="number" id="pwd-id-number" class="form-control form-control-sm mt-1" 
                   name="pwd_id_number" 
                   value="{{ old('pwd_id_number', $specialStatus->pwd_id_number ?? '') }}">
        </div>

        <div class="mb-2">
            <label>Solo Parent ID Number:</label>
            <input type="number" id="solo-parent-id-number" class="form-control form-control-sm mt-1" 
                   name="solo_parent_id_number" 
                   value="{{ old('solo_parent_id_number', $specialStatus->solo_parent_id_number ?? '') }}">
        </div>
    </td>
</tr>


<style>
/* small visual hint for disabled fields */
#relationship-details:disabled,
#admin-offense-details:disabled,
#criminal-charge-date:disabled,
#criminal-charge-status:disabled,
#conviction-details:disabled,
#employment-separation-details:disabled,
#election-details:disabled,
#campaign-details:disabled,
#immigration-country:disabled {
    background-color: #e9ecef;
    cursor: not-allowed;
}
</style>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function toggleField(radiosSelector, fieldIds) {
        const radios = document.querySelectorAll(radiosSelector);
        fieldIds = Array.isArray(fieldIds) ? fieldIds : [fieldIds];

        function update() {
            const yesSelected = [...radios].some(r => r.checked && r.value === 'yes');
            fieldIds.forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;
                el.disabled = !yesSelected;
                if (!yesSelected && (el.tagName.toLowerCase() === 'input' || el.tagName.toLowerCase() === 'textarea' || el.tagName.toLowerCase() === 'select')) {
                    el.value = '';
                }
            });
        }

        radios.forEach(r => r.addEventListener('change', update));
        update();
    }

    // Previous toggles
    toggleField('.within-degree', 'relationship-details');
    toggleField('.admin-offense', ['admin-offense-details']);
    toggleField('.criminal-charge', ['criminal-charge-date', 'criminal-charge-status']);
    toggleField('.conviction-radio', 'conviction-details');
    toggleField('.employment-separation-radio', 'employment-separation-details');
    toggleField('.candidate-radio', 'election-details');
    toggleField('.campaign-radio', 'campaign-details');
    toggleField('.immigration-status-radio', 'immigration-country');

    // Special Statuses toggles
    toggleField('.indigenous-radio', 'indigenous-group-name');
    toggleField('.pwd-radio', 'pwd-id-number');
    toggleField('.solo-parent-radio', 'solo-parent-id-number');
});
</script>
@endpush