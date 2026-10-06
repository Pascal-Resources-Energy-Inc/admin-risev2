@extends('layouts.header')
<link rel="icon" type="image/png" href="{{asset('images/logo_nya.png')}}">
@section('css')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">

<style>
.chosen-container .chosen-single {
  height: calc(2.25rem + 2px);
  padding: 0.375rem 0.75rem;
  font-size: 1rem;
  line-height: 1.5;
  color: #495057;
  background-color: #fff;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  box-shadow: none;
}

.chosen-container-active.chosen-with-drop .chosen-single {
  border-color: #80bdff;
  box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.chosen-container .chosen-drop {
  border: 1px solid #ced4da;
  border-top: none;
  border-radius: 0 0 0.25rem 0.25rem;
  box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
}

.chosen-container .chosen-results {
  max-height: 200px;
  overflow-y: auto;
}

.chosen-container .chosen-search input {
  height: calc(1.5em + 0.75rem + 2px);
  padding: 0.375rem 0.75rem;
  font-size: 1rem;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
}

.customer-toolbar {
  background: #f8fafc;
  border: 1px solid #e9ecef;
  border-radius: .5rem;
  padding: 1rem;
}

.customer-search {
  min-width: 260px;
}

.customer-table th {
  border-top: 0;
  color: #6c757d;
  font-size: .75rem;
  font-weight: 700;
  letter-spacing: .04em;
  text-transform: uppercase;
  white-space: nowrap;
}

.customer-table td {
  vertical-align: middle;
}

.customer-name {
  color: #1f2937;
  font-weight: 600;
  text-decoration: none;
}

.customer-name:hover {
  color: #0d6efd;
}

.customer-meta {
  color: #6c757d;
  font-size: .82rem;
}

.customer-table-shell {
  min-height: 190px;
}

.dataTables_wrapper .dataTables_processing {
  background: #fff;
  border: 1px solid #dbeafe;
  border-radius: .6rem;
  box-shadow: 0 .75rem 1.5rem rgba(15, 23, 42, .12);
  color: #1d4ed8;
  font-size: .85rem;
  font-weight: 600;
  margin-left: 0;
  padding: .7rem 1rem;
  transform: translateX(-50%);
  width: auto;
}

</style>

@endsection
@section('content')
<section class="welcome">
  <div class="row">
    <div class="col-sm-6 col-lg-4 col-xl-2">
      <div class="card warning-card overflow-hidden text-bg-primary w-100">
        <div class="card-body p-4">
          <div class="mb-7">
            <i class="ti ti-user-check fs-8 fw-lighter"></i> <!-- Active icon -->
          </div>
          <h5 class="text-white fw-bold fs-14 text-nowrap">
            {{ $activeCustomers }}
          </h5>
          <p class="opacity-50 mb-0" style="font-size: 12px;">ACTIVE CUSTOMERS</p>
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
            {{ $inactiveCustomers }}
          </h5>
          <p class="opacity-50 mb-0" style="font-size: 12px;">INACTIVE CUSTOMERS</p>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-12 col-xl-12 d-flex align-items-stretch">
        <div class="card w-100">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                  <div>
                    <h5 class="mb-1">Customers</h5>
                    <p class="text-muted mb-0 small">Manage your customer directory and account status.</p>
                  </div>
                  <button class="btn btn-success mt-3 mt-sm-0" data-bs-toggle="modal" data-bs-target="#new_customer">
                    <i class="ti ti-plus me-1"></i> Add customer
                  </button>
                </div>

                <form id="customerFilterForm" class="customer-toolbar mb-4">
                  <div class="row align-items-end">
                    <div class="col-md-6 col-lg-5 mb-3 mb-md-0">
                      <label for="customer-search" class="form-label small font-weight-bold mb-1">Search customers</label>
                      <input id="customer-search" class="form-control customer-search" type="search" name="search" value="{{ request('search') }}" placeholder="Name, reference, contact, email, center...">
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                      <label for="customer-status" class="form-label small font-weight-bold mb-1">Status</label>
                      <select id="customer-status" class="form-control" name="status">
                        <option value="">All statuses</option>
                        <option value="Active" {{ request('status') === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ request('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                      </select>
                    </div>
                    <div class="col-6 col-md-3 col-lg-2">
                      <label for="per-page" class="form-label small font-weight-bold mb-1">Per page</label>
                      <select id="per-page" class="form-control" name="per_page">
                        @foreach([10, 15, 25, 50] as $size)
                          <option value="{{ $size }}" {{ (int) request('per_page', 15) === $size ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                      </select>
                    </div>
                    <div class="col-lg-3 mt-3 mt-lg-0 d-flex">
                      <button class="btn btn-primary mr-2" type="submit"><i class="ti ti-search me-1"></i> Search</button>
                      <a class="btn btn-light" href="{{ route('customers') }}">Reset</a>
                    </div>
                  </div>
                </form>
              <div class="table-responsive customer-table-shell" id="customerTableShell" aria-busy="false">
                <table id="customerTable" class="table customer-table mb-0" style="width:100%">
                    <thead>
                      <tr>
                          <th>Customer Reference</th>
                          <th>Customer Name</th>
                          <th>Contact Number</th>
                          <th>Email Address</th>
                          <th>Serial Number</th>
                          <th>Address</th>
                          <th>Total Points</th>
                          <th>Center</th>
                          <th>SPO</th>
                          <th>Status</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                </table>
              </div>
            </div>
        </div>
    </div>
  </div>
    
</section>
@endsection
@include('new_customer')
@section('javascript')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
  $('#new_customer').on('shown.bs.modal', function () {
    if (typeof map === 'undefined' || !map) {
      initMap();
    } else {
      setTimeout(function() {
        map.invalidateSize();
      }, 200);
    }
  });
  $('.chosen-select').chosen({
      width: '100%'
    });

  var customerTable = $('#customerTable').DataTable({
    processing: true,
    serverSide: true,
    pageLength: 15,
    lengthMenu: [10, 15, 25, 50],
    ajax: {
      url: "{{ route('customers.data') }}",
      data: function (data) {
        data.status = $('#customer-status').val();
      }
    },
    columns: [
      { data: 'reference', name: 'clients.client_reference' },
      { data: 'customer_name', name: 'clients.name' },
      { data: 'number', name: 'clients.number' },
      { data: 'email_address', name: 'clients.email_address' },
      { data: 'serial_number', orderable: false, searchable: false },
      { data: 'address', orderable: false, searchable: false },
      { data: 'points', orderable: false, searchable: false },
      { data: 'center', name: 'clients.center' },
      { data: 'spo', name: 'clients.spo' },
      { data: 'status_badge', name: 'clients.status' }
    ],
    language: {
      processing: 'Loading customers…',
      emptyTable: 'No customers found.',
      zeroRecords: 'No customers match your search.'
    }
  });

  $('#customerFilterForm').on('submit', function (event) {
    event.preventDefault();
    customerTable.search($('#customer-search').val()).draw();
  });
  $('#customer-status').on('change', function () { customerTable.ajax.reload(); });
  $('#per-page').on('change', function () { customerTable.page.len(Number(this.value)).draw(); });
  $('.customer-toolbar .btn-light').on('click', function (event) {
    event.preventDefault();
    $('#customer-search').val('');
    $('#customer-status').val('');
    $('#per-page').val('15');
    customerTable.search('').page.len(15).draw();
  });
});
</script>

@endsection
