<div id="edit_area_distributor-{{ $ad->id }}" class="modal fade modal-select2" tabindex="-1" aria-labelledby="bs-example-modal-md" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header d-flex align-items-center">
                <h4 class="modal-title" id="myModalLabel">
                    Edit Partner Information
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ url('edit-ads/'.$ad->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="latitude" id="hidden_latitude_{{ $ad->id }}" value="{{ $ad->latitude }}">
                <input type="hidden" name="longitude" id="hidden_longitude_{{ $ad->id }}" value="{{ $ad->longitude }}">
                <div class="modal-body">
                    <div class="row">
                        @php
                            $selectedTypes = is_array($ad->userAds->type)
                                ? $ad->userAds->type
                                : json_decode($ad->userAds->type, true);
                        @endphp

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Store Code</label>
                            <input type="text" class="form-control mb-2" id="store_code" name="store_code" placeholder="Enter Store Code" value="{{ $ad->store_code }}" readonly>
                            {{-- <label class="form-label fw-semibold">Project Tag</label>
                            <div class="border rounded p-2 bg-light">
                                <label class="project-card">
                                    <input type="checkbox"
                                        class="project-type"
                                        name="type[]"
                                        value="Project Rise"
                                        {{ in_array('Project Rise', $selectedTypes ?? []) ? 'checked' : '' }}>
                                    <span><i class="bi bi-graph-up text-success"></i> Project Rise</span>
                                </label>
                                <label class="project-card">
                                    <input type="checkbox"
                                        class="project-type"
                                        name="type[]"
                                        value="Project Genesis"
                                        {{ in_array('Project Genesis', $selectedTypes ?? []) ? 'checked' : '' }}>
                                    <span><i class="bi bi-lightning text-primary"></i> Project Genesis</span>
                                </label>
                                <label class="project-card">
                                    <input type="checkbox"
                                        class="project-type"
                                        name="type[]"
                                        value="Regular"
                                        {{ in_array('Regular', $selectedTypes ?? []) ? 'checked' : '' }}>
                                    <span><i class="bi bi-person-badge text-warning"></i> Regular</span>
                                </label>
                            </div> --}}
                        </div>

                        <div class="col-md-6 text-center">
                            <div class="avatar-wrapper mx-auto mb-2">
                                <img id="avatar-{{ $ad->id }}" 
                                    src="{{ $ad->avatar ? asset($ad->avatar) : asset('design/assets/images/profile/user-1.png') }}">
                            </div>
                            <label for="inputImage-{{ $ad->id }}" class="btn btn-outline-primary btn-sm">
                                <i class="ti ti-upload"></i> Upload Image
                            </label>

                            <input type="file" 
                                name="avatar"
                                id="inputImage-{{ $ad->id }}"
                                hidden
                                accept="image/*"
                                onchange="uploadImage(this, {{ $ad->id }})">

                            <small class="d-block text-muted mt-1">
                                JPG, PNG (Max: 2MB)
                            </small>
                        </div>
                        {{-- <div class="col-md-12 mb-2">
                            <label class="form-label" for="name">Full Name&nbsp;<span class="text-danger">*</span></label>
                            <input type="text" class="form-control required" id="name" name="name" placeholder="Enter Full Name" value="{{ $ad->name }}" required/>
                        </div> --}}
                        <div class="col-md-4 mb-2">
                            <label class="form-label" for="first_name">First Name&nbsp;<span class="text-danger">*</span></label>
                            <input type="text" class="form-control required" id="first_name" name="first_name" placeholder="Enter First Name" data-uppercase value="{{ $ad->userAds->first_name }}" required/>
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label" for="middle_name">Middle Name</label>
                            <input type="text" class="form-control required" id="middle_name" name="middle_name" placeholder="Enter Middle Name" data-uppercase value="{{ $ad->userAds->middle_name }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label" for="last_name">Last Name&nbsp;<span class="text-danger">*</span></label>
                            <input type="text" class="form-control required" id="last_name" name="last_name" placeholder="Enter Last Name" data-uppercase value="{{ $ad->userAds->last_name }}" required/>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="email_address">Email Address&nbsp;<span class="text-danger">*</span></label>
                            <input type="email" class="form-control required" id="email_address" name="email_address" placeholder="Enter Email Address" value="{{ $ad->email_address }}" required/>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="contact_number">Contact Number&nbsp;<span class="text-danger">*</span></label>
                            <input type="number" class="form-control required" id="contact_number" name="contact_number" placeholder="Enter Contact Number" step="0.01" value="{{ $ad->contact_number }}" maxlength="11" required>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="birthdate">Birthdate&nbsp;<span class="text-danger">*</span></label>
                            <input type="date" class="form-control required" id="birthdate" name='birthdate' placeholder="Enter Birthdate" value="{{ $ad->userAds->birthdate }}">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="facebook">Facebook</label>
                            <input type="text" class="form-control required" id="facebook" name='facebook' placeholder="Enter Facebook" data-uppercase value="{{ $ad->facebook }}"/>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="ad-location-panel">
                                <div class="ad-location-title">
                                    <i class="bi bi-geo-alt"></i>
                                    <span>Location Details</span>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 mb-2">
                                        <label class="form-label">Street Name, Building, House No.</label>
                                        <input type="text" class="form-control" name="street_address" id="street_address_{{ $ad->id }}" value="{{ old('street_address', $ad->street_address ?? '') }}" placeholder="e.g., 1868 Kapalaran St" data-uppercase>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Region <span class="text-danger">*</span></label>
                                        <select class="form-select ad-location-select" id="location_region_{{ $ad->id }}" name="location_region" required onclick="event.stopPropagation();">
                                            <option value="">-- Select Region --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Province <span class="text-danger">*</span></label>
                                        <select class="form-select ad-location-select" id="location_province_{{ $ad->id }}" name="location_province" required onclick="event.stopPropagation();" disabled>
                                            <option value="">-- Select Region First --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">City/Municipality <span class="text-danger">*</span></label>
                                        <select class="form-select ad-location-select" id="location_city_{{ $ad->id }}" name="location_city" required onclick="event.stopPropagation();" disabled>
                                            <option value="">-- Select Province First --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Barangay <span class="text-danger">*</span></label>
                                        <select class="form-select ad-location-select"
                                                name="location_barangay"
                                                id="location_barangay_{{ $ad->id }}"
                                                required
                                                onclick="event.stopPropagation();"
                                                disabled>
                                            <option value="">-- Select City First --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label">Zip Code&nbsp;<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="location_zipcode_{{ $ad->id }}" name="zipcode" value="{{ old('zipcode', $ad->zipcode ?? '') }}" readonly required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12 mt-3">
                            @php
                                $savedDeliveryAddress = old('delivery_address', $ad->delivery_address ?? '');
                                $sameAsAddress = trim($savedDeliveryAddress) !== '' && trim($savedDeliveryAddress) === trim($ad->address ?? '');
                            @endphp
                            <div class="ad-delivery-panel">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                    <div class="ad-delivery-title">
                                        <i class="bi bi-truck"></i>
                                        <div>
                                            <span>Delivery Address</span>
                                            <small>Use a separate destination when delivery differs from the partner address.</small>
                                        </div>
                                    </div>
                                    <div class="form-check ad-same-address-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               name="same_as_delivery_address"
                                               id="same_as_delivery_address_{{ $ad->id }}"
                                               value="1"
                                               {{ $sameAsAddress ? 'checked' : '' }}>
                                        <label class="form-check-label" for="same_as_delivery_address_{{ $ad->id }}">
                                            Same as address
                                        </label>
                                    </div>
                                </div>
                                <textarea class="form-control ad-delivery-address-box"
                                          id="delivery_address_{{ $ad->id }}"
                                          name="delivery_address"
                                          rows="3"
                                          placeholder="Enter delivery address"
                                          data-uppercase>{{ $sameAsAddress ? ($ad->address ?? '') : $savedDeliveryAddress }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="business_name">Business Name&nbsp;<span class="text-danger">*</span></label>
                            <input type="text" class="form-control required" id="business_name" name="business_name" placeholder="Enter Business Name" data-uppercase value="{{ $ad->business_name }}" required/>
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label" for="business_type">Business Type&nbsp;<span class="text-danger">*</span></label>
                            {{-- <input type="text" class="form-control required" id="business_type" name="business_type" placeholder="Enter Business Type" value="{{ $ad->business_type }}" required/> --}}
                            <select name="business_type" class="form-control" data-placeholder="Select Business Type" required>
                                <option value="">Select Business Type</option>
                                <option value="Sari Sari Store" @if($ad->business_type == 'Sari Sari Store') selected @endif>Sari Sari Store</option>
                                <option value="Mini Mart" @if($ad->business_type == 'Mini Mart') selected @endif>Mini Mart</option>
                                <option value="Retail Shop" @if($ad->business_type == 'Retail Shop') selected @endif>Retail Shop</option>
                                <option value="Wholesale" @if($ad->business_type == 'Wholesale') selected @endif>Wholesale</option>
                                <option value="Grocery" @if($ad->business_type == 'Grocery') selected @endif>Grocery</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="attachment-{{ $ad->id }}">Attachment</label>
                            <input type="file" class="form-control" id="attachment-{{ $ad->id }}" name="attachment">
                            @if($ad->attachment)
                                <a href="{{ asset($ad->attachment) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="bi bi-paperclip"></i> View current attachment
                                </a>
                            @endif
                        </div>
                         <div class="col-md-6 mb-3">
                            <label class="form-label d-block">Withholding Tax</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="withholding_tax" value="1" {{ $ad->withholding_tax ? 'checked' : '' }}>
                                <label class="form-check-label">Enabled</label>
                            </div>
                        </div>
                        {{-- <div class="col-md-6 mb-2 business-fields">
                            <label class="form-label" for="joining_date">Joining Date<span class="text-danger">*</span></label>
                            <input type="date" class="form-control required" id="joining_date" name="joining_date" placeholder="Enter Joining Date" value="{{ $ad->joining_date }}">
                        </div> --}}
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="Active" @if($ad->status == 'Active') selected @endif>Active</option>
                                <option value="Inactive" @if($ad->status == 'Inactive') selected @endif>Inactive</option>
                            </select>
                        </div>
                        {{-- <div id="dynamic-area-wrapper-{{ $ad->id }}" class="col-md-12 business-fields">
                            <div class="card border-0 shadow-sm rounded-4">
                                <div class="card-body" style="padding: 10px 10px">

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 fw-bold">
                                            <i class="bi bi-map me-2"></i>Awarded Areas Per Project
                                        </h6>

                                        <button type="button"
                                                class="btn btn-sm btn-primary"
                                                onclick="addProjectRow({{ $ad->id }})">
                                            <i class="bi bi-plus-lg"></i> Add Row
                                        </button>
                                    </div>
                                    <div id="projectRows-{{ $ad->id }}"></div>
                                </div>
                            </div>
                        </div> --}}

                        {{-- <template id="project-row-template">
                            <div class="row align-items-end project-row mb-3 border rounded-3 p-3 bg-white">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Joining Date</label>
                                    <input type="date" name="joining_date[]" class="form-control" required>
                                </div>
                                <div class="col-md-4 project-rise-area" style="display:none;">
                                    <label class="form-label fw-semibold">Project Rise Area</label>
                                    <select class="form-control area_name select2" name="area_name_rise[]">
                                        @foreach($centers as $center)
                                            <option value="{{ $center->name }}">{{ $center->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 project-genesis-area" style="display:none;">
                                    <label class="form-label fw-semibold">Project Genesis Area</label>
                                    <select class="form-control area_name select2" name="area_name_genesis[]">
                                        @foreach($centers as $center)
                                            <option value="{{ $center->name }}">{{ $center->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-1 text-center">
                                    <button type="button" class="btn btn-danger remove-row mt-4">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </template> --}}
                        {{-- <template id="project-row-template">

                            <div class="row align-items-end project-row mb-3 border rounded-3 p-3 bg-white">

                                <div class="col-md-3">
                                    <label>Joining Date</label>

                                    <input
                                        type="date"
                                        class="form-control joining-date"
                                    >
                                </div>

                                <div class="col-md-4 project-rise-area d-none">

                                    <label>Project Rise Area</label>

                                    <select class="form-control select2 rise-select">
                                        <option value="">Select Area</option>

                                        @foreach($areas as $area)
                                            <option value="{{ $area->name }}">
                                                {{ $area->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-md-4 project-genesis-area d-none">

                                    <label>Project Genesis Area</label>

                                    <select class="form-control select2 genesis-select">
                                        <option value="">Select Area</option>

                                        @foreach($centers as $center)
                                            <option value="{{ $center->name }}">
                                                {{ $center->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="col-md-1 text-center">
                                    <button
                                        type="button"
                                        class="btn btn-danger remove-row"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                            </div>

                        </template> --}}

                        {{-- <div class="col-md-12 mb-2">
                            <label class="form-label">Awarded Area&nbsp;<span class="text-danger">*</span></label>
                            @php
                                $selectedAreas = $ad->areas->pluck('area_name')->toArray();
                            @endphp

                            <select class="form-control area_name" name="area_name[]" multiple required>
                                @foreach($centers as $center)
                                    <option value="{{ $center->name }}"
                                        {{ in_array($center->name, $selectedAreas) ? 'selected' : '' }}>
                                        {{ $center->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div> --}}
                        <div class="col-md-12">
                            <div class="form-group">
                            <label>Pin Exact Location</span></label>
                            <div class="alert alert-warning d-flex align-items-start" role="alert">
                                <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16" style="min-width: 24px; margin-right: 10px;">
                                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                </svg>
                                <div>
                                    <strong>Place an accurate pin</strong><br>
                                    <small>We will deliver to your map location. Please check if it is correct, else click the map to adjust the pin location.</small>
                                </div>
                            </div>
                            <div id="location_map_{{ $ad->id }}" class="location-map" style="height: 400px; border-radius: 8px; border: 2px solid #dee2e6;"></div>
                                <div class="mt-2 p-2 bg-light rounded">
                                    <strong>Current Pin Location:</strong><br>
                                    Latitude: <span id="display_lat_{{ $ad->id }}">--</span>, Longitude: <span id="display_lng_{{ $ad->id }}">--</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label>Complete Address Preview</label>
                            <textarea class="form-control bg-light" id="full_address_preview_{{ $ad->id }}" rows="2" readonly>{{ $ad->address }}</textarea>
                            <input type="hidden" name="address" id="location_hidden_{{ $ad->id }}" value="{{ $ad->address }}" data-uppercase>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn bg-danger-subtle text-danger  waves-effect"data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn bg-info-subtle text-info  waves-effect">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .location-map {
        height: 400px;
        width: 100%;
    }
    #edit_area_distributor-{{ $ad->id }} .ad-location-panel {
        border: 1px solid #d9e2ec;
        border-radius: 8px;
        background: #f8fbfd;
        padding: 16px;
        margin-bottom: 14px;
    }
    #edit_area_distributor-{{ $ad->id }} .ad-location-title {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 12px;
    }
    #edit_area_distributor-{{ $ad->id }} .ad-location-select:disabled {
        background-color: #eef2f6;
        color: #6b7280;
        cursor: not-allowed;
    }
    #edit_area_distributor-{{ $ad->id }} .ad-delivery-panel {
        border: 1px solid #d7e3f1;
        border-radius: 8px;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        padding: 16px;
        box-shadow: 0 8px 20px rgba(33, 37, 41, 0.04);
    }
    #edit_area_distributor-{{ $ad->id }} .ad-delivery-title {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-weight: 700;
        color: #1f2937;
    }
    #edit_area_distributor-{{ $ad->id }} .ad-delivery-title i {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #e7f1ff;
        color: #0d6efd;
        flex: 0 0 auto;
    }
    #edit_area_distributor-{{ $ad->id }} .ad-delivery-title span,
    #edit_area_distributor-{{ $ad->id }} .ad-delivery-title small {
        display: block;
        line-height: 1.25;
    }
    #edit_area_distributor-{{ $ad->id }} .ad-delivery-title small {
        color: #6b7280;
        font-weight: 400;
        margin-top: 2px;
    }
    #edit_area_distributor-{{ $ad->id }} .ad-same-address-check {
        min-height: 34px;
        display: flex;
        align-items: center;
        gap: 6px;
        border-radius: 8px;
        background: #fff;
        padding: 6px 10px;
    }
    #edit_area_distributor-{{ $ad->id }} .ad-delivery-address-box {
        min-height: 92px;
        resize: vertical;
    }
    #edit_area_distributor-{{ $ad->id }} .ad-delivery-address-box[readonly] {
        background: #eef2f6;
        cursor: not-allowed;
    }
    #edit_area_distributor-{{ $ad->id }} .form-label {
        font-weight: 600;
        color: #374151;
    }
    .avatar-wrapper {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
    }

    .avatar-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Project Box */
    .project-box {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    #edit_area_distributor-{{ $ad->id }} .modal-body {
        max-height: calc(100vh - 180px);
        overflow-y: auto;
    }
