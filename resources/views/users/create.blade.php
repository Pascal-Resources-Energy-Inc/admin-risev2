<div id="new_users" class="modal fade admin-user-modal" tabindex="-1" aria-labelledby="newAdminUserTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ url('new-user') }}" id="newUserForm">
                @csrf
                <input type="hidden" name="role" value="Admin">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title text-white" id="newAdminUserTitle">New Admin User</h5>
                        <p>Create an administrator account and assign its department and warehouse.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
                    @endif

                    <section class="admin-user-section">
                        <h6><i class="bi bi-person-vcard"></i> Personal information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="adminFirstName" class="form-label">First name <span>*</span></label>
                                <input id="adminFirstName" class="form-control" name="first_name" value="{{ old('first_name') }}" maxlength="255" autocomplete="given-name" placeholder="Enter First Name" required data-uppercase>
                            </div>
                            <div class="col-md-4">
                                <label for="adminMiddleName" class="form-label">Middle name</label>
                                <input id="adminMiddleName" class="form-control" name="middle_name" value="{{ old('middle_name') }}" maxlength="255" autocomplete="additional-name" placeholder="Enter Middle Name" data-uppercase>
                            </div>
                            <div class="col-md-4">
                                <label for="adminLastName" class="form-label">Last name <span>*</span></label>
                                <input id="adminLastName" class="form-control" name="last_name" value="{{ old('last_name') }}" maxlength="255" autocomplete="family-name" placeholder="Enter Last Name" required data-uppercase>
                            </div>
                            <div class="col-md-8">
                                <label for="adminEmail" class="form-label">Email address <span>*</span></label>
                                <input id="adminEmail" class="form-control" type="email" name="email_address" value="{{ old('email_address') }}" maxlength="255" autocomplete="email" placeholder="Enter Email Address" required>
                            </div>
                        </div>
                    </section>

                    <section class="admin-user-section">
                        <h6><i class="bi bi-briefcase"></i> Work information</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="adminEmployeeNumber" class="form-label">Employee number <span>*</span></label>
                                <input id="adminEmployeeNumber" class="form-control" name="employee_number" value="{{ old('employee_number') }}" maxlength="255" placeholder="Enter Employee Number" required data-uppercase>
                            </div>
                            <div class="col-md-4">
                                <label for="adminDepartment" class="form-label">Department <span>*</span></label>
                                <input id="adminDepartment" class="form-control" name="department" value="{{ old('department') }}" maxlength="255" placeholder="Enter Department" required data-uppercase>
                            </div>
                            <div class="col-md-4">
                                <label for="adminDesignation" class="form-label">Designation <span>*</span></label>
                                <input id="adminDesignation" class="form-control" name="designation" value="{{ old('designation') }}" maxlength="255" placeholder="Enter Designation" required data-uppercase>
                            </div>
                            <div class="col-md-4">
                                <label for="adminStatus" class="form-label">Status <span>*</span></label>
                                <select id="adminStatus" class="form-select" name="status" required>
                                    <option value="Active" {{ old('status', 'Active') === 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ old('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    {{-- <section class="admin-user-section warehouse-section">
                        <div>
                            <h6><i class="bi bi-building"></i> Warehouse assignment</h6>
                            <p>Choose a warehouse for this administrator, or leave unassigned.</p>
                        </div>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="admin-warehouse-option" for="warehouse_lubao">
                                    <input type="radio" name="warehouse" id="warehouse_lubao" value="lubao" {{ old('warehouse') === 'lubao' ? 'checked' : '' }}>
                                    <span class="warehouse-option-icon"><i class="bi bi-building"></i></span>
                                    <span><strong>Lubao</strong><small>Pampanga warehouse</small></span>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="admin-warehouse-option" for="warehouse_guinobatan">
                                    <input type="radio" name="warehouse" id="warehouse_guinobatan" value="guinobatan" {{ old('warehouse') === 'guinobatan' ? 'checked' : '' }}>
                                    <span class="warehouse-option-icon"><i class="bi bi-building"></i></span>
                                    <span><strong>Guinobatan</strong><small>Albay warehouse</small></span>
                                </label>
                            </div>
                        </div>
                    </section> --}}

                    <p class="admin-user-password-note"><i class="bi bi-info-circle"></i> The initial password is <strong>12345678</strong>.</p>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> Create admin user</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .admin-user-modal .modal-content { overflow: hidden; border: 0; border-radius: 16px; box-shadow: 0 24px 70px rgba(16, 42, 67, .24); }
    .admin-user-modal .modal-header { padding: 20px 24px; color: #fff; background: linear-gradient(120deg, #123c61, #1592bd); border: 0; }
    .admin-user-modal .modal-title { font-size: 18px; font-weight: 900; }
    .admin-user-modal .modal-header p { margin: 4px 0 0; color: rgba(255,255,255,.78); font-size: 12px; }
    .admin-user-modal .btn-close { filter: brightness(0) invert(1); }
    .admin-user-modal .modal-body { padding: 22px 24px; }
    .admin-user-section { margin-bottom: 20px; padding-bottom: 18px; border-bottom: 1px solid #edf1f5; }
    .admin-user-section h6 { margin: 0 0 14px; color: #193b59; font-size: 13px; font-weight: 900; }
    .admin-user-section h6 i { margin-right: 6px; color: #1688b3; }
    .admin-user-section .form-label { margin-bottom: 6px; color: #445b70; font-size: 11px; font-weight: 800; }
    .admin-user-section .form-label span { color: #dc3545; }
    .admin-user-section .form-control { min-height: 41px; border-color: #d9e3eb; border-radius: 9px; font-size: 12px; }
    .admin-user-section .form-control:focus { border-color: #1592bd; box-shadow: 0 0 0 3px rgba(21,146,189,.12); }
    .admin-user-section small { color: #8896a4; font-size: 10px; }
    .warehouse-section > div:first-child p { margin: -8px 0 13px; color: #82909e; font-size: 11px; }
    .admin-warehouse-option { position: relative; display: flex; align-items: center; gap: 11px; min-height: 68px; padding: 12px; border: 1px solid #dbe5ed; border-radius: 10px; cursor: pointer; transition: .18s ease; }
    .admin-warehouse-option:hover { border-color: #1592bd; background: #f5fbfd; }
    .admin-warehouse-option:has(input:checked) { border-color: #1592bd; background: #effaff; box-shadow: 0 0 0 3px rgba(21,146,189,.1); }
    .admin-warehouse-option input { accent-color: #1592bd; }
    .warehouse-option-icon { display: grid; flex: 0 0 auto; width: 38px; height: 38px; place-items: center; color: #1688b3; background: #e4f7fd; border-radius: 9px; }
    .admin-warehouse-option strong,.admin-warehouse-option small { display: block; }
    .admin-warehouse-option strong { color: #243d55; font-size: 12px; }
    .admin-user-password-note { margin: 0; padding: 10px 12px; color: #5b7185; background: #f5f8fb; border-radius: 9px; font-size: 11px; }
    .admin-user-password-note i { margin-right: 5px; color: #1688b3; }
    .admin-user-modal .modal-footer { padding: 14px 24px; border-top-color: #edf1f5; }
    .admin-user-modal .modal-footer .btn { min-height: 39px; padding-inline: 15px; border-radius: 9px; font-size: 12px; font-weight: 800; }
    .admin-user-modal .modal-footer .btn-primary { background: #1592bd; border-color: #1592bd; }
    @media (max-width: 575px) { .admin-user-modal .modal-header,.admin-user-modal .modal-body { padding: 17px; } .admin-user-modal .modal-footer { padding: 12px 17px; } }
</style>

