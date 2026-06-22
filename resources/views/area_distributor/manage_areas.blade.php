<div class="modal fade" id="manageAreaModal-{{ $ad->id }}"  tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        {{-- <form method="POST" action="{{ url('ad/'.$ad->id.'/areas/update') }}"> --}}
        <form id="areaForm-{{ $ad->id }}" data-ad="{{ $ad->id }}" method="POST" action="{{ url('ad/'.$ad->id.'/areas/update') }}">
            @csrf

            <div class="modal-content border-0 shadow-lg">

                <div class="modal-header bg-primary">
                    <div>
                        <h4 class="modal-title mb-0 text-white">
                            <i class="ti ti-map-pin me-2"></i>
                            Manage Awarded Areas
                        </h4>

                        <small class="text-white">
                            {{ $ad->name }}
                        </small>
                    </div>

                    <button type="button"
                            class="close text-white border-0 bg-transparent"
                            data-bs-dismiss="modal">
                        &times;
                    </button>
                </div>

                <div class="modal-body bg-light">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h5 class="mb-1">Awarded Area(s)</h5>

                            <small class="text-muted">
                                Add or update project assigned areas
                            </small>
                        </div>

                        <button type="button"
                                class="btn btn-primary add-area-btn"
                                data-ad="{{ $ad->id }}" onclick="addAreaRow({{ $ad->id }})">

                            <i class="ti ti-plus"></i>
                            Add Area
                        </button>
                    </div>

                    <div id="areaRows-{{ $ad->id }}">
                        @php
                            $areaOptionNames = $areas->pluck('name')->map(function ($name) {
                                return trim((string) $name);
                            });
                        @endphp

                        @forelse($ad->areas as $index => $area)
                            @php
                                $projectType = trim((string) $area->project_type);
                                $projectTypeKey = strtolower($projectType);
                                $areaName = trim((string) $area->area_name);
                            @endphp

                            <div class="card border-0 shadow-sm mb-3 area-row">

                                <div class="card-body">

                                    <input type="hidden"
                                           name="rows[{{ $index }}][id]"
                                           value="{{ $area->id }}">

                                    <div class="row">

                                        {{-- <div class="col-md-3 mb-3">
                                            <label>Project Type</label>

                                            <select class="form-control select2"
                                                    name="rows[{{ $index }}][project_type]"
                                                    required>
                                                 @php
                                                    $selectedProjectType = trim((string) $area->project_type);
                                                    // $selectedAreaName = trim((string) $area->area_name);
                                                @endphp
                                                <option value="" {{ $selectedProjectType === '' ? 'selected' : '' }}>
                                                    Select Project
                                                </option>
                                               
                                                <option value="Project Rise"
                                                    {{ $selectedProjectType === 'Project Rise' ? 'selected' : '' }}>
                                                    Project Rise
                                                </option>

                                                <option value="Project Genesis"
                                                    {{ $selectedProjectType === 'Project Genesis' ? 'selected' : '' }}>
                                                    Project Genesis
                                                </option>

                                            </select>
                                        </div> --}}
                                        @php
                                            $projectType = trim((string) ($area->project_type ?? ''));
                                        @endphp

                                        <div class="col-md-3 mb-3">

                                            <label class="form-label">
                                                Project Type
                                            </label>

                                            <select class="form-control"
                                                    name="rows[{{ $index }}][project_type]"
                                                    required>

                                                <option value="">
                                                    Select Project
                                                </option>

                                                <option value="Project Rise"
                                                    {{ $projectType === 'Project Rise' ? 'selected' : '' }}>
                                                    Project Rise
                                                </option>

                                                <option value="Project Genesis"
                                                    {{ $projectType === 'Project Genesis' ? 'selected' : '' }}>
                                                    Project Genesis
                                                </option>

                                            </select>

                                        </div>
                                            
                                        {{-- <div class="col-md-5 mb-3">

                                            <label>Area Name</label>

                                            <select class="form-control select2"
                                                    name="rows[{{ $index }}][area_name]"
                                                    required>

                                                <option value="">
                                                    Select Area
                                                </option>

                                                @if($selectedAreaName !== '' && ! $areaOptionNames->contains($selectedAreaName))
                                                    <option value="{{ $selectedAreaName }}" selected>
                                                        {{ $selectedAreaName }}
                                                    </option>
                                                @endif
                                                
                                                @foreach($areas as $a)
                                                    @php
                                                        $optionAreaName = trim((string) $a->name);
                                                    @endphp

                                                    <option value="{{ $optionAreaName }}"
                                                        {{ $selectedAreaName === $optionAreaName ? 'selected' : '' }}>

                                                        {{ $optionAreaName }}

                                                    </option>

                                                @endforeach

                                            </select>

                                        </div> --}}
                                        <div class="col-md-5 mb-3">
                                            <label>Area Name</label>
                                            <select class="form-control area-select"
                                                    name="rows[{{ $index }}][area_name]"
                                                    required>

                                                <option value="">
                                                    Select Area
                                                </option>

                                                @php
                                                    $selectedAreaName = trim((string) $area->area_name);
                                                @endphp

                                                @foreach($areas as $a)

                                                    @php
                                                        $optionAreaName = trim((string) $a->name);
                                                    @endphp

                                                    <option value="{{ $optionAreaName }}"
                                                        {{ strtolower($selectedAreaName) == strtolower($optionAreaName) ? 'selected' : '' }}>

                                                        {{ $optionAreaName }}

                                                    </option>

                                                @endforeach

                                            </select>

                                        </div>

                                        <div class="col-md-3 mb-3">

                                            <label>Joining Date</label>

                                            <input type="date"
                                                   class="form-control"
                                                   name="rows[{{ $index }}][joining_date]"
                                                   value="{{ !empty($area->joining_date) ? \Carbon\Carbon::parse($area->joining_date)->format('Y-m-d') : '' }}">

                                        </div>

                                        <div class="col-md-1 mb-3 d-flex align-items-end">

                                            <button type="button"
                                                    class="btn btn-danger remove-row">

                                                <i class="ti ti-trash"></i>

                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="empty-area text-center py-5 text-muted">

                                <i class="ti ti-map-pin-off fa-3x"></i>

                                <p class="mt-2 mb-0">
                                    No awarded areas yet
                                </p>

                            </div>

                        @endforelse

                    </div>

                </div>

                <div class="modal-footer bg-white">

                    <button type="button"
                            class="btn btn-light border"
                            data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button type="submit"
                            class="btn btn-success px-4">

                        <i class="ti ti-device-floppy me-1"></i>
                        Save Areas

                    </button>

                </div>

            </div>

        </form>
    </div>
