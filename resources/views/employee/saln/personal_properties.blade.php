{{-- resources/views/saln/personal_properties.blade.php --}}

{{-- Subsection Header --}}
<table class="table saln-table">
    {{-- Subsection Header --}}
    <tr>
        <td colspan="62" class="saln-subsection-header">
            <span>b. Personal Properties*</span>
        </td>
    </tr>

    <tbody id="personal-properties-body">
        {{-- Table Headers --}}
        <tr>
            <td colspan="31" class="saln-label text-center align-middle">
                DESCRIPTION
            </td>
            <td colspan="10" class="saln-label text-center align-middle">
                YEAR ACQUIRED
            </td>
            <td colspan="21" class="saln-label text-center align-middle">
                ACQUISITION COST/AMOUNT
            </td>
        </tr>

        {{-- Dynamic Rows --}}
        @php $personalOld = old('description_personal', []); @endphp

        @if($personalOld)
            @foreach($personalOld as $i => $oldDesc)
                <tr>
                    <td style="display:none;">
                        <input type="hidden" name="personal_property_id[]" value="">
                    </td>
                    <td colspan="31"><textarea name="description_personal[]" class="form-control saln-input-borderless auto-resize" required>{{ $oldDesc }}</textarea></td>
                    <td colspan="10"><textarea name="year_acquired_personal[]" class="form-control saln-input-borderless auto-resize">{{ old("year_acquired_personal.$i") }}</textarea></td>
                    <td colspan="21"><textarea name="acquisition_cost_personal[]" class="form-control saln-input-borderless auto-resize number-input" required>{{ old("acquisition_cost_personal.$i") }}</textarea></td>
                </tr>
            @endforeach
        @elseif(isset($personal_properties) && $personal_properties->count())
            @foreach($personal_properties as $personal)
                <tr>
                    <td style="display:none;">
                        <input type="hidden" name="personal_property_id[]" value="{{ $personal->id }}">
                    </td>
                    <td colspan="31"><textarea name="description_personal[]" class="form-control saln-input-borderless auto-resize">{{ $personal->description }}</textarea></td>
                    <td colspan="10"><textarea name="year_acquired_personal[]" class="form-control saln-input-borderless auto-resize">{{ $personal->year_acquired }}</textarea></td>
                    <td colspan="21"><textarea name="acquisition_cost_personal[]" class="form-control saln-input-borderless auto-resize number-input">{{ $personal->acquisition_cost }}</textarea></td>
                </tr>
            @endforeach
        @else
            {{-- Default empty row if no data --}}
            <tr>
                <td style="display:none;">
                    <input type="hidden" name="personal_property_id[]" value="">
                </td>
                <td colspan="31"><textarea name="description_personal[]" class="form-control saln-input-borderless auto-resize" required></textarea></td>
                <td colspan="10"><textarea name="year_acquired_personal[]" class="form-control saln-input-borderless auto-resize"></textarea></td>
                <td colspan="21"><textarea name="acquisition_cost_personal[]" class="form-control saln-input-borderless auto-resize number-input" required></textarea></td>
            </tr>
        @endif
    </tbody>
</table>

{{-- Subtotal + Buttons --}}
<div class="d-flex justify-content-between align-items-center me-4 mb-4">
    {{-- Buttons on the left --}}
    <div>
        <button type="button" class="btn btn-sm btn-success me-2" id="addPersonalBtn">+ Add</button>
        <button type="button" class="btn btn-sm btn-danger" id="removePersonalBtn">- Remove</button>
    </div>

    {{-- Subtotal on the right --}}
    <strong>Subtotal: ₱<span id="personalAcquisitionCostSubtotal" class="underline-value">0.00</span></strong>
</div>

{{-- 🔽 Total Assets from total_costs --}}
<div class="d-flex justify-content-end me-4 mb-4">
    <strong>TOTAL ASSETS (a+b): ₱<span id="totalAssetsDisplay" class="underline-value">
        {{ isset($total_costs) ? number_format($total_costs->total_assets_costs, 2) : '0.00' }}
    </span></strong>
</div>



{{-- Hidden subtotal field --}}
<input type="hidden" name="personal_acquisition_cost_subtotal" id="personal_acquisition_cost_subtotal">

{{-- Scripts --}}
<script>
function autoResizeTextarea(el) {
    el.style.height = "auto";
    el.style.height = Math.max(el.scrollHeight, parseInt(window.getComputedStyle(el).minHeight) || 0) + "px";
}

function formatNumber(value) {
    value = value.replace(/,/g, "");
    let parts = value.split(".");
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    return parts.join(".");
}

function cleanNumber(value) {
    return value.replace(/,/g, "");
}

function calculatePersonalSubtotal() {
    let subtotal = 0;
    document.querySelectorAll('textarea[name="acquisition_cost_personal[]"]').forEach(t => {
        subtotal += parseFloat(cleanNumber(t.value)) || 0;
    });
    document.getElementById('personalAcquisitionCostSubtotal').textContent =
        subtotal.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('personal_acquisition_cost_subtotal').value = subtotal;

    calculateTotalAssets(); // 🔥 update total assets real-time
}

function calculateTotalAssets() {
    let real = parseFloat(document.getElementById('real_acquisition_cost_subtotal')?.value || 0);
    let personal = parseFloat(document.getElementById('personal_acquisition_cost_subtotal')?.value || 0);
    let total = real + personal;

    document.getElementById('totalAssetsDisplay').textContent =
        total.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

// Input handler
document.addEventListener("input", e => {
    if (e.target.tagName === "TEXTAREA") autoResizeTextarea(e.target);
    if (e.target.classList.contains("number-input")) {
        let pos = e.target.selectionStart;
        e.target.value = formatNumber(e.target.value);
        e.target.setSelectionRange(pos, pos);
    }
    if (e.target.name === "acquisition_cost_personal[]") calculatePersonalSubtotal();
});

// On load
window.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("textarea.auto-resize").forEach(autoResizeTextarea);
    document.querySelectorAll("textarea.number-input").forEach(t => t.value = formatNumber(t.value));
    calculatePersonalSubtotal();
    calculateTotalAssets(); // ensure initial total assets is computed
});

// On submit
document.querySelector("form").addEventListener("submit", () => {
    document.querySelectorAll("textarea.number-input").forEach(t => t.value = cleanNumber(t.value));
    calculatePersonalSubtotal();
    calculateTotalAssets();
});

// Add/Remove rows
const personalBody = document.getElementById("personal-properties-body");

document.getElementById("addPersonalBtn").addEventListener("click", () => {
    personalBody.insertAdjacentHTML("beforeend", `
        <tr>
            <td style="display:none;"><input type="hidden" name="personal_property_id[]" value=""></td>
            <td colspan="31"><textarea name="description_personal[]" class="form-control saln-input-borderless auto-resize" required></textarea></td>
            <td colspan="10"><textarea name="year_acquired_personal[]" class="form-control saln-input-borderless auto-resize"></textarea></td>
            <td colspan="21"><textarea name="acquisition_cost_personal[]" class="form-control saln-input-borderless auto-resize number-input" required></textarea></td>
        </tr>
    `);
    calculatePersonalSubtotal();
    calculateTotalAssets();
});

document.getElementById("removePersonalBtn").addEventListener("click", () => {
    if (personalBody.rows.length > 1) {
        personalBody.deleteRow(personalBody.rows.length - 1);
        calculatePersonalSubtotal();
        calculateTotalAssets();
    }
});
</script>
