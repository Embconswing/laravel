@php
    $selectedService = old('service', $selectedService ?? null);
@endphp

<select name="service" class="form-select" required>

    <option value="">— Select Service —</option>

    {{-- ================= VISA ================= --}}
    <option value="Visa Services"
        {{ $selectedService == 'Visa Services' ? 'selected' : '' }}>
        Visa
    </option>

    {{-- ================= OCI ================= --}}
    <optgroup label="OCI Services">
        <option value="Fresh OCI"
            {{ $selectedService == 'Fresh OCI' ? 'selected' : '' }}>
            Fresh OCI
        </option>

        <option value="Miscellaneous OCI"
            {{ $selectedService == 'Miscellaneous OCI' ? 'selected' : '' }}>
            Miscellaneous OCI
        </option>

        <option value="PIO to OCI"
            {{ $selectedService == 'PIO to OCI' ? 'selected' : '' }}>
            PIO to OCI
        </option>
    </optgroup>

    {{-- ================= PASSPORT ================= --}}
    <optgroup label="Passport Services">

        <option value="Passport - New / Issue / Reissue / Lost"
            {{ $selectedService == 'Passport - New / Issue / Reissue / Lost' ? 'selected' : '' }}>
            New / Issue / Reissue / Lost
        </option>

        <option value="Emergency Certificate"
            {{ $selectedService == 'Emergency Certificate' ? 'selected' : '' }}>
            Emergency Certificate
        </option>

        <option value="Passport PCC"
            {{ $selectedService == 'Passport PCC' ? 'selected' : '' }}>
            Police Clearance Certificate for Indian Nationals
        </option>

        <option value="Passport Surrender Certificate"
            {{ $selectedService == 'Passport Surrender Certificate' ? 'selected' : '' }}>
            Surrender Certificate of Indian Passport
        </option>

        <option value="Birth Registration & Fresh Passport"
            {{ $selectedService == 'Birth Registration & Fresh Passport' ? 'selected' : '' }}>
            Birth Registration and Fresh Passport to New Born
        </option>
    </optgroup>

    {{-- ================= MISCELLANEOUS ================= --}}
    <optgroup label="Miscellaneous Consular Services">

        <option value="Attestation / Legalisation of Documents"
            {{ $selectedService == 'Attestation / Legalisation of Documents' ? 'selected' : '' }}>
            Attestation / Legalisation of Documents
        </option>

        <option value="Consular Surrender Certificate"
            {{ $selectedService == 'Consular Surrender Certificate' ? 'selected' : '' }}>
            Surrender Certificate
        </option>

        <option value="NRI Certificate"
            {{ $selectedService == 'NRI Certificate' ? 'selected' : '' }}>
            NRI Certificate
        </option>

        <option value="Consular Police Clearance Certificate"
            {{ $selectedService == 'Consular Police Clearance Certificate' ? 'selected' : '' }}>
            Police Clearance Certificate
        </option>

        <option value="Life Certificate"
            {{ $selectedService == 'Life Certificate' ? 'selected' : '' }}>
            Life Certificate
        </option>

        <option value="Birth Certificate as per Indian Passport"
            {{ $selectedService == 'Birth Certificate as per Indian Passport' ? 'selected' : '' }}>
            Birth Certificate as per Indian Passport
        </option>

        <option value="Name Change Certificate"
            {{ $selectedService == 'Name Change Certificate' ? 'selected' : '' }}>
            Name Change Certificate
        </option>

        <option value="NOC for Naming of New Born Child"
            {{ $selectedService == 'NOC for Naming of New Born Child' ? 'selected' : '' }}>
            No Objection Certificate for Naming of New Born Child
        </option>

    </optgroup>

</select>