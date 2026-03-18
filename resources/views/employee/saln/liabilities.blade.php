{{-- resources/views/saln/liabilities.blade.php --}}

<table class="table saln-table">

    {{-- Section Header --}}
    <tr>
        <td colspan="62" class="saln-section-header">
            <span>2. LIABILITIES</span>
        </td>
    </tr>

    <tbody id="liabilities-body">
        {{-- Table Headers --}}
        <tr>
            <td colspan="25" class="saln-label text-center align-middle">
                NATURE
            </td>
            <td colspan="25" class="saln-label text-center align-middle">
                NAME OF CREDITORS
            </td>
            <td colspan="12" class="saln-label text-center align-middle">
                OUTSTANDING BALANCE
            </td>
        </tr>

        {{-- Dynamic Rows --}}
        @php $liabilitiesOld = old('nature_type', []); @endphp

        @if($liabilitiesOld)
            @foreach($liabilitiesOld as $i => $oldNature)
                <tr>
                    <td style="display:none;">
                        <input type="hidden" name="liability_id[]" value="">
                    </td>
                    <td colspan="25"><textarea name="nature_type[]" class="form-control saln-input-borderless auto-resize" required>{{ $oldNature }}</textarea></td>
                    <td colspan="25"><textarea name="name_of_creditors[]" class="form-control saln-input-borderless auto-resize">{{ old("name_of_creditors.$i") }}</textarea></td>
                    <td colspan="12"><textarea name="outstanding_balance[]" class="form-control saln-input-borderless auto-resize number-input" required>{{ old("outstanding_balance.$i") }}</textarea></td>
                </tr>
            @endforeach
        @elseif(isset($liabilities) && $liabilities->count())
            @foreach($liabilities as $liability)
                <tr>
                    <td style="display:none;">
                        <input type="hidden" name="liability_id[]" value="{{ $liability->id }}">
                    </td>
                    <td colspan="25"><textarea name="nature_type[]" class="form-control saln-input-borderless auto-resize">{{ $liability->nature_type }}</textarea></td>
                    <td colspan="25"><textarea name="name_of_creditors[]" class="form-control saln-input-borderless auto-resize">{{ $liability->name_of_creditors }}</textarea></td>
                    <td colspan="12"><textarea name="outstanding_balance[]" class="form-control saln-input-borderless auto-resize number-input">{{ $liability->outstanding_balance }}</textarea></td>
                </tr>
            @endforeach
        @else
            {{-- Default empty row --}}
            <tr>
                <td style="display:none;">
                    <input type="hidden" name="liability_id[]" value="">
                </td>
                <td colspan="25"><textarea name="nature_type[]" class="form-control saln-input-borderless auto-resize" required></textarea></td>
                <td colspan="25"><textarea name="name_of_creditors[]" class="form-control saln-input-borderless auto-resize"></textarea></td>
                <td colspan="12"><textarea name="outstanding_balance[]" class="form-control saln-input-borderless auto-resize number-input" required></textarea></td>
            </tr>
        @endif
    </tbody>
</table>

{{-- Subtotal + Buttons --}}
<div class="d-flex justify-content-between align-items-center me-4 mb-4">
    {{-- Buttons on the left --}}
    <div>
        <button type="button" class="btn btn-sm btn-success me-2" id="addLiabilityBtn">+ Add</button>
        <button type="button" class="btn btn-sm btn-danger" id="removeLiabilityBtn">- Remove</button>
    </div>

    {{-- Subtotal on the right --}}
    <strong>TOTAL LIABILITIES: ₱<span id="liabilitiesSubtotal" class="underline-value">0.00</span></strong>
</div>

{{-- 🔽 Net Worth (Assets – Liabilities) --}}
<div class="d-flex justify-content-end me-4 mb-4">
    <strong>NET WORTH: ₱<span id="netWorthDisplay" class="underline-value">
        {{ isset($total_costs) ? number_format($total_costs->net_worth, 2) : '0.00' }}
    </span></strong>
</div>

{{-- Double Line Separator --}}
<div class="double-line"></div>

{{-- Hidden subtotal field --}}
<input type="hidden" name="liabilities_subtotal" id="liabilities_subtotal">

{{-- Scripts --}}
<script>
function calculateLiabilitiesSubtotal() {
    let subtotal = 0;
    document.querySelectorAll('textarea[name="outstanding_balance[]"]').forEach(t => {
        subtotal += parseFloat(t.value.replace(/,/g, "")) || 0;
    });
    document.getElementById('liabilitiesSubtotal').textContent =
        subtotal.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    document.getElementById('liabilities_subtotal').value = subtotal;

    calculateNetWorth();
}

function calculateNetWorth() {
    let assets = parseFloat(document.getElementById('totalAssetsDisplay')?.textContent.replace(/,/g, "")) || 0;
    let liabilities = parseFloat(document.getElementById('liabilities_subtotal')?.value || 0);
    let netWorth = assets - liabilities;

    document.getElementById('netWorthDisplay').textContent =
        netWorth.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
}

// Input handler
document.addEventListener("input", e => {
    if (e.target.classList.contains("number-input")) {
        let pos = e.target.selectionStart;
        e.target.value = e.target.value.replace(/,/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        e.target.setSelectionRange(pos, pos);
        calculateLiabilitiesSubtotal();
    }
});

// On load
window.addEventListener("DOMContentLoaded", () => {
    calculateLiabilitiesSubtotal();
});

// On submit (clean number inputs)
document.querySelector("form").addEventListener("submit", () => {
    document.querySelectorAll("textarea.number-input").forEach(t => {
        t.value = t.value.replace(/,/g, "");
    });
    calculateLiabilitiesSubtotal();
});

// Add/Remove rows
const liabilitiesBody = document.getElementById("liabilities-body");

document.getElementById("addLiabilityBtn").addEventListener("click", () => {
    liabilitiesBody.insertAdjacentHTML("beforeend", `
        <tr>
            <td style="display:none;"><input type="hidden" name="liability_id[]" value=""></td>
            <td colspan="25"><textarea name="nature_type[]" class="form-control saln-input-borderless auto-resize" required></textarea></td>
            <td colspan="25"><textarea name="name_of_creditors[]" class="form-control saln-input-borderless auto-resize"></textarea></td>
            <td colspan="12"><textarea name="outstanding_balance[]" class="form-control saln-input-borderless auto-resize number-input" required></textarea></td>
        </tr>
    `);
    calculateLiabilitiesSubtotal();
});

document.getElementById("removeLiabilityBtn").addEventListener("click", () => {
    if (liabilitiesBody.rows.length > 1) {
        liabilitiesBody.deleteRow(liabilitiesBody.rows.length - 1);
        calculateLiabilitiesSubtotal();
    }
});
</script>