</div>

<?php if (empty($GLOBALS['manageAreaScriptRendered'])): ?>
<?php $GLOBALS['manageAreaScriptRendered'] = true; ?>
<script>
    window.manageAreaOptions = @json($areas->pluck('name')->values());

    (function () {

        function initAreaModalManager() {
            if (typeof window.jQuery === 'undefined') {
                return setTimeout(initAreaModalManager, 50);
            }

            var $ = window.jQuery;

            // prevent double loading in Laravel (VERY IMPORTANT)
            if (window.AreaModalManager) return;

            window.AreaModalManager = true;

            /**
             * Initialize Select2
             */
            function initializeSelect2(scope) {
                if (!$.fn.select2) {
                    return;
                }

                $(scope).find('.select2').each(function () {

                    if ($(this).hasClass('select2-hidden-accessible')) return;

                    $(this).select2({
                        width: '100%',
                        dropdownParent: $(this).closest('.modal')
                    });

                });
            }

            function escapeHtml(value) {
                return String(value === null || typeof value === 'undefined' ? '' : value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            window.addAreaRow = function (adId) {

                let container = $('#areaRows-' + adId);

                container.find('.empty-area').remove();

                let index = Date.now(); // still unique but numeric-safe
                let areaOptions = (window.manageAreaOptions || [])
                    .map(function (name) {
                        let safeName = escapeHtml(name);
                        return `<option value="${safeName}">${safeName}</option>`;
                    })
                    .join('');

                let html = `
                    <div class="card border-0 shadow-sm mb-3 area-row">
                        <div class="card-body">
                            <div class="row">

                                <input type="hidden" name="rows[${index}][id]" value="">

                                <div class="col-md-3">
                                    <select class="form-control select2"
                                            name="rows[${index}][project_type]" required>
                                        <option value="" disabled>Select Project</option>
                                        <option value="Project Rise">Project Rise</option>
                                        <option value="Project Genesis">Project Genesis</option>
                                    </select>
                                </div>

                                <div class="col-md-5">
                                    <select class="form-control select2"
                                            name="rows[${index}][area_name]" required>
                                        <option value="">Select Area</option>
                                        ${areaOptions}
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <input type="date"
                                        class="form-control"
                                        name="rows[${index}][joining_date]">
                                </div>

                                <div class="col-md-1 d-flex align-items-center">
                                    <button type="button" class="btn btn-danger remove-row">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                `;

                let row = $(html);
                container.append(row);

                initializeSelect2(row);
            };

            /**
             * Remove Row
             */
            $(document).on('click', '.remove-row', function () {
                let row = $(this).closest('.area-row');
                let modal = row.closest('.modal');
                let container = modal.find('[id^="areaRows-"]');

                // destroy select2 only in the removed row
                row.find('select.select2').each(function () {
                    if ($.fn.select2 && $(this).data('select2')) {
                        $(this).select2('destroy');
                    }
                });

                row.remove();

                if (container.find('.area-row').length === 0) {
                    container.html(`
                        <div class="empty-area text-center py-5 text-muted">
                            <i class="ti ti-map-pin-off fa-3x"></i>
                            <p class="mt-2 mb-0">No awarded areas yet</p>
                        </div>
                    `);
                }
            });

            /**
             * Init on modal open (IMPORTANT FIX)
             */
            $(document).on('shown.bs.modal', '.modal', function () {
                initializeSelect2(this);
            });
                
            /**
             * Initial page load
             */
            $(document).ready(function () {
                initializeSelect2(document);
            });

            $(document).on('submit', 'form[id^="areaForm-"]', function (e) {

                e.preventDefault();

                let form = $(this);
                let url = form.attr('action');
                let btn = form.find('button[type="submit"]');

                btn.prop('disabled', true).html('Saving...');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: form.serialize(), // THIS IS NOW SAFE

                    success: function (res) {

                        if (res.status) {

                            alert(res.message);

                            let modalEl = document.getElementById(
                                'manageAreaModal-' + form.data('ad')
                            );

                            let modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                            if (modal) modal.hide();

                            window.location.reload();
                        } else {
                            alert(res.message || 'Unable to update areas');
                        }
                    },

                    error: function (xhr) {
                        alert(xhr.responseJSON?.message || 'Error occurred');
                    },

                    complete: function () {
                        btn.prop('disabled', false)
                            .html('<i class="ti ti-device-floppy me-1"></i> Save Areas');
                    }
                });

            });
        }

        window.addEventListener('load', initAreaModalManager);
    })();
</script>
<?php endif; ?>

<?php if (empty($GLOBALS['manageAreaStyleRendered'])): ?>
<?php $GLOBALS['manageAreaStyleRendered'] = true; ?>
<style>

.select2-container {
    width: 100% !important;
}

.select2-dropdown {
    z-index: 999999 !important;
}

.modal {
    overflow: visible !important;
}

.modal-dialog {
    overflow: visible !important;
}

.modal-content {
    overflow: visible !important;
}

</style>
<?php endif; ?>
