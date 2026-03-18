{{-- Section Title --}}
<div class="saln-section-title text-center">
    BUSINESS INTERESTS AND FINANCIAL CONNECTIONS
</div>

{{-- Note --}}
<div class="saln-note text-center">
    (of Declarant / Declarant's spouse / Unmarried Children Below (18) years of Age Living in Declarant's Household)<br>
</div>

{{-- ✅ Checkbox for "No Business Interest" --}}
<div class="text-center my-3">
    <label class="d-inline-flex align-items-center" style="gap: 6px; cursor: pointer;">
        <input 
            type="checkbox" 
            id="no_business_interest" 
            name="no_business_interest" 
            value="1"
            class="saln-checkbox"
            {{ old('no_business_interest', $no_business_interest) ? 'checked' : '' }}
        >
        <span>I/We do not have any business interest or financial connection.</span>
    </label>
</div>
<input type="hidden" name="reporting_year" value="{{ $reportingYear ?? date('Y') }}">


{{-- Subsection Header --}}
<table class="table saln-table">
    <tbody id="business-interests-body">
        {{-- Table Headers --}}
        <tr>
            <td colspan="20" class="saln-label text-center align-middle">
                NAME OF ENTITY / BUSINESS ENTERPRISE
            </td>
            <td colspan="20" class="saln-label text-center align-middle">
                BUSINESS ADDRESS
            </td>
            <td colspan="12" class="saln-label text-center align-middle">
                NATURE OF BUSINESS INTEREST &amp;/OR FINANCIAL CONNECTION
            </td>
            <td colspan="10" class="saln-label text-center align-middle">
                DATE OF ACQUISITION
            </td>
        </tr>
        

        {{-- Dynamic Rows --}}
        @php $businessOld = old('name_of_business', []); @endphp

        @if($businessOld)
            @foreach($businessOld as $i => $oldBusiness)
                <tr>
                    <td style="display:none;">
                        <input type="hidden" name="business_interest_id[]" value="">
                    </td>
                    <td colspan="20"><textarea name="name_of_business[]" class="form-control saln-input-borderless auto-resize">{{ $oldBusiness }}</textarea></td>
                    <td colspan="20"><textarea name="business_address[]" class="form-control saln-input-borderless auto-resize">{{ old("business_address.$i") }}</textarea></td>
                    <td colspan="12"><textarea name="name_of_business_interest[]" class="form-control saln-input-borderless auto-resize">{{ old("name_of_business_interest.$i") }}</textarea></td>
                    <td colspan="10"><input type="date" name="date_of_acquisition[]" class="form-control saln-input-borderless" value="{{ old("date_of_acquisition.$i") }}"></td>
                </tr>
            @endforeach
        @elseif(isset($business_interests) && $business_interests->count())
            @foreach($business_interests as $business)
                <tr>
                    <td style="display:none;">
                        <input type="hidden" name="business_interest_id[]" value="{{ $business->id }}">
                    </td>
                    <td colspan="20"><textarea name="name_of_business[]" class="form-control saln-input-borderless auto-resize">{{ $business->name_of_business }}</textarea></td>
                    <td colspan="20"><textarea name="business_address[]" class="form-control saln-input-borderless auto-resize">{{ $business->business_address }}</textarea></td>
                    <td colspan="12"><textarea name="name_of_business_interest[]" class="form-control saln-input-borderless auto-resize">{{ $business->name_of_business_interest }}</textarea></td>
                    <td colspan="10"><input type="date" name="date_of_acquisition[]" class="form-control saln-input-borderless" value="{{ $business->date_of_acquisition }}"></td>
                </tr>
            @endforeach
        @else
            {{-- Default empty row if no data --}}
            <tr>
                <td style="display:none;">
                    <input type="hidden" name="business_interest_id[]" value="">
                </td>
                <td colspan="20"><textarea name="name_of_business[]" class="form-control saln-input-borderless auto-resize"></textarea></td>
                <td colspan="20"><textarea name="business_address[]" class="form-control saln-input-borderless auto-resize"></textarea></td>
                <td colspan="12"><textarea name="name_of_business_interest[]" class="form-control saln-input-borderless auto-resize"></textarea></td>
                <td colspan="10"><input type="date" name="date_of_acquisition[]" class="form-control saln-input-borderless"></td>
            </tr>
        @endif
    </tbody>
</table>

{{-- Buttons --}}
<div class="d-flex justify-content-center align-items-center me-4 mb-4">
    <div>
        <button type="button" class="btn btn-sm btn-success me-2" id="addBusinessBtn">+ Add</button>
        <button type="button" class="btn btn-sm btn-danger" id="removeBusinessBtn">- Remove</button>
    </div>
</div>

{{-- Scripts --}}
<script>
const businessBody = document.getElementById("business-interests-body");
const addBusinessBtn = document.getElementById("addBusinessBtn");
const removeBusinessBtn = document.getElementById("removeBusinessBtn");
const noBusinessCheckbox = document.getElementById("no_business_interest");

// Add row
addBusinessBtn.addEventListener("click", () => {
    businessBody.insertAdjacentHTML("beforeend", `
        <tr>
            <td style="display:none;"><input type="hidden" name="business_interest_id[]" value=""></td>
            <td colspan="20"><textarea name="name_of_business[]" class="form-control saln-input-borderless auto-resize"></textarea></td>
            <td colspan="20"><textarea name="business_address[]" class="form-control saln-input-borderless auto-resize"></textarea></td>
            <td colspan="12"><textarea name="name_of_business_interest[]" class="form-control saln-input-borderless auto-resize"></textarea></td>
            <td colspan="10"><input type="date" name="date_of_acquisition[]" class="form-control saln-input-borderless"></td>
        </tr>
    `);
});

// Remove row
removeBusinessBtn.addEventListener("click", () => {
    if (businessBody.rows.length > 1) {
        businessBody.deleteRow(businessBody.rows.length - 1);
    }
});

// ✅ Lock/Unlock function
function toggleBusinessTable(disabled) {
    businessBody.querySelectorAll("textarea, input").forEach(el => {
        el.disabled = disabled;
    });
    addBusinessBtn.disabled = disabled;
    removeBusinessBtn.disabled = disabled;
}

// Run on page load
toggleBusinessTable(noBusinessCheckbox.checked);

// Watch for changes
noBusinessCheckbox.addEventListener("change", function() {
    toggleBusinessTable(this.checked);
});
</script>
