<div class="modal-header">
    <div><h5 class="modal-title text-white">{{ $chief ? 'Edit Center Chief' : 'Add Center Chief' }}</h5><p>Assign one chief to one or more centers.</p></div>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body"><div class="row">
    <div class="col-12"><label for="{{ $formId }}Name">Full name <span class="text-danger">*</span></label><input id="{{ $formId }}Name" class="form-control" name="name" value="{{ old('name', optional($chief)->name) }}" required></div>
    <div class="col-md-5"><label for="{{ $formId }}Mfi">MFI <span class="text-danger">*</span></label><select id="{{ $formId }}Mfi" class="form-select" name="mfi" required><option value="">Select MFI</option>@foreach($mfis as $mfi)<option value="{{ $mfi }}" {{ old('mfi', optional($chief)->mfi) === $mfi ? 'selected' : '' }}>{{ $mfi }}</option>@endforeach</select></div>
    <div class="col-md-7">
        <label for="{{ $formId }}CenterSearch">Centers <span class="text-danger">*</span> <small class="chief-selection-count" data-count-for="{{ $formId }}">Choose one or more</small></label>
        @php($chiefCenterIds = $chief ? $chief->centers->pluck('id')->map(function ($id) { return (int) $id; })->all() : [])
        @php($selectedCenters = array_map('intval', (array) old('center_ids', $chiefCenterIds)))
        <div class="chief-center-picker" data-center-picker="{{ $formId }}">
            <div class="chief-picker-search"><i class="bi bi-search"></i><input id="{{ $formId }}CenterSearch" type="search" placeholder="Search centers" autocomplete="off"></div>
            <div class="chief-picker-options">
                @foreach($centers as $center)
                    @php($assignedTo = $centerAssignments[$center->id] ?? null)
                    @php($isAssignedElsewhere = $assignedTo && (!$chief || !in_array($center->id, $chiefCenterIds, true)))
                    <div class="chief-picker-option {{ $isAssignedElsewhere ? 'is-assigned' : '' }}" data-center-option data-mfi="{{ strtoupper(trim((string) $center->mfi)) }}" data-name="{{ strtolower($center->name) }}" data-label="{{ $center->name }}" data-disabled="{{ $isAssignedElsewhere ? 'true' : 'false' }}" role="checkbox" aria-checked="{{ in_array($center->id, $selectedCenters, true) ? 'true' : 'false' }}" tabindex="{{ $isAssignedElsewhere ? '-1' : '0' }}">
                        <input type="checkbox" name="center_ids[]" value="{{ $center->id }}" {{ in_array($center->id, $selectedCenters, true) ? 'checked' : '' }} tabindex="-1">
                        <span class="chief-picker-check"><i class="bi bi-check"></i></span><span>{{ $center->name }}</span><small>{{ $isAssignedElsewhere ? 'Assigned to ' . $assignedTo : $center->mfi }}</small>
                    </div>
                @endforeach
                <div class="chief-picker-empty" hidden><i class="bi bi-search"></i>No centers match this MFI or search.</div>
            </div>
        </div>
        <div class="chief-selected-preview" data-selected-preview="{{ $formId }}" hidden></div>
        <small class="chief-picker-help">Click each center to select it. Centers assigned to another chief are unavailable.</small>
    </div>
    <div class="col-md-6"><label for="{{ $formId }}Email">Email <span class="text-danger">*</span></label><input id="{{ $formId }}Email" class="form-control" type="email" name="email" value="{{ old('email', optional($chief)->email) }}" placeholder="name@example.com" required></div>
    <div class="col-md-6"><label for="{{ $formId }}Number">Mobile number</label><input id="{{ $formId }}Number" class="form-control" name="number" value="{{ old('number', optional($chief)->number) }}" placeholder="09XXXXXXXXX" maxlength="11"></div>
    <div class="col-12"><div class="chief-address-heading"><i class="bi bi-geo-alt"></i><span>Address details</span></div></div>
    <div class="col-12"><label for="{{ $formId }}Street">Street name, building, house no. <span class="text-danger">*</span></label><input id="{{ $formId }}Street" class="form-control" name="street_address" value="{{ old('street_address', optional($chief)->street_address) }}" placeholder="e.g., 1868 Kapalaran St" required></div>
    <div class="col-md-6"><label for="{{ $formId }}Region">Region <span class="text-danger">*</span></label><select id="{{ $formId }}Region" class="form-select chief-location-region" name="location_region" data-current="{{ old('location_region', optional($chief)->location_region) }}" required><option value="">Loading regions…</option></select></div>
    <div class="col-md-6"><label for="{{ $formId }}Province">Province <span class="text-danger">*</span></label><select id="{{ $formId }}Province" class="form-select chief-location-province" name="location_province" data-current="{{ old('location_province', optional($chief)->location_province) }}" required disabled><option value="">Select region first</option></select></div>
    <div class="col-md-6"><label for="{{ $formId }}City">City / Municipality <span class="text-danger">*</span></label><select id="{{ $formId }}City" class="form-select chief-location-city" name="location_city" data-current="{{ old('location_city', optional($chief)->location_city) }}" required disabled><option value="">Select province first</option></select></div>
    <div class="col-md-6"><label for="{{ $formId }}Barangay">Barangay <span class="text-danger">*</span></label><select id="{{ $formId }}Barangay" class="form-select chief-location-barangay" name="location_barangay" data-current="{{ old('location_barangay', optional($chief)->location_barangay) }}" required disabled><option value="">Select city first</option></select></div>
    <div class="col-12"><label for="{{ $formId }}Area">Sales Territory <span class="text-danger">*</span></label><select id="{{ $formId }}Area" class="form-select select2" name="area" data-territory-select data-location-region="#{{ $formId }}Region" data-location-province="#{{ $formId }}Province" data-location-city="#{{ $formId }}City" data-location-barangay="#{{ $formId }}Barangay" required><option value="">Select sales territory</option>@foreach($areas as $area)<option value="{{ $area->name }}" {{ old('area', optional($chief)->area) === $area->name ? 'selected' : '' }}>{{ $area->name }}</option>@endforeach</select><small class="chief-territory-status d-none" data-territory-status></small></div>
    @if(! $chief || ! $chief->user_id)<div class="col-12"><div class="chief-default-password"><i class="bi bi-key"></i><span>User account password: <strong>12345678</strong></span></div></div>@endif
    <div class="col-12"><label for="{{ $formId }}Status">Status <span class="text-danger">*</span></label><select id="{{ $formId }}Status" class="form-select" name="status" required><option value="Active" {{ old('status', optional($chief)->status ?: 'Active') === 'Active' ? 'selected' : '' }}>Active</option><option value="Inactive" {{ old('status', optional($chief)->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option></select></div>
</div></div>
<div class="modal-footer"><button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button><button class="btn btn-primary" type="submit"><i class="bi bi-check-lg me-1"></i>{{ $chief ? 'Save changes' : 'Add Center Chief' }}</button></div>
