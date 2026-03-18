{{-- resources/views/saln/real_properties.blade.php --}}

{{-- Section Title --}}
<div class="saln-section-title text-center">
    ASSETS, LIABILITIES AND NET WORTH
</div>

{{-- Note --}}
<div class="saln-note text-center">
    (Including those of the spouse and unmarried children <br>
    below eighteen (18) years of age living in declarant’s household)
</div>

<table class="table saln-table">
    {{-- Section Header --}}
    <tr>
        <td colspan="62" class="saln-section-header">
            <span>1. ASSETS</span>
        </td>
    </tr>

    {{-- Subsection Header --}}
    <tr>
        <td colspan="62" class="saln-subsection-header">
            <span>a. Real Properties*</span>
        </td>
    </tr>

    <tbody id="real-properties-body">
        {{-- Table Headers --}}
        <tr>
            <td colspan="10" rowspan="2" class="saln-label text-center align-middle">
                DESCRIPTION <br>
                <small>(e.g. lot, house & lot, condominium and improvements)</small>
            </td>
            <td colspan="10" rowspan="2" class="saln-label text-center align-middle">
                KIND <br>
                <small>(e.g. residential, commercial, industrial, agricultural, mixed use)</small>
            </td>
            <td colspan="12" rowspan="2" class="saln-label text-center align-middle">
                EXACT LOCATION
            </td>
            <td colspan="7" class="saln-label text-center align-middle">ASSESSED VALUE</td>
            <td colspan="7" class="saln-label text-center align-middle">CURRENT FAIR MARKET VALUE</td>
            <td colspan="12" class="saln-label text-center align-middle">ACQUISITION</td>
            <td colspan="7" rowspan="2" class="saln-label text-center align-middle">ACQUISITION COST</td>
        </tr>
        <tr>
            <td colspan="14" class="saln-label text-center align-middle">
                <small>(As found in Tax Declaration of Real Properties)</small>
            </td>
            <td colspan="6" class="saln-label text-center align-middle">Year</td>
            <td colspan="6" class="saln-label text-center align-middle">Mode</td>
        </tr>

        {{-- Dynamic Rows --}}
        @php $realOld = old('description', []); @endphp

        @if($realOld)
            @foreach($realOld as $i => $oldDesc)
                <tr>
                    <td style="display:none;">
                        <input type="hidden" name="real_property_id[]" value="">
                    </td>
                    <td colspan="10"><textarea name="description[]" class="form-control saln-input-borderless auto-resize" required>{{ $oldDesc }}</textarea></td>
                    <td colspan="10"><textarea name="kind[]" class="form-control saln-input-borderless auto-resize" required>{{ old("kind.$i") }}</textarea></td>
                    <td colspan="12"><textarea name="location[]" class="form-control saln-input-borderless auto-resize" required>{{ old("location.$i") }}</textarea></td>
                    <td colspan="7"><textarea name="assessed_value[]" class="form-control saln-input-borderless auto-resize number-input" required>{{ old("assessed_value.$i") }}</textarea></td>
                    <td colspan="7"><textarea name="current_fair_market_value[]" class="form-control saln-input-borderless auto-resize number-input" required>{{ old("current_fair_market_value.$i") }}</textarea></td>
                    <td colspan="6"><textarea name="acquisition_year[]" class="form-control saln-input-borderless auto-resize" required>{{ old("acquisition_year.$i") }}</textarea></td>
                    <td colspan="6"><textarea name="acquisition_mode[]" class="form-control saln-input-borderless auto-resize" required>{{ old("acquisition_mode.$i") }}</textarea></td>
                    <td colspan="7"><textarea name="acquisition_cost[]" class="form-control saln-input-borderless auto-resize number-input" required>{{ old("acquisition_cost.$i") }}</textarea></td>
                </tr>
            @endforeach
        @elseif(isset($real_properties) && $real_properties->count())
            @foreach($real_properties as $real)
                <tr>
                    <td style="display:none;">
                        <input type="hidden" name="real_property_id[]" value="{{ $real->id }}">
                    </td>
                    <td colspan="10"><textarea name="description[]" class="form-control saln-input-borderless auto-resize">{{ $real->description }}</textarea></td>
                    <td colspan="10"><textarea name="kind[]" class="form-control saln-input-borderless auto-resize">{{ $real->kind }}</textarea></td>
                    <td colspan="12"><textarea name="location[]" class="form-control saln-input-borderless auto-resize">{{ $real->location }}</textarea></td>
                    <td colspan="7"><textarea name="assessed_value[]" class="form-control saln-input-borderless auto-resize number-input">{{ $real->assessed_value }}</textarea></td>
                    <td colspan="7"><textarea name="current_fair_market_value[]" class="form-control saln-input-borderless auto-resize number-input">{{ $real->current_fair_market_value }}</textarea></td>
                    <td colspan="6"><textarea name="acquisition_year[]" class="form-control saln-input-borderless auto-resize">{{ $real->acquisition_year }}</textarea></td>
                    <td colspan="6"><textarea name="acquisition_mode[]" class="form-control saln-input-borderless auto-resize">{{ $real->acquisition_mode }}</textarea></td>
                    <td colspan="7"><textarea name="acquisition_cost[]" class="form-control saln-input-borderless auto-resize number-input">{{ $real->acquisition_cost }}</textarea></td>
                </tr>
            @endforeach
        @else
            {{-- Default empty row --}}
            <tr>
                <td style="display:none;">
                    <input type="hidden" name="real_property_id[]" value="">
                </td>
                <td colspan="10"><textarea name="description[]" class="form-control saln-input-borderless auto-resize" required></textarea></td>
                <td colspan="10"><textarea name="kind[]" class="form-control saln-input-borderless auto-resize" required></textarea></td>
                <td colspan="12"><textarea name="location[]" class="form-control saln-input-borderless auto-resize" required></textarea></td>
                <td colspan="7"><textarea name="assessed_value[]" class="form-control saln-input-borderless auto-resize number-input" required></textarea></td>
                <td colspan="7"><textarea name="current_fair_market_value[]" class="form-control saln-input-borderless auto-resize number-input" required></textarea></td>
                <td colspan="6"><textarea name="acquisition_year[]" class="form-control saln-input-borderless auto-resize" required></textarea></td>
                <td colspan="6"><textarea name="acquisition_mode[]" class="form-control saln-input-borderless auto-resize" required></textarea></td>
                <td colspan="7"><textarea name="acquisition_cost[]" class="form-control saln-input-borderless auto-resize number-input" required></textarea></td>
            </tr>
        @endif
    </tbody>
