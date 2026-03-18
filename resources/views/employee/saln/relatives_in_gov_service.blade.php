{{-- Section Title --}}
<div class="saln-section-title text-center">
    RELATIVES IN THE GOVERNMENT SERVICE
</div>

{{-- Note --}}
<div class="saln-note text-center">
    (Within the Fourth Degree of Consanguinity or Affinity. Include also Bilas, Balae and Inso)
</div>

{{-- ✅ Checkbox for "No Relatives in Gov Service" --}}
<div class="text-center my-3">
    <label class="d-inline-flex align-items-center" style="gap: 6px; cursor: pointer;">
        <input 
            type="checkbox" 
            id="no_relative_in_gov_service" 
            name="no_relative_in_gov_service" 
            value="1"
            class="saln-checkbox"
            {{ old('no_relative_in_gov_service', $no_relative_in_gov_service ?? false) ? 'checked' : '' }}
        >
        <span>I/We do not know of any relative/s in the government service.</span>
    </label>
</div>

{{-- Table --}}
<table class="table saln-table">
    <tbody id="relatives-body">
        {{-- Table Headers --}}
        <tr>
            <td colspan="20" class="saln-label text-center align-middle">NAME OF RELATIVE</td>
            <td colspan="15" class="saln-label text-center align-middle">RELATIONSHIP</td>
            <td colspan="15" class="saln-label text-center align-middle">POSITION</td>
            <td colspan="30" class="saln-label text-center align-middle">NAME OF AGENCY/OFFICE AND ADDRESS</td>
        </tr>

        {{-- Dynamic Rows --}}
        @php $relativeOld = old('name_of_relative', []); @endphp

        {{-- Case 1: old input (validation failed) --}}
        @if($relativeOld)
            @foreach($relativeOld as $i => $oldRelative)
                <tr>
                    <td style="display:none;">
                        <input type="hidden" name="relative_id[]" value="{{ old("relative_id.$i") }}">
                    </td>
                    <td colspan="20">
                        <textarea name="name_of_relative[]" class="form-control saln-input-borderless auto-resize">{{ $oldRelative }}</textarea>
                    </td>
                    <td colspan="15">
                        <textarea name="relationship[]" class="form-control saln-input-borderless auto-resize">{{ old("relationship.$i") }}</textarea>
                    </td>
                    <td colspan="15">
                        <textarea name="position_of_relative[]" class="form-control saln-input-borderless auto-resize">{{ old("position_of_relative.$i") }}</textarea>
                    </td>
                    <td colspan="30">
                        <textarea name="name_of_agency[]" class="form-control saln-input-borderless auto-resize">{{ old("name_of_agency.$i") }}</textarea>
                    </td>
                </tr>
            @endforeach

        {{-- Case 2: existing relatives from DB --}}
        @elseif(isset($relatives_in_gov_service) && $relatives_in_gov_service->count())
            @foreach($relatives_in_gov_service as $relative)
                <tr>
                    <td style="display:none;">
                        <input type="hidden" name="relative_id[]" value="{{ $relative->id }}">
                    </td>
                    <td colspan="20">
                        <textarea name="name_of_relative[]" class="form-control saln-input-borderless auto-resize">{{ $relative->name_of_relative }}</textarea>
                    </td>
                    <td colspan="15">
                        <textarea name="relationship[]" class="form-control saln-input-borderless auto-resize">{{ $relative->relationship }}</textarea>
                    </td>
                    <td colspan="15">
                        <textarea name="position_of_relative[]" class="form-control saln-input-borderless auto-resize">{{ $relative->position_of_relative }}</textarea>
                    </td>
                    <td colspan="30">
                        <textarea name="name_of_agency[]" class="form-control saln-input-borderless auto-resize">{{ $relative->name_of_agency }}</textarea>
                    </td>
                </tr>
            @endforeach

        {{-- Case 3: default empty row --}}
        @else
            <tr>
                <td style="display:none;">
                    <input type="hidden" name="relative_id[]" value="">
                </td>
                <td colspan="20"><textarea name="name_of_relative[]" class="form-control saln-input-borderless auto-resize"></textarea></td>
                <td colspan="15"><textarea name="relationship[]" class="form-control saln-input-borderless auto-resize"></textarea></td>
                <td colspan="15"><textarea name="position_of_relative[]" class="form-control saln-input-borderless auto-resize"></textarea></td>
                <td colspan="30"><textarea name="name_of_agency[]" class="form-control saln-input-borderless auto-resize"></textarea></td>
            </tr>
        @endif
    </tbody>
</table>

{{-- Buttons --}}
<div class="d-flex justify-content-center align-items-center me-4 mb-4">
    <div>
        <button type="button" class="btn btn-sm btn-success me-2" id="addRelativeBtn">+ Add</button>
        <button type="button" class="btn btn-sm btn-danger" id="removeRelativeBtn">- Remove</button>
    </div>
</div>

{{-- Scripts --}}
<script>
const relativesBody = document.getElementById("relatives-body");
const addRelativeBtn = document.getElementById("addRelativeBtn");
const removeRelativeBtn = document.getElementById("removeRelativeBtn");
const noRelativeCheckbox = document.getElementById("no_relative_in_gov_service");

// Add row
addRelativeBtn.addEventListener("click", () => {
    relativesBody.insertAdjacentHTML("beforeend", `
        <tr>
            <td style="display:none;"><input type="hidden" name="relative_id[]" value=""></td>
            <td colspan="20"><textarea name="name_of_relative[]" class="form-control saln-input-borderless auto-resize"></textarea></td>
            <td colspan="15"><textarea name="relationship[]" class="form-control saln-input-borderless auto-resize"></textarea></td>
            <td colspan="15"><textarea name="position_of_relative[]" class="form-control saln-input-borderless auto-resize"></textarea></td>
            <td colspan="30"><textarea name="name_of_agency[]" class="form-control saln-input-borderless auto-resize"></textarea></td>
        </tr>
    `);
});

// Remove row
removeRelativeBtn.addEventListener("click", () => {
    if (relativesBody.rows.length > 1) {
        relativesBody.deleteRow(relativesBody.rows.length - 1);
    }
});

// ✅ Lock/Unlock function
function toggleRelativesTable(disabled) {
    relativesBody.querySelectorAll("textarea, input").forEach(el => {
        el.disabled = disabled;
    });
    addRelativeBtn.disabled = disabled;
    removeRelativeBtn.disabled = disabled;
}

// Run on page load
toggleRelativesTable(noRelativeCheckbox.checked);

// Watch for changes
noRelativeCheckbox.addEventListener("change", function() {
    toggleRelativesTable(this.checked);
});
</script>
