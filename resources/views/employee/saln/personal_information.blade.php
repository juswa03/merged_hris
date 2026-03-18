{{-- Row 1: Filing Type --}}
<tr>
    <td colspan="2" class="text-center">
        <div class="mt-2">
            <small class="text-muted">
                <em>Note: Husband and wife who are both public officials and employees may file jointly or separately.</em>
            </small>
        </div>

        <div class="d-inline-block" style="font-size: 14px;"> {{-- Increased font size --}}
            <label class="me-5" style="font-size: 14px;">
                <input type="radio" class="form-check-input me-1" name="filing_type" value="joint"
                    style="transform: scale(1.0);" {{-- make radio button bigger --}}
                    {{ old('filing_type', $personalInfo->filing_type ?? '') == 'joint' ? 'checked' : '' }}>
                Joint Filing
            </label>

            <label class="me-5" style="font-size: 14px;">
                <input type="radio" class="form-check-input me-1" name="filing_type" value="separate"
                    style="transform: scale(1.0);"
                    {{ old('filing_type', $personalInfo->filing_type ?? '') == 'separate' ? 'checked' : '' }}>
                Separate Filing
            </label>

            <label style="font-size: 14px;">
                <input type="radio" class="form-check-input me-1" name="filing_type" value="not_applicable"
                    style="transform: scale(1.0);"
                    {{ old('filing_type', $personalInfo->filing_type ?? '') == 'not_applicable' ? 'checked' : '' }}>
                Not Applicable
            </label>
        </div>
    </td>
</tr>


<tr>
    <td colspan="2" style="height: 20px; border: none;"></td>
</tr>

{{-- Row 2: Declarant & Position --}}
<tr>

    <td class="left-cell">
        <span class="fw-bold">DECLARANT:</span>

        <div class="name-block">
            <input type="text" class="saln-line name-input" 
                name="last_name"
                value="{{ old('last_name', $user->last_name ?? '') }}" readonly>
            <span class="sub-label">(Family Name)</span>
        </div>

        <div class="name-block">
            <input type="text" class="saln-line name-input"
                name="declarant_first"
                value="{{ old('declarant_first', $user->first_name ?? '') }}" readonly>
            <span class="sub-label">(First Name)</span>
        </div>

        <div class="name-block">
            <input type="text" class="saln-line small" 
                name="declarant_mi"
                value="{{ old('declarant_mi', $user->middle_name ? Str::substr($user->middle_name, 0, 1) : '') }}" readonly>
            <span class="sub-label">(M.I.)</span>
        </div>

    </td>
    <td class="right-cell">
        <span class="fw-bold">POSITION:</span>
        <input type="text" class="saln-line wide" 
            name="position"
            value="{{ old('position', $personalInfo->position ?? '') }}">
    </td>
</tr>

{{-- Row 2: Address & Agency --}}
<tr>
    <td class="left-cell">
        <span class="fw-bold">ADDRESS:</span>
        <input type="text" class="saln-line address-input" name="house_block_lot_no"
            value="{{ old('house_block_lot_no', $permanentAddress->house_block_lot_no ?? '') }}">
        <input type="text" class="saln-line address-input" name="street"
            value="{{ old('street', $permanentAddress->street ?? '') }}">
        <input type="text" class="saln-line address-input" name="subdivision"
            value="{{ old('subdivision', $permanentAddress->subdivision ?? '') }}">
    </td>

<td class="right-cell">
    <span class="fw-bold" style="font-size:12px;">AGENCY/OFFICE:</span>
    <input type="text" class="saln-line wide" name="agency_name"
        value="{{ old('agency_name', $agency->name ?? '') }}">
</td>

</tr>

{{-- Row 3: Office Address --}}
<tr>
    <td>
        <span class="fw-bold"></span>
        <input type="text" class="saln-line address-input" name="barangay"
            value="{{ old('barangay', $permanentAddress->barangay ?? '') }}">
        <input type="text" class="saln-line address-input" name="city"
            value="{{ old('city', $permanentAddress->city ?? '') }}">
        <input type="text" class="saln-line address-input" name="province"
            value="{{ old('province', $permanentAddress->province ?? '') }}">
    </td>
    <td class="right-cell">
        <span class="fw-bold" style="font-size:12px;">OFFICE ADDRESS:</span>
        <input type="text" class="saln-line wide" name="agency_address"
            value="{{ old('agency_address', $agency?->address ?? '') }}">
    </td>
</tr>

{{-- Spacer Row --}}
<tr>
    <td colspan="2" style="height: 40px; border: none;"></td>
</tr>

{{-- Row 1: Spouse & Position --}}
<tr>
    <td class="left-cell">
        <span class="fw-bold">SPOUSE:</span>

        <div class="name-block">
            <input type="text" class="saln-line name-input"
                name="spouse_last"
                value="{{ old('spouse_last', $spouse->last_name ?? '') }}">
            <span class="sub-label">(Family Name)</span>
        </div>

        <div class="name-block">
            <input type="text" class="saln-line name-input"
                name="spouse_first"
                value="{{ old('spouse_first', $spouse->first_name ?? '') }}">
            <span class="sub-label">(First Name)</span>
        </div>

        <div class="name-block">
            <input type="text" class="saln-line small"
                name="spouse_mi"
                value="{{ old('spouse_mi', optional($spouse)->middle_name ? Str::substr(optional($spouse)->middle_name, 0, 1) : '') }}"
            <span class="sub-label">(M.I.)</span>
        </div>
    </td>

    <td class="right-cell">
        <span class="fw-bold">POSITION:</span>
        <input type="text" class="saln-line wide"
            name="spouse_position"
            value="{{ old('spouse_position', $spouse->position ?? '') }}">
    </td>
</tr>

{{-- Row 2: Spouse Agency --}}
<tr>
    <td class="left-cell"></td>
    <td class="right-cell">
        <span class="fw-bold" style="font-size:12px;">AGENCY/OFFICE:</span>
        <input type="text" class="saln-line wide" name="spouse_agency_name"
            value="{{ old('spouse_agency_name', $spouseAgency?->name ?? '') }}">
    </td>
</tr>

{{-- Row 3: Spouse Office Address --}}
<tr>
    <td class="left-cell"></td>
    <td class="right-cell">
        <span class="fw-bold" style="font-size:12px;">OFFICE ADDRESS:</span>
        <input type="text" class="saln-line wide" name="spouse_agency_address"
            value="{{ old('spouse_agency_address', $spouseAgency?->address ?? '') }}">
    </td>
</tr>


{{-- Double Line Separator --}}
<tr>
    <td colspan="2">
        <div class="double-line"></div>
    </td>
</tr>