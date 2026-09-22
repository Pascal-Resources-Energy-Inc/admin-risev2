@extends('layouts.header')
<link rel="icon" type="image/png" href="{{asset('images/logo_nya.png')}}">
@section('css')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">

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

.customer-pagination .page-link {
  border-radius: .35rem;
  margin: 0 .15rem;
  border: 0;
  color: #495057;
}

.customer-pagination .page-item.active .page-link {
  background: #0d6efd;
}

.empty-state {
  padding: 3.5rem 1rem;
  text-align: center;
  color: #6c757d;
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

                <form method="GET" action="{{ route('customers') }}" class="customer-toolbar mb-4">
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
                      @if(request('search') !== null || request('status') !== null || request('per_page') !== null)
                        <a class="btn btn-light" href="{{ route('customers') }}">Reset</a>
                      @endif
                    </div>
                  </div>
                </form>
              <div class="table-responsive">
                <table class="table customer-table mb-0" style="width:100%">
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
                    <tbody id="customerBody">
                        @foreach($customers as $customer)
                      <tr>
                        <td><span class="font-weight-bold">{{ $customer->client_reference }}</span></td>
                        <td>
                          <a class="customer-name" href="{{ route('client.view', $customer->id) }}">{{ strtoupper($customer->name) }}</a>
                          <div class="customer-meta d-md-none">{{ $customer->number ?: 'No contact number' }}</div>
                        </td>
                        <td>{{ $customer->number ?: '—' }}</td>
                        <td>{{ $customer->email_address ? strtoupper($customer->email_address) : '—' }}</td>
                        <td>
                          @if($customer->serial)
                            {{ $customer->serial->serial_number }}
                          @else
                            -
                          @endif
                        </td>
                        <td>
                          {{ strtoupper(
                              implode(', ', array_filter([
                                  $customer->street_address,
                                  $customer->location_barangay,
                                  $customer->location_city,
                                  $customer->location_province
                              ])) . ' ' . $customer->postal_code
                          ) }}
                        </td>
                        <td>{{ $customer->transactions->sum('points_client') }}</td>
                        <td>{{ $customer->center ? strtoupper($customer->center) : '—' }}</td>
                        <td>{{ $customer->spo ? strtoupper($customer->spo) : '—' }}</td>
                        <td>
                          @if($customer->status == 'Active')
                            <span class="badge badge-success px-2 py-1">Active</span>
                          @else
                            <span class="badge badge-danger px-2 py-1">Inactive</span>
                          @endif
                        </td>
                      </tr>
                      @endforeach

                    </tbody>
                </table>
              </div>
              @if($customers->isEmpty())
                <div class="empty-state">
                  <i class="ti ti-users fs-8 d-block mb-2"></i>
                  <h6 class="mb-1">No customers found</h6>
                  <p class="mb-0">Try adjusting your search or filters.</p>
                </div>
              @else
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between border-top pt-3 mt-3">
                  <p class="text-muted small mb-3 mb-md-0">
                    Showing {{ $customers->firstItem() }}–{{ $customers->lastItem() }} of {{ $customers->total() }} customers
                  </p>
                  <nav aria-label="Customer pages">
                    <ul class="pagination customer-pagination mb-0">
                      <li class="page-item {{ $customers->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $customers->previousPageUrl() ?: '#' }}" aria-label="Previous">&laquo;</a>
                      </li>
                      @for($page = max(1, $customers->currentPage() - 2); $page <= min($customers->lastPage(), $customers->currentPage() + 2); $page++)
                        <li class="page-item {{ $page === $customers->currentPage() ? 'active' : '' }}">
                          <a class="page-link" href="{{ $customers->url($page) }}">{{ $page }}</a>
                        </li>
                      @endfor
                      <li class="page-item {{ $customers->hasMorePages() ? '' : 'disabled' }}">
                        <a class="page-link" href="{{ $customers->nextPageUrl() ?: '#' }}" aria-label="Next">&raquo;</a>
                      </li>
                    </ul>
                  </nav>
                </div>
              @endif
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
});
</script>

@endsection
