@extends('layouts.header')
<link rel="icon" type="image/png" href="{{asset('images/logo_nya.png')}}">
@section('css')
<style>
    .btn-view {
        width: 100px;
        font-size: 14px;
    }
    .dashboard-stats {
        display: flex;
        justify-content: space-around;
    }
    .dashboard-stats div {
        text-align: center;
        padding: 20px;
        background-color: #f8f9fa;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 30%;
    }
    .welcome {
        margin-top: 20px;
    }
    .card-header {
        font-size: 1.25rem;
        font-weight: bold;
    }
    .card-body {
        padding: 20px;
    }
    .filter-container {
        margin-bottom: 20px;
    }
    .dataTables_length select {
        width: 55px !important;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
@endsection
@section('content')
<section class="welcome">
    <div class="row">
        <!-- Total Sales -->
        <div class="col-sm-6 col-lg-4 col-xl-2">
            <div class="card warning-card overflow-hidden text-bg-primary w-100">
                <div class="card-body p-4">
                    <div class="mb-7">
                        <i class="ti ti-user-check fs-8 fw-lighter"></i> <!-- Active icon -->
                    </div>
                    <h5 class="text-white fw-bold fs-14 text-nowrap">
                        {{ $activeAds }}
                    </h5>
                    <p class="opacity-50 mb-0" style="font-size: 12px;">ACTIVE PROVINCIAL DISTRIBUTOR</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4 col-xl-2">
            <div class="card danger-card overflow-hidden text-bg-primary w-100">
                <div class="card-body p-4">
                    <div class="mb-7">
                        <i class="ti ti-user-x fs-8 fw-lighter"></i> <!-- Inactive icon -->
                    </div>
                    <h5 class="text-white fw-bold fs-14 text-nowrap">
                        {{ $inactiveAds }}
                    </h5>
                    <p class="opacity-50 mb-0" style="font-size: 12px;">INACTIVE PROVINCIAL DISTRIBUTOR</p>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-xl-12 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <h5>Provincial Distributors</h5>
                    {{-- <button class="btn-sm btn-success btn" data-bs-toggle="modal"  data-bs-target="#new_area_distributor">+ Add</button> --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped transaction-table" id="example" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>Partner Code</th>
                                    <th>Name</th>
                                    <th>Contact Number</th>
                                    <th>Address</th>
                                    <th>Business Name</th>
                                    <th>Business Type</th>
                                    <th>Region</th>
                                    <th>Area</th>
                                    {{-- <th>Qty Sold</th> --}}
                                    {{-- <th>Points Earned</th> --}}
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="dealerBody">
                                @foreach($ads as $ad)
                                <tr>
                                    <td scope="col">
                                        <div class="d-flex gap-1">
                                            <!-- VIEW BUTTON -->
                                            <a href="{{ url('view-ad/'.$ad->id) }}" 
                                            class="btn btn-sm btn-info"
                                            title="View">
                                                <i class="ti ti-eye"></i>
                                            </a>

                                            <!-- EDIT BUTTON (MODAL TRIGGER) -->
                                            <button type="button"
                                                    class="btn btn-sm btn-warning"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#edit_area_distributor-{{ $ad->id }}"
                                                    title="Edit">
                                                <i class="ti ti-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td scope="col">{{ $ad->store_code }}</td>
                                    <td scope="col"><a href='view-ad/{{$ad->id}}'>{{$ad->name}}</a></td>
                                    <td scope="col">{{$ad->contact_number ?? '-'}}</td>
                                    <td scope="col">{{$ad->address ?? '-'}}</td>
                                    <td scope="col">{{$ad->business_name ?? '-'}}</td>
                                    <td scope="col">{{$ad->business_type ?? '-'}}</td>
                                    <td scope="col">{{$ad->location_region ?? '-'}}</td>
                                    {{-- <td scope="col">{{($ad->sales)->sum('qty')}}</td> --}}
                                    <td>
                                        @foreach($ad->areas as $area)
                                            <span class="badge bg-primary me-1 mb-1">
                                                {{ $area->area_name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($ad->status == 'Active')
                                            <span class="badge badge-success">Active</span>
                                        @else 
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                @include('area_distributor.edit')
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@include('area_distributor.create')
@section('javascript')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>



{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script> --}}
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function () {

    $('#example').DataTable();

    // INIT SELECT2 FUNCTION
    function initSelect2(modal) {
        modal.find('.area_name').select2({
            placeholder: "Select Area(s)",
            width: '100%',
            allowClear: true,
            dropdownParent: modal // ✅ FIX: bind to current modal only
        });
    }

    // INITIALIZE ALL VISIBLE MODALS (optional)
    $('.modal-select2').each(function () {
        initSelect2($(this));
    });

    // RE-INIT WHEN MODAL OPENS (BEST PRACTICE)
    $('.modal-select2').on('shown.bs.modal', function () {
        let modal = $(this);

        // destroy old instance first (IMPORTANT)
        modal.find('.area_name').select2('destroy');

        // re-init properly
        initSelect2(modal);
    });

});
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById("dealerFilter").addEventListener("change", function () {
            const selectedDealer = this.value;
            filterDealersByName(selectedDealer);
        });

        function filterDealersByName(dealerName) {
            const rows = document.querySelectorAll('#dealerBody tr');
            rows.forEach(row => {
                const dealerColumn = row.cells[0].textContent;
                if (dealerName === 'All' || dealerColumn === dealerName) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    });
</script>
@endsection
