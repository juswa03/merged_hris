<!-- resources/views/saln/certification_and_oath.blade.php -->

<div class="page">
    <form method="POST" action="{{ route('saln.update') }}">
        @csrf

        <p style="text-align: justify; text-indent: 40px; font-size: 16px;">
            I hereby certify that these are true and correct statements of my assets, liabilities, net worth,
            business interests and financial connections, including those of my spouse and unmarried children below
            eighteen (18) years of age living in my household, and that to the best of my knowledge, the above-
            enumerated are names of my relatives in the government within the fourth civil degree of consanguinity or
            affinity.
        </p>

        <p style="text-align: justify; text-indent: 40px; font-size: 16px;">
            I hereby authorize the Ombudsman or his/her duly authorized representative to obtain and
            secure from all appropriate government agencies, including the Bureau of Internal Revenue such
            documents that may show my assets, liabilities, net worth, business interests and financial connections,
            to include those of my spouse and unmarried children below 18 years of age living with me in my
            household covering previous years to include the year I first assumed office in government.
        </p>

        <p style="margin-top: 20px; font-size: 16px;">
            Date: <strong>{{ now()->format('F d, Y') }}</strong>
        </p>
<table style="width: 100%; margin-top: 40px; font-size: 14px; border-collapse: collapse;">

<tr>
    <!-- Declarant Signature -->
    <td style="width: 50%; text-align: center; vertical-align: top;">
        <div style="display: flex; flex-direction: column; align-items: center;">
            @if(!empty($saln_certification->signature_declarant))
                <img src="{{ asset('storage/' . $saln_certification->signature_declarant) }}" 
                     alt="Declarant Signature" 
                     style="width: 150px; height: auto; margin-bottom: 15px;">
            @else
                <img id="previewDeclarant" 
                     src="#" 
                     alt="Preview" 
                     style="display:none; width:150px; height:auto; margin-bottom:-50px;">
            @endif

            <!-- Underline (kept very close) -->
            <div style="width: 80%; border-bottom: 1px solid #000; height: 15px; margin-bottom: 3px;"></div>

            <!-- File input -->
            <input type="file" name="signature_declarant" accept="image/*" 
                   onchange="previewSignature(event, 'previewDeclarant')" 
                   style="display:block; margin:5px auto; font-size: 13px;">

            <em>(Signature of Declarant)</em>
        </div>
    </td>

    <!-- Spouse Signature -->
    <td style="width: 50%; text-align: center; vertical-align: top;">
        <div style="display: flex; flex-direction: column; align-items: center;">
            @if(!empty($saln_certification->signature_spouse))
                <img src="{{ asset('storage/' . $saln_certification->signature_spouse) }}" 
                     alt="Spouse Signature" 
                     style="width: 150px; height: auto; margin-bottom: 1px;">
            @else
                <img id="previewSpouse" 
                     src="#" 
                     alt="Preview" 
                     style="display:none; width:150px; height:auto; margin-bottom:-50px;">
            @endif

            <!-- Underline (kept very close) -->
            <div style="width: 80%; border-bottom: 1px solid #000; height: 15px; margin-bottom: 3px;"></div>

            <!-- File input -->
            <input type="file" name="signature_spouse" accept="image/*" 
                   onchange="previewSignature(event, 'previewSpouse')" 
                   style="display:block; margin:5px auto; font-size: 13px; ">

            <em>(Signature of Co-Declarant/Spouse)</em>
        </div>
    </td>
</tr>



    <tr>
        <!-- Declarant Gov ID -->
        <td style="padding-top: 25px; vertical-align: top;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 45%; text-align: left; padding-right: 10px;">Government Issued ID:</td>
                    <td><input type="text" name="gov_id_declarant"
                               value="{{ old('gov_id_declarant', $saln_certification->gov_id_declarant ?? '') }}"
                               style="width: 90%; border: none; border-bottom: 1px solid #000; font-size: 14px; text-align: left;"></td>
                </tr>
                <tr>
                    <td style="text-align: left; padding-right: 10px;">ID No.:</td>
                    <td><input type="text" name="id_no_declarant"
                               value="{{ old('id_no_declarant', $saln_certification->id_no_declarant ?? '') }}"
                               style="width: 90%; border: none; border-bottom: 1px solid #000; font-size: 14px; text-align: left;"></td>
                </tr>
                <tr>
                    <td style="text-align: left; padding-right: 10px;">Date Issued:</td>
                    <td><input type="date" name="date_issued_declarant"
                               value="{{ old('date_issued_declarant', $saln_certification->date_issued_declarant ?? '') }}"
                               style="width: 90%; border: none; border-bottom: 1px solid #000; font-size: 14px; text-align: left;"></td>
                </tr>
            </table>
        </td>

        <!-- Spouse Gov ID -->
        <td style="padding-top: 25px; vertical-align: top;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 45%; text-align: left; padding-right: 10px;">Government Issued ID:</td>
                    <td><input type="text" name="gov_id_spouse"
                               value="{{ old('gov_id_spouse', $saln_certification->gov_id_spouse ?? '') }}"
                               style="width: 90%; border: none; border-bottom: 1px solid #000; font-size: 14px; text-align: left;"></td>
                </tr>
                <tr>
                    <td style="text-align: left; padding-right: 10px;">ID No.:</td>
                    <td><input type="text" name="id_no_spouse"
                               value="{{ old('id_no_spouse', $saln_certification->id_no_spouse ?? '') }}"
                               style="width: 90%; border: none; border-bottom: 1px solid #000; font-size: 14px; text-align: left;"></td>
                </tr>
                <tr>
                    <td style="text-align: left; padding-right: 10px;">Date Issued:</td>
                    <td><input type="date" name="date_issued_spouse"
                               value="{{ old('date_issued_spouse', $saln_certification->date_issued_spouse ?? '') }}"
                               style="width: 90%; border: none; border-bottom: 1px solid #000; font-size: 14px; text-align: left;"></td>
                </tr>
            </table>
        </td>
    </tr>
</table>



        <p style="margin-top: 40px; font-size: 13px;">
            <strong>SUBSCRIBED AND SWORN</strong> to before me this _____ day of _______,
            affiant exhibiting to me the above-stated government issued identification card.
        </p>

        <div style="margin-top: 50px; text-align: center; font-size: 13px;">
            ___________________________________<br>
            <em>(Person Administering Oath)</em>
        </div>

        <div style="text-align: center; margin-top: 40px; font-style: italic; font-size: 12px;">
            Page 2 of ___
        </div>

    </form>
</div>

<script>
function previewSignature(event, previewId) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById(previewId);
        output.src = reader.result;
        output.style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>