{{-- Section Header --}}
<tr>
    <td colspan="2" class="saln-section-label">
        UNMARRIED CHILDREN BELOW EIGHTEEN (18) YEARS OF AGE LIVING IN DECLARANT’S HOUSEHOLD
    </td>
</tr>

{{-- Spacer --}}
<tr>
    <td colspan="2" class="saln-spacer"></td>
</tr>

{{-- Children Table --}}
<tr>
    <td colspan="2">
        <table class="children-table" id="childrenTable">
            <thead>
                <tr>
                    <td>NAME</td>
                    <td>DATE OF BIRTH</td>
                    <td>AGE</td>
                    <td>LIVING WITH DECLARANT</td>
                </tr>
            </thead>
            <tbody>
                @php
                    // ✅ Safely filter children below 18 even if $personalInfo is null
                    $eligibleChildren = optional($personalInfo)->children
                        ? $personalInfo->children->filter(function ($child) {
                            return $child->date_of_birth
                                ? \Carbon\Carbon::parse($child->date_of_birth)->age < 18
                                : false;
                        })
                        : collect(); // empty collection if null
                @endphp

                @forelse($eligibleChildren as $child)
                    <tr data-child-id="{{ $child->id }}">
                        <td>
                            <input type="text" name="children[{{ $child->id }}][full_name]"
                                   class="saln-line child-input text-center"
                                   value="{{ $child->full_name }}" placeholder="Enter name">
                        </td>
                        <td>
                            <input type="date" name="children[{{ $child->id }}][date_of_birth]"
                                   class="saln-line child-input text-center"
                                   value="{{ $child->date_of_birth ? \Carbon\Carbon::parse($child->date_of_birth)->format('Y-m-d') : '' }}">
                        </td>
                        <td>
                            <input type="text" class="saln-line child-input age-input text-center"
                                   value="{{ $child->date_of_birth ? \Carbon\Carbon::parse($child->date_of_birth)->age : '' }}" readonly>
                        </td>
                        <td>
                            <input type="checkbox" name="children[{{ $child->id }}][is_living_with_declarant]" value="1"
                                   {{ old("children.$child->id.is_living_with_declarant", $child->is_living_with_declarant) ? 'checked' : '' }}>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">N/A</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Buttons under the table --}}
        <div class="text-center mt-2">
            <button type="button" id="addChildBtn" class="btn btn-sm btn-success">+ Add Row</button>
            <button type="button" id="removeChildBtn" class="btn btn-sm btn-danger">- Remove Row</button>
        </div>
    </td>
</tr>

{{-- Double Line Separator --}}
<tr>
    <td colspan="2">
        <div class="double-line"></div>
    </td>
</tr>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const tableBody = document.querySelector("#childrenTable tbody");
    const addBtn = document.getElementById("addChildBtn");
    const removeBtn = document.getElementById("removeChildBtn");

    let rowIndex = tableBody.rows.length;

    // Add Child Row
    addBtn.addEventListener("click", function () {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>
                <input type="text" name="children[new_${rowIndex}][full_name]" 
                       class="saln-line child-input text-center" placeholder="Enter name">
            </td>
            <td>
                <input type="date" name="children[new_${rowIndex}][date_of_birth]" 
                       class="saln-line child-input text-center">
            </td>
            <td>
                <input type="text" class="saln-line child-input age-input text-center" 
                       value="Auto" readonly>
            </td>
            <td>
                <input type="checkbox" name="children[new_${rowIndex}][is_living_with_declarant]" value="1">
            </td>
        `;
        tableBody.appendChild(row);
        rowIndex++;
    });

    // Remove Last Row (existing or new)
    removeBtn.addEventListener("click", function () {
        if (tableBody.rows.length > 0) {
            const lastRow = tableBody.rows[tableBody.rows.length - 1];

            // If it's an existing child, mark it for deletion
            if (lastRow.dataset.childId) {
                const childId = lastRow.dataset.childId;
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'deleted_children[]';
                input.value = childId;
                tableBody.appendChild(input);
            }

            // Remove the row from table
            tableBody.deleteRow(tableBody.rows.length - 1);
            rowIndex--;
        }
    });
});
</script>
