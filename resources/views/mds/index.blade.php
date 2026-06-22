@extends('layouts.header')
<link rel="icon" type="image/png" href="{{ asset('images/logo_nya.png') }}">

@section('css')
<style>
    .welcome {
        margin-top: 20px;
    }
    .dataTables_length select {
        width: 55px !important;
    }
    .table thead th {
        vertical-align: middle !important;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
@endsection

@section('content')
<section class="welcome">
    <div class="row">
        <div class="col-sm-6 col-lg-4 col-xl-2">
            <div class="card warning-card overflow-hidden text-bg-primary w-100">
                <div class="card-body p-4">
                    <div class="mb-7">
                        <i class="ti ti-user-check fs-8 fw-lighter"></i>
                    </div>
                    <h5 class="text-white fw-bold fs-14 text-nowrap">{{ $activeAds }}</h5>
                    <p class="opacity-50 mb-0" style="font-size: 12px;">ACTIVE MEGA DEALERS</p>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-4 col-xl-2">
            <div class="card danger-card overflow-hidden text-bg-primary w-100">
                <div class="card-body p-4">
                    <div class="mb-7">
                        <i class="ti ti-user-x fs-8 fw-lighter"></i>
                    </div>
                    <h5 class="text-white fw-bold fs-14 text-nowrap">{{ $inactiveAds }}</h5>
                    <p class="opacity-50 mb-0" style="font-size: 12px;">INACTIVE MEGA DEALERS</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-xl-12 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">Mega Dealers</h5>
                        <button class="btn-sm btn-success btn" data-bs-toggle="modal" data-bs-target="#new_area_distributor">
                            + Add
                        </button>
                    </div>

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
                                    <th>Awarded Area</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="dealerBody">
                                @foreach($ads as $ad)
                                    <tr>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ url('view-ad/'.$ad->id) }}" class="btn btn-sm btn-info" title="View">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                <button type="button"
                                                        class="btn btn-sm btn-warning"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#edit_area_distributor-{{ $ad->id }}"
                                                        title="Edit">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td>{{ strtoupper($ad->store_code ?? '-') }}</td>
                                        <td><a href="{{ url('view-ad/'.$ad->id) }}">{{ strtoupper($ad->name) }}</a></td>
                                        <td>{{ strtoupper($ad->contact_number ?? '-') }}</td>
                                        <td>{{ strtoupper($ad->address ?? '-') }}</td>
                                        <td>{{ strtoupper($ad->business_name ?? '-') }}</td>
                                        <td>{{ strtoupper($ad->business_type ?? '-') }}</td>
                                        <td>{{ strtoupper($ad->location_region ?? '-') }}</td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1 mb-2">
                                                @forelse($ad->areas as $area)
                                                    <span class="badge bg-primary">
                                                        {{ $area->project_type }}: {{ $area->area_name }}
                                                    </span>
                                                @empty
                                                    <span class="text-muted">No area</span>
                                                @endforelse
                                            </div>

                                            <button type="button"
                                                    class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#manageAreaModal-{{ $ad->id }}">
                                                <i class="ti ti-map-pin"></i> Manage Areas
                                            </button>
                                        </td>
                                        <td>
                                            @if($ad->status == 'Active')
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @foreach($ads as $ad)
                            @include('area_distributor.manage_areas')
                            @include('area_distributor.edit')
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@include('area_distributor.create')

@section('javascript')
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function () {
        $('#example').DataTable();
    });
</script>
@endsection