</style>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function uploadImage(input, id) {
        const file = input.files[0];

        if (!file) return;

        if (!file.type.startsWith('image/')) {
            Swal.fire('Error', 'Please upload a valid image file.', 'error');
            input.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            Swal.fire('Error', 'Image must be less than 2MB.', 'error');
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('avatar-' + id).src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
</script>

<script>
    (function () {
        const adId = @json($ad->id);
        const modal = document.getElementById(`edit_area_distributor-${adId}`);

        if (!modal) return;

        const BASE_URL = 'https://psgc.cloud/api';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const saved = {
            region: @json($ad->location_region ?? ''),
            province: @json($ad->location_province ?? ''),
            city: @json($ad->location_city ?? ''),
            barangay: @json($ad->location_barangay ?? ''),
            zipcode: @json($ad->zipcode ?? ''),
            address: @json($ad->address ?? ''),
            deliveryAddress: @json($ad->delivery_address ?? ''),
            lat: Number(@json($ad->latitude ?? null)) || 14.6507,
            lng: Number(@json($ad->longitude ?? null)) || 121.0494,
        };

        const fields = {
            street: document.getElementById(`street_address_${adId}`),
            region: document.getElementById(`location_region_${adId}`),
            province: document.getElementById(`location_province_${adId}`),
            city: document.getElementById(`location_city_${adId}`),
            barangay: document.getElementById(`location_barangay_${adId}`),
            zipcode: document.getElementById(`location_zipcode_${adId}`),
            map: document.getElementById(`location_map_${adId}`),
            latText: document.getElementById(`display_lat_${adId}`),
            lngText: document.getElementById(`display_lng_${adId}`),
            latInput: document.getElementById(`hidden_latitude_${adId}`),
            lngInput: document.getElementById(`hidden_longitude_${adId}`),
            addressPreview: document.getElementById(`full_address_preview_${adId}`),
            addressInput: document.getElementById(`location_hidden_${adId}`),
            deliveryAddress: document.getElementById(`delivery_address_${adId}`),
            sameAsDeliveryAddress: document.getElementById(`same_as_delivery_address_${adId}`),
        };

        let initialized = false;
        let map = null;
        let marker = null;
        let currentLat = saved.lat;
        let currentLng = saved.lng;
        let geocodeTimeout = null;
        let zipTimeout = null;

        function normalize(value) {
            return String(value || '').trim().toLowerCase();
        }

        function selectedOption(select) {
            return select?.options?.[select.selectedIndex] || null;
        }

        function selectedText(select) {
            const text = selectedOption(select)?.text?.trim() || '';

            if (!text || text.includes('Select') || text.includes('Loading') || text.includes('Error')) {
                return '';
            }

            return text;
        }

        function selectedCode(select) {
            const option = selectedOption(select);
            return option?.dataset?.code || option?.value || '';
        }

        function setOptions(select, placeholder, items, selectedValue = '') {
            const selected = normalize(selectedValue);
            let matchedValue = '';

            select.innerHTML = `<option value="">${placeholder}</option>`;

            items.forEach(item => {
                const option = document.createElement('option');
                option.value = item.name;
                option.textContent = item.name;

                if (item.code) {
                    option.dataset.code = item.code;
                }

                if (selected && (normalize(item.name) === selected || normalize(item.code) === selected)) {
                    matchedValue = item.name;
                }

                select.appendChild(option);
            });

            select.value = matchedValue;
            select.disabled = false;
        }

        function resetSelect(select, placeholder, disabled = true) {
            select.innerHTML = `<option value="">${placeholder}</option>`;
            select.value = '';
            select.disabled = disabled;
        }

        function isNCR(regionCode, regionName) {
            return String(regionCode || '').startsWith('13') ||
                normalize(regionName).includes('ncr') ||
                normalize(regionName).includes('national capital');
        }

        function syncDeliveryAddress() {
            if (!fields.sameAsDeliveryAddress?.checked || !fields.deliveryAddress) return;

            fields.deliveryAddress.value = fields.addressInput.value || fields.addressPreview.value || '';
        }

        function updateFullAddress() {
            const street = fields.street.value.trim();
            const barangay = selectedText(fields.barangay);
            const city = selectedText(fields.city);
            const province = selectedText(fields.province);
            const region = selectedText(fields.region);
            const zipcode = fields.zipcode.value.trim();

            const parts = [];

            if (street) parts.push(street);
            if (barangay) parts.push(barangay);
            if (city) parts.push(city);

            if (isNCR(selectedCode(fields.region), region)) {
                parts.push('Metro Manila');
            } else if (province) {
                parts.push(province);
            }

            if (zipcode) parts.push(zipcode);
            if (region) parts.push(region);

            const fullAddress = parts.length ? parts.join(', ') : saved.address;

            fields.addressPreview.value = fullAddress;
            fields.addressInput.value = fullAddress;
            syncDeliveryAddress();
        }

        function updateCoordinates(lat, lng, fetchZip = true) {
            currentLat = Number(lat);
            currentLng = Number(lng);

            fields.latText.textContent = currentLat.toFixed(6);
            fields.lngText.textContent = currentLng.toFixed(6);
            fields.latInput.value = currentLat;
            fields.lngInput.value = currentLng;

            if (fetchZip) {
                updateZipCode(currentLat, currentLng);
            }
        }

        function updateZipCode(lat, lng) {
            if (!csrfToken || !lat || !lng) return;

            clearTimeout(zipTimeout);
            zipTimeout = setTimeout(() => {
                fetch("{{ route('get.zipcode') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        latitude: lat,
                        longitude: lng,
                    }),
                })
                    .then(response => response.json())
                    .then(data => {
                        fields.zipcode.value = data.zipcode || fields.zipcode.value || saved.zipcode || '';
                        updateFullAddress();
                    })
                    .catch(error => console.error('Zip code lookup error:', error));
            }, 300);
        }

        function initMap() {
            if (!fields.map || !window.L) return;

            if (map) {
                setTimeout(() => map.invalidateSize(), 200);
                return;
            }

            map = L.map(fields.map).setView([currentLat, currentLng], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19,
            }).addTo(map);

            marker = L.marker([currentLat, currentLng], {
                draggable: true,
            }).addTo(map);

            updateCoordinates(currentLat, currentLng, false);

            marker.on('dragend', function () {
                const position = marker.getLatLng();
                updateCoordinates(position.lat, position.lng);
            });

            map.on('click', function (event) {
                marker.setLatLng(event.latlng);
                updateCoordinates(event.latlng.lat, event.latlng.lng);
            });

            setTimeout(() => map.invalidateSize(), 200);
        }

        async function loadRegions(selectedValue = '') {
            try {
                const response = await fetch(`${BASE_URL}/regions`);
                const regions = await response.json();

                setOptions(fields.region, '-- Select Region --', regions, selectedValue);
            } catch (error) {
                console.error('Error loading regions:', error);
                resetSelect(fields.region, '-- Error loading --', false);
            }
        }

        async function loadNCRCities(selectedValue = '') {
            const regionCode = selectedCode(fields.region);

            if (!regionCode) {
                resetSelect(fields.city, '-- Select Region First --');
                return;
            }

            try {
                const response = await fetch(`${BASE_URL}/regions/${regionCode}/cities-municipalities`);
                const cities = await response.json();

                cities.sort((a, b) => a.name.localeCompare(b.name));
                setOptions(fields.city, '-- Select City --', cities, selectedValue);
            } catch (error) {
                console.error('Error loading NCR cities:', error);
                resetSelect(fields.city, '-- Error loading --', false);
            }
        }

        async function loadProvinces(selectedValue = '') {
            const regionCode = selectedCode(fields.region);
            const regionName = selectedText(fields.region);

            resetSelect(fields.city, '-- Select Province First --');
            resetSelect(fields.barangay, '-- Select City First --');

            if (!regionCode) {
                resetSelect(fields.province, '-- Select Region First --');
                return;
            }

            if (isNCR(regionCode, regionName)) {
                setOptions(fields.province, '-- Select Province --', [
                    { name: 'Metro Manila', code: 'NCR' },
                ], selectedValue || 'Metro Manila');

                await loadNCRCities(saved.city);
                return;
            }

            try {
                const response = await fetch(`${BASE_URL}/regions/${regionCode}/provinces`);
                const provinces = await response.json();

                setOptions(fields.province, '-- Select Province --', provinces, selectedValue);
            } catch (error) {
                console.error('Error loading provinces:', error);
                resetSelect(fields.province, '-- Error loading --', false);
            }
        }

        async function loadCities(selectedValue = '') {
            const provinceCode = selectedCode(fields.province);

            resetSelect(fields.barangay, '-- Select City First --');

            if (!provinceCode) {
                resetSelect(fields.city, '-- Select Province First --');
                return;
            }

            if (provinceCode === 'NCR') {
                await loadNCRCities(selectedValue);
                return;
            }

            try {
                const [citiesResponse, municipalitiesResponse] = await Promise.all([
                    fetch(`${BASE_URL}/provinces/${provinceCode}/cities`),
                    fetch(`${BASE_URL}/provinces/${provinceCode}/municipalities`),
                ]);

                const cities = await citiesResponse.json();
                const municipalities = await municipalitiesResponse.json();
                const allCities = [...cities, ...municipalities].sort((a, b) => a.name.localeCompare(b.name));

                setOptions(fields.city, '-- Select City --', allCities, selectedValue);
            } catch (error) {
                console.error('Error loading cities:', error);
                resetSelect(fields.city, '-- Error loading --', false);
            }
        }

        async function loadBarangays(selectedValue = '') {
            const cityCode = selectedCode(fields.city);

            if (!cityCode) {
                resetSelect(fields.barangay, '-- Select City First --');
                return;
            }

            try {
                const response = await fetch(`${BASE_URL}/cities-municipalities/${cityCode}/barangays`);
                const barangays = await response.json();

                barangays.sort((a, b) => a.name.localeCompare(b.name));
                setOptions(fields.barangay, '-- Select Barangay --', barangays, selectedValue);
            } catch (error) {
                console.error('Error loading barangays:', error);
                resetSelect(fields.barangay, '-- Error loading --', false);
            }
        }

        async function geocodeSelectedBarangay() {
            const barangay = selectedText(fields.barangay);
            const city = selectedText(fields.city);
            const province = selectedText(fields.province);

            if (!barangay || !city || !province || !csrfToken) return;

            try {
                const response = await fetch("{{ route('geocode.location') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        barangay,
                        city,
                        province,
                    }),
                });

                const data = await response.json();

                if (data.success && map && marker) {
                    const lat = Number(data.lat);
                    const lng = Number(data.lng);

                    map.setView([lat, lng], 16);
                    marker.setLatLng([lat, lng]);
                    updateCoordinates(lat, lng);
                }
            } catch (error) {
                console.error('Geocoding error:', error);
            }
        }

        async function initializeLocationFields() {
            if (initialized) return;

            initialized = true;

            fields.zipcode.value = saved.zipcode || fields.zipcode.value;
            fields.addressPreview.value = saved.address;
            fields.addressInput.value = saved.address;

            if (fields.deliveryAddress && fields.sameAsDeliveryAddress) {
                const deliveryMatchesAddress = normalize(fields.deliveryAddress.value) !== '' &&
                    normalize(fields.deliveryAddress.value) === normalize(saved.address);

                fields.sameAsDeliveryAddress.checked = fields.sameAsDeliveryAddress.checked || deliveryMatchesAddress;
                fields.deliveryAddress.readOnly = fields.sameAsDeliveryAddress.checked;

                if (fields.sameAsDeliveryAddress.checked) {
                    syncDeliveryAddress();
                } else if (!fields.deliveryAddress.value && saved.deliveryAddress) {
                    fields.deliveryAddress.value = saved.deliveryAddress;
                }
            }

            await loadRegions(saved.region);
            await loadProvinces(saved.province);

            if (selectedCode(fields.province) !== 'NCR') {
                await loadCities(saved.city);
            }

            await loadBarangays(saved.barangay);

            updateFullAddress();
        }

        fields.region.addEventListener('change', async function () {
            fields.zipcode.value = '';
            await loadProvinces();
            updateFullAddress();
        });

        fields.province.addEventListener('change', async function () {
            fields.zipcode.value = '';
            await loadCities();
            updateFullAddress();
        });

        fields.city.addEventListener('change', async function () {
            fields.zipcode.value = '';
            await loadBarangays();
            updateFullAddress();
        });

        fields.barangay.addEventListener('change', function () {
            clearTimeout(geocodeTimeout);
            geocodeTimeout = setTimeout(geocodeSelectedBarangay, 300);
            updateFullAddress();
        });

        fields.street.addEventListener('input', updateFullAddress);

        fields.sameAsDeliveryAddress?.addEventListener('change', function () {
            fields.deliveryAddress.readOnly = this.checked;
            syncDeliveryAddress();

            if (!this.checked) {
                fields.deliveryAddress.focus();
            }
        });

        modal.addEventListener('shown.bs.modal', async function () {
            await initializeLocationFields();
            initMap();
        });

        modal.querySelector('form')?.addEventListener('submit', function () {
            updateFullAddress();
            syncDeliveryAddress();
        });
    })();
</script>

