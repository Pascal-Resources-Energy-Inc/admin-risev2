{{-- <div id="edit-users-{{ $user->id }}" class="modal fade" tabindex="-1" aria-labelledby="bs-example-modal-md" aria-hidden="true" style="display:none;">
  <div class="modal-dialog modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header d-flex align-items-center">
        <h4 class="modal-title" id="myModalLabel">
          Edit Users
        </h4>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <form action="{{ route('edit-users', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                @if($user->role !== 'Dealer' && $user->role !== 'Admin')
                <div class="col-12 mb-3">
                  <label for="serialNumber" class="form-label">
                      Serial Number
                  </label>
                  <div class="dropdown-container">
                      @php
                          $currentSerial = '';
                          $currentStoveId = null;
                          if($user->role == 'Client' && $user->client && $user->client->serial) {
                              $currentSerial = $user->client->serial->serial_number;
                              $currentStoveId = $user->client->serial_number;
                          } else {
                              $currentSerial = $user->serial ?? '';
                          }
                      @endphp
                      
                      <select id="serial_number_{{ $user->id }}"
                              name="serial_number" 
                              data-user-id="{{ $user->id }}"
                              data-current-stove-id="{{ $currentStoveId }}"
                              class="form-control js-serial-number-select" 
                              style="width: 100%;">
                          
                          @if($currentSerial && $currentStoveId)
                              <option value="{{ $currentStoveId }}" selected>{{ $currentSerial }}</option>
                          @endif
                          
                          <option value="">--  Available Serial Number  --</option>
                          <option value=""></option>
                          
                          @foreach($stoves as $stove)
                              @if($currentStoveId != $stove->id)
                                  <option value="{{ $stove->id }}">{{ $stove->serial_number }}</option>
                              @endif
                          @endforeach
                      </select>
                  </div>
                  <small class="text-muted">Current Serial Number</small>
                </div>
                @endif

                <div class="col-12 mb-3">
                    <label for="fullName" class="form-label">
                        Full Name <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="fullName" name="full_name" value="{{ $user->name }}" required class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="emailAddress" class="form-label">
                        Email Address <span class="text-danger">*</span>
                    </label>
                    <input type="email" id="emailAddress" name="email" value="{{ $user->email }}" required class="form-control" required>
                </div>
                
                <div class="form-group col-md-6 mb-3">
                    <label for="role">Role</label>
                    <input type="text" id="role" name="role" value="{{ $user->role }}" required class="form-control" disabled>
                </div>

                
               @if($user->role !== 'Admin')
                <div class="col-md-6 mb-3">
                    <label for="phoneNumber" class="form-label">
                        Phone Number <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="tel" 
                        id="phoneNumber" 
                        name="phone" 
                        maxlength="11" 
                        inputmode="numeric"
                        pattern="\d{11}" 
                        value="{{ 
                            $user->role == 'Dealer' && $user->dealer ? $user->dealer->number : 
                            ($user->role == 'Client' && $user->client ? $user->client->number : 
                            ($user->number ?? '')) 
                        }}" 
                        required 
                        class="form-control"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);"
                    >
                </div>
                @else
                <input type="hidden" name="phone" value="{{ $user->phone ?? $user->number ?? 'N/A' }}">
                @endif

                <div class="col-12 mb-3">
                    <label for="address" class="form-label">
                        Address <span class="text-danger">*</span>
                    </label>
                    <textarea id="address" name="address" required rows="3" class="form-control" required>{{ 
                        $user->role == 'Dealer' && $user->dealer ? $user->dealer->address : 
                        ($user->role == 'Client' && $user->client ? $user->client->address : 
                        ($user->address ?? '')) 
                    }}</textarea>
                </div>
            </div>
            
            <div class="modal-footer">
              <button type="button" class="btn bg-danger-subtle text-danger  waves-effect"
                data-bs-dismiss="modal">
                Close
              </button>
              <button type="submit" class="btn bg-info-subtle text-info  waves-effect">
                Submit
              </button>
            </div>
        </form>
      </div>
    </div>
  </div>
</div> --}}