</table>

{{-- Subtotal + Buttons --}}
<div class="d-flex justify-content-between align-items-center me-4 mb-4">
    <div>
        <button type="button" class="btn btn-sm btn-success me-2" id="addRealBtn">+ Add</button>
        <button type="button" class="btn btn-sm btn-danger" id="removeRealBtn">- Remove</button>
    </div>
    <strong >Subtotal: ₱<span id="acquisitionCostSubtotal" class="underline-value">0.00</span></strong>
</div>

{{-- 🔽 Hidden subtotal field (renamed to match personal properties script) --}}
<input type="hidden" name="real_acquisition_cost_subtotal" id="real_acquisition_cost_subtotal">

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

function calculateAcquisitionSubtotal() {
    let subtotal = 0;
    document.querySelectorAll('textarea[name="acquisition_cost[]"]').forEach(t => {
        subtotal += parseFloat(cleanNumber(t.value)) || 0;
    });
    document.getElementById('acquisitionCostSubtotal').textContent =
        subtotal.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});

    // 🔽 store in the renamed hidden field
    document.getElementById('real_acquisition_cost_subtotal').value = subtotal;

    // 🔽 update total assets if available
    if (typeof calculateTotalAssets === "function") {
        calculateTotalAssets();
    }
}

// Input handler
document.addEventListener("input", e => {
    if (e.target.tagName === "TEXTAREA") autoResizeTextarea(e.target);
    if (e.target.classList.contains("number-input")) {
        let pos = e.target.selectionStart;
        e.target.value = formatNumber(e.target.value);
        e.target.setSelectionRange(pos, pos);
    }
    if (e.target.name === "acquisition_cost[]") calculateAcquisitionSubtotal();
});

// On load
window.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll("textarea.auto-resize").forEach(autoResizeTextarea);
    document.querySelectorAll("textarea.number-input").forEach(t => t.value = formatNumber(t.value));
    calculateAcquisitionSubtotal();
});

// On submit
document.querySelector("form").addEventListener("submit", () => {
    document.querySelectorAll("textarea.number-input").forEach(t => t.value = cleanNumber(t.value));
    calculateAcquisitionSubtotal();
});

// Add/Remove rows
const body = document.getElementById("real-properties-body");

const realRowTemplate = `
<tr>
    <td style="display:none;"><input type="hidden" name="real_property_id[]" value=""></td>
    <td colspan="10"><textarea name="description[]" class="form-control saln-input-borderless auto-resize" required></textarea></td>
    <td colspan="10"><textarea name="kind[]" class="form-control saln-input-borderless auto-resize" required></textarea></td>
    <td colspan="12"><textarea name="location[]" class="form-control saln-input-borderless auto-resize" required></textarea></td>
    <td colspan="7"><textarea name="assessed_value[]" class="form-control saln-input-borderless auto-resize number-input" required></textarea></td>
    <td colspan="7"><textarea name="current_fair_market_value[]" class="form-control saln-input-borderless auto-resize number-input" required></textarea></td>
    <td colspan="6"><textarea name="acquisition_year[]" class="form-control saln-input-borderless auto-resize" required></textarea></td>
    <td colspan="6"><textarea name="acquisition_mode[]" class="form-control saln-input-borderless auto-resize" required></textarea></td>
    <td colspan="7"><textarea name="acquisition_cost[]" class="form-control saln-input-borderless auto-resize number-input" required></textarea></td>
</tr>
`;

document.getElementById("addRealBtn").addEventListener("click", () => {
    body.insertAdjacentHTML("beforeend", realRowTemplate);
    calculateAcquisitionSubtotal();
});

document.getElementById("removeRealBtn").addEventListener("click", () => {
    if (body.querySelectorAll("tr").length > 1) {
        body.removeChild(body.lastElementChild);
        calculateAcquisitionSubtotal();
    }
});
</script>