<div class="modal fade admin-user-modal" id="editUserModal" tabindex="-1" aria-labelledby="editUserTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title text-white" id="editUserTitle">Edit User</h5>
          <p id="editUserSubtitle">Update account information.</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="edit_user_id">
        <input type="hidden" id="edit_role">

        <div id="editAdminFields" hidden>
          <section class="admin-user-section">
            <h6><i class="bi bi-person-vcard"></i> Personal information</h6>
            <div class="row g-3">
              <div class="col-md-4">
                <label for="edit_first_name" class="form-label">First name <span>*</span></label>
                <input type="text" id="edit_first_name" class="form-control" maxlength="255" autocomplete="given-name" data-uppercase required>
              </div>
              <div class="col-md-4">
                <label for="edit_middle_name" class="form-label">Middle name</label>
                <input type="text" id="edit_middle_name" class="form-control" maxlength="255" autocomplete="additional-name" data-uppercase>
              </div>
              <div class="col-md-4">
                <label for="edit_last_name" class="form-label">Last name <span>*</span></label>
                <input type="text" id="edit_last_name" class="form-control" maxlength="255" autocomplete="family-name" data-uppercase required>
              </div>
              <div class="col-md-8">
                <label for="edit_email" class="form-label">Email address <span>*</span></label>
                <input type="email" id="edit_email" class="form-control" maxlength="255" autocomplete="email" required>
              </div>
            </div>
          </section>

          <section class="admin-user-section">
            <h6><i class="bi bi-briefcase"></i> Work information</h6>
            <div class="row g-3">
              <div class="col-md-4">
                <label for="edit_employee_number" class="form-label">Employee number <span>*</span></label>
                <input type="text" id="edit_employee_number" class="form-control" maxlength="255" data-uppercase required>
              </div>
              <div class="col-md-4">
                <label for="edit_department" class="form-label">Department <span>*</span></label>
                <input type="text" id="edit_department" class="form-control" maxlength="255" data-uppercase required>
              </div>
              <div class="col-md-4">
                <label for="edit_designation" class="form-label">Designation <span>*</span></label>
                <input type="text" id="edit_designation" class="form-control" maxlength="255" data-uppercase required>
              </div>
              <div class="col-md-4">
                <label for="edit_status" class="form-label">Status <span>*</span></label>
                <select id="edit_status" class="form-select" required>
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
            </div>
          </section>
        </div>

        <div id="editBasicFields" class="row g-3" hidden>
          <div class="col-12">
            <label for="edit_name" class="form-label">Full name <span class="text-danger">*</span></label>
            <input type="text" id="edit_name" class="form-control" maxlength="255" required>
          </div>
          <div class="col-12">
            <label for="edit_email_basic" class="form-label">Email address <span class="text-danger">*</span></label>
            <input type="email" id="edit_email_basic" class="form-control" maxlength="255" required>
          </div>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveUser"><i class="bi bi-check2 me-1"></i> Save changes</button>
      </div>
    </div>
  </div>
</div>

<style>
  #editUserModal .modal-content { overflow: hidden; border: 0; border-radius: 16px; box-shadow: 0 24px 70px rgba(16,42,67,.24); }
  #editUserModal .modal-header { padding: 20px 24px; color: #fff; background: linear-gradient(120deg,#123c61,#1592bd); border: 0; }
  #editUserModal .modal-title { font-size: 18px; font-weight: 900; }
  #editUserModal .modal-header p { margin: 4px 0 0; color: rgba(255,255,255,.78); font-size: 12px; }
  #editUserModal .btn-close { filter: brightness(0) invert(1); }
  #editUserModal .modal-body { padding: 22px 24px; }
  #editUserModal .admin-user-section { margin-bottom: 20px; padding-bottom: 18px; border-bottom: 1px solid #edf1f5; }
  #editUserModal .admin-user-section h6 { margin: 0 0 14px; color: #193b59; font-size: 13px; font-weight: 900; }
  #editUserModal .admin-user-section h6 i { margin-right: 6px; color: #1688b3; }
  #editUserModal .form-label { margin-bottom: 6px; color: #445b70; font-size: 11px; font-weight: 800; }
  #editUserModal .admin-user-section .form-label span { color: #dc3545; }
  #editUserModal .form-control, #editUserModal .form-select { min-height: 41px; border-color: #d9e3eb; border-radius: 9px; font-size: 12px; }
  #editUserModal .form-control:focus, #editUserModal .form-select:focus { border-color: #1592bd; box-shadow: 0 0 0 3px rgba(21,146,189,.12); }
  #editUserModal .modal-footer { padding: 14px 24px; border-top-color: #edf1f5; }
  #editUserModal .modal-footer .btn { min-height: 39px; padding-inline: 15px; border-radius: 9px; font-size: 12px; font-weight: 800; }
  #editUserModal .modal-footer .btn-primary { background: #1592bd; border-color: #1592bd; }
  @media (max-width: 575px) { #editUserModal .modal-header, #editUserModal .modal-body { padding: 17px; } #editUserModal .modal-footer { padding: 12px 17px; } }
</style>

<style>
.modal-body {
  padding: 1rem 1.5rem 0.5rem 1.5rem;
}

.modal-header {
  padding: 1rem 1.5rem 0.5rem 1.5rem;
}

/* .modal-footer {
  padding: 0.5rem 1.5rem 1rem 1.5rem;
} */

.mb-3 {
  margin-bottom: 0.75rem !important;
}

#address {
  min-height: 80px;
  rows: 3;
}

.dropdown-container {
  position: relative;
}

.dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  z-index: 1000;
  max-height: 200px;
  overflow-y: auto;
  border: 1px solid #ced4da;
  border-radius: 0.375rem;
  background-color: white;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.dropdown-item {
  padding: 0.5rem 1rem;
  color: #212529;
  text-decoration: none;
  display: block;
  cursor: pointer;
}

.dropdown-item:hover {
  background-color: #f8f9fa;
  color: #212529;
}

.card {
  border: 1px solid #dee2e6;
  border-radius: 0.375rem;
}

.card-body {
  background-color: #f8f9fa;
}

.custom-select {
    position: relative;
    display: inline-block;
    width: 100%;
}

.custom-select select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    width: 100%;
    padding: 10px 40px 10px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    background: white;
    font-size: 16px;
    cursor: pointer;
}

/* custom arrow */
.custom-select::after {
    content: "▼";
    position: absolute;
    top: 50%;
    right: 12px;
    transform: translateY(-50%);
    pointer-events: none;
    font-size: 14px;
    color: #555;
}

</style>
