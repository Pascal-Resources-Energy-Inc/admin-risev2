@extends('layouts.header')
<link rel="icon" type="image/png" href="{{asset('images/logo_nya.png')}}">
@section('css')
<style>
    table td:nth-child(6) {
        white-space: nowrap;
        text-align: center;
        vertical-align: middle;
    }

    .transaction-table th {
        text-align: center;
    }
    .btn-view {
        width: 100px;
        font-size: 14px;
    }

    .welcome {
        margin-top: 20px;
    }

    .card-header {
        font-size: 1.25rem;
        font-weight: bold;
    }

    .card-body.users {
        padding: 20px;
        background-color: #ffffffff;
        border-radius: 50px;
    }

    .filter-container {
        margin-bottom: 20px;
    }

    .btn-custom {
        display: inline-block;
        padding: 8px 12px;
        font-size: 14px;
        font-weight: 500;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        border-radius: 20px;
        border: 2px solid;
        background-color: white;
        text-decoration: none;
        margin: 2px;
        min-width: 40px;
        min-height: 36px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-view-custom {
        border-color: #1e90ff;
        background-color: #1e90ff;
        color: #fff;
    }

    .btn-view-custom:hover {
        background-color: #0d7ddd;
        border-color: #0d7ddd;
        color: #fff !important;
    }

    .btn-edit-custom {
        border-color: #e53e3e;
        background-color: #e53e3e;
        color: #fff;
    }

    .btn-edit-custom:hover {
        background-color: #c53030;
        border-color: #c53030;
        color: #fff !important;
    }

    .btn-access-custom {
        border-color: #28a745;
        background-color: #28a745;
        color: #fff;
    }

    .btn-access-custom:hover {
        background-color: #218838;
        border-color: #1e7e34;
        color: #fff !important;
    }

    .btn-access-custom i {
        color: white;
    }

    .btn-access-custom:hover i {
        color: white;
    }

    .btn-group .btn {
        border-radius: 0;
    }
    .btn-group .btn:first-child {
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
    }
    .btn-group .btn:last-child {
        border-top-right-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }
    .btn-group .btn.active {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }

    .custom-dropdown {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        padding-right: 2rem;
    }

    .align-label-select {
        display: flex;
        align-items: center;
    }

    .align-label-select label {
        margin-bottom: 0;
        margin-right: 10px;
        line-height: 1.5;
    }

    .btn-custom i {
        font-size: 16px;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
        justify-content: center;
    }

    .btn-add-admin {
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        margin-left: 15px;
    }

    .btn-add-admin i {
        margin-right: 5px;
    }

    .users-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .users-header h5 {
        margin: 0;
    }

    .custom-dropdown {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-color: #fff;
        padding-right: 30px;
        background-image: none;
        position: relative;
    }

    .custom-select-container {
        position: relative;
        display: inline-block;
        width: 200px;
    }

    .custom-select-container::after {
        content: "▼";
        font-size: 12px;
        color: #333;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

@endsection
@section('content')
<section class="welcome">
    <div class="row">
        <div class="col-lg-12 col-xl-12 d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body users">
                    <div class="users-header">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div class="d-flex align-items-center">
                                <h5 class="mb-0">Users</h5>
                                @php
                                    $currentUser = auth()->user();
                                    $canShowAddAdmin = false;
                                    
                                    // Check if current user can add (has can_add permission)
                                    if ($currentUser && $currentUser->role === 'Admin' && $currentUser->can_add === 'on') {
                                        $canShowAddAdmin = true;
                                    }
                                @endphp
                                
                                @if($canShowAddAdmin)
                                {{-- <button type="button" class="btn btn-add-admin btn-success btn" data-bs-toggle="modal" data-bs-target="#add-admin-modal">
                                    <i class="fas fa-plus"></i>Add Users
                                    
                                </button> --}}
                                <button class="btn-success btn-add-admin btn" data-bs-toggle="modal" data-bs-target="#new_users"><i class="fas fa-plus"></i>&nbsp;Add Users</button>
                                @endif
                            </div>
                            
                            <div class="align-label-select custom-select-wrapper">
                                <label for="roleFilter" class="mb-0 mr-2">Role Filter:</label>
                                <div class="custom-select-container">
                                    <select id="roleFilter" class="form-control custom-dropdown">
                                        <option value="">All Roles</option>
                                        <option value="Admin">Admin</option>
                                        <option value="Dealer">Dealer</option>
                                        <option value="Client">Client</option>
                                        <option value="Area Distributor">Area Distributor</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                    <table id="example" class="table table-bordered table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th scope="col" width="20%">Name</th>
                                <th scope="col" width="20%">Email</th>
                                <th scope="col" width="25%">Address</th>
                                <th scope="col" width="15%">Status</th>
                                <th scope="col" width="10%">Role</th>
                                <th scope="col" width="10%">Actions</th>
                            </tr>
                        </thead>
                        {{-- <tbody id="userBody">
                            @foreach($users as $user)
                            <tr>
                                 <td scope="col">
                                    @if($user->role == 'Dealer' && $user->dealer)
                                        <span>{{$user->name}}</span>
                                    @elseif($user->role == 'Client' && $user->client)
                                        <span>{{$user->name}}</span>
                                    @else
                                        {{$user->name}}
                                    @endif
                                </td>
                                <td scope="col">{{$user->email}}</td>
                                <td scope="col">
                                    @if($user->role == 'Dealer' && $user->dealer)
                                        {{$user->dealer->address}}
                                    @elseif($user->role == 'Client' && $user->client)
                                        {{$user->client->address ?? 'N/A'}}
                                    @elseif($user->role == 'Admin')
                                        {{$user->address ?? ''}}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td scope="col">
                                    @php
                                        $status = '';
                                        
                                        if($user->role == 'Dealer' && $user->dealer) {
                                            $status = $user->dealer->status;
                                        } elseif($user->role == 'Client' && $user->client) {
                                            $status = $user->client->status ?? '';
                                        }
                                    @endphp
                                    <span>
                                        {{$status}}
                                    </span>
                                </td>
                                <td scope="col">
                                    @php
                                        $role = $user->role ?? 'N/A';
                                        $roleClass = 'badge-default';
                                        
                                        switch(strtolower($role)) {
                                            case 'dealer':
                                                $roleClass = 'badge-dealer';
                                                break;
                                            case 'client':
                                                $roleClass = 'badge-client';
                                                break;
                                            case 'admin':
                                                $roleClass = 'badge-admin';
                                                break;
                                            default:
                                                $roleClass = 'badge-default';
                                        }
                                    @endphp
                                    <span class="badge-custom {{$roleClass}}">
                                        {{$role}}
                                    </span>
                                </td>
                                <td scope="col">
                                    @php
                                        $status = 'N/A';
                                        
                                        if($user->role == 'Dealer' && $user->dealer) {
                                            $status = $user->dealer->status;
                                        } elseif($user->role == 'Client' && $user->client) {
                                            $status = $user->client->status ?? '';
                                        }
                                        
                                        // Check current user permissions
                                        $currentUser = auth()->user();
                                        $canEdit = $currentUser && $currentUser->role === 'Admin' && $currentUser->can_edit === 'on';
                                        $canAdd = $currentUser && $currentUser->role === 'Admin' && $currentUser->can_add === 'on';
                                    @endphp

                                    <div class="action-buttons">
                                        @if($status !== 'Inactive')
                                            @if($user->role == 'Dealer' && $user->dealer)
                                                <a href='view-dealer/{{$user->dealer->id}}' class="btn-custom btn-view-custom" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @elseif($user->role == 'Client' && $user->client)
                                                <a href='view-client/{{$user->client->id}}' class="btn-custom btn-view-custom" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                        @endif

                                        @if($user->role == 'Admin')
                                            @if($canEdit)
                                                <button class="btn-custom btn-edit-custom" data-bs-toggle="modal" data-bs-target="#edit-users-{{ $user->id }}" title="Edit Admin">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endif
                                            
                                            @if($canEdit || $canAdd)
                                                <button class="btn-custom btn-access-custom" data-bs-toggle="modal" data-bs-target="#access-admin-{{ $user->id }}" title="Access Control">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                            @endif
                                        @else
                                        
                                            @if($canEdit)
                                                <button class="btn-custom btn-edit-custom" data-bs-toggle="modal" data-bs-target="#edit-users-{{ $user->id }}" title="Edit User">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody> --}}
                        <tbody></tbody>
                    </table>
                  </div>
                </div>
            </div>
        </div>
    </div>
</section>

@include('new_admin')
@include('users.create')
@include('edit_users')
@include('admin-privillege')
@endsection


@section('javascript')
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>



<script>
    $(document).ready(function () {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        if ($.fn.DataTable.isDataTable('#example')) {
            $('#example').DataTable().destroy();
        }
        var table = $('#example').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('users.data') }}",
                data: function (d) {
                    d.role = $('#roleFilter').val();
                },
                error: function (xhr) {
                    console.log(xhr.responseText); // 🔥 shows real error
                }
            },
            columns: [
                { data: 'name', searchable: true },
                { data: 'email', searchable: true },
                { data: 'address', searchable: true },
                { data: 'status', searchable: true },
                { data: 'role', searchable: true },
                { data: 'actions', orderable: false, searchable: false }
            ]
        });

        // ✅ SINGLE FILTER ONLY
        $('#roleFilter').on('change', function () {
            table.ajax.reload();
        });

        $(document).on('click', '.btn-edit-user', function () {

            let id = $(this).data('id');

            $.get('/users/' + id + '/show', function (res) {

                $('#edit_user_id').val(res.id);
                $('#edit_name').val(res.name);
                $('#edit_email').val(res.email);

                $('#editUserModal').modal('show');
            });
        });

        $('#saveUser').click(function () {

            $.post('/users/update', {
                id: $('#edit_user_id').val(),
                name: $('#edit_name').val(),
                email: $('#edit_email').val()
            }, function (res) {

                if (res.success) {
                    $('#editUserModal').modal('hide');
                    $('#example').DataTable().ajax.reload(null, false);
                } else {
                    alert(res.message);
                }
            });
        });

        $(document).on('click', '.btn-access-user', function () {

            let id = $(this).data('id');

            $.get('/users/' + id + '/show', function (res) {

                $('#access_user_id').val(res.id);

                // ✅ FIX: CHECKBOX (not select anymore)
                $('#can_edit').prop('checked', res.can_edit === 'on');
                $('#can_add').prop('checked', res.can_add === 'on');
                $('#can_delete').prop('checked', res.can_delete === 'on');

                $('#can_edit_rewards').prop('checked', res.can_edit_rewards === 'on');
                $('#can_add_rewards').prop('checked', res.can_add_rewards === 'on');
                $('#can_delete_rewards').prop('checked', res.can_delete_rewards === 'on');

                $('#accessUserModal').modal('show');
            });
        });

        $('#saveAccess').click(function () {

            $.post('/users/access-update', {
                id: $('#access_user_id').val(),

                can_edit: $('#can_edit').is(':checked') ? 'on' : 'off',
                can_add: $('#can_add').is(':checked') ? 'on' : 'off',
                can_delete: $('#can_delete').is(':checked') ? 'on' : 'off',

                can_edit_rewards: $('#can_edit_rewards').is(':checked') ? 'on' : 'off',
                can_add_rewards: $('#can_add_rewards').is(':checked') ? 'on' : 'off',
                can_delete_rewards: $('#can_delete_rewards').is(':checked') ? 'on' : 'off',

            }, function (res) {

                if (res.success) {
                    $('#accessUserModal').modal('hide');
                    $('#example').DataTable().ajax.reload(null, false);
                } else {
                    alert(res.message);
                }
            });

        });

    });
    function toggleFieldRequired() {
        const contact = document.getElementById("contact_number");
        const facebook = document.getElementById("facebook");
        const contactMark = document.getElementById("contactRequiredMark");
        const facebookMark = document.getElementById("facebookRequiredMark");

        if (!contact || !facebook) return;

        const hasContact = contact.value.trim() !== "";
        const hasFacebook = facebook.value.trim() !== "";

        // Contact → Facebook not required
        if (hasContact) {
            facebook.removeAttribute("required");
            if (facebookMark) facebookMark.style.display = "none";
        } else {
            facebook.setAttribute("required", "required");
            if (facebookMark) facebookMark.style.display = "inline";
        }

        // Facebook → Contact not required
        if (hasFacebook) {
            contact.removeAttribute("required");
            if (contactMark) contactMark.style.display = "none";
        } else {
            contact.setAttribute("required", "required");
            if (contactMark) contactMark.style.display = "inline";
        }
    }
</script>

<script>
    $(function () {

        const roleFilter = $('#roleFilter2');

        const businessFields = $('.business-fields');
        const attachmentFields = $('.attachment-field');
        const adminFields = $('.admin-fields');
        const adminRequiredFields = $('.admin-required');
        const nonAdminPersonalFields = $('.non-admin-personal-fields');
        const locationFields = $('.location-fields');
        const nonAdminPersonalInputs = nonAdminPersonalFields.find('input, select, textarea');
        const locationInputs = locationFields.find('input, select, textarea');
        const distributorDeliveryFields = $('.distributor-delivery-fields');
        const distributorDeliveryRequiredFields = $('.distributor-delivery-required');
        const areaField = $('.project-area-field');

        const areaSelect = $('#area_name');
        const businessName = $('#business_name');
        const businessType = $('#business_type');
        const partnerCode = $('#store_code');
        const attachment = $('#attachment');

        const dynamicAreaWrapper = $('#dynamic-area-wrapper');
        const projectRows = $('#projectRows');

        function getSelectedProjects() {
            let selected = [];

            $('input[name="type[]"]:checked').each(function () {
                const val = $(this).val();
                if (val !== 'Regular') {
                    selected.push(val);
                }
            });

            return selected;
        }

        function toggleProjectAreas() {
            const selected = getSelectedProjects();

            const isRise = selected.includes('Project Rise');
            const isGenesis = selected.includes('Project Genesis');

            $('.project-row').each(function () {

                const riseCol = $(this).find('.project-rise-area');
                const genesisCol = $(this).find('.project-genesis-area');

                // Show/Hide columns
                riseCol.toggle(isRise);
                genesisCol.toggle(isGenesis);

                // Required rules
                riseCol.find('select').prop('required', isRise);
                genesisCol.find('select').prop('required', isGenesis);

                // Clear hidden values
                if (!isRise) {
                    riseCol.find('select').val(null).trigger('change');
                }

                if (!isGenesis) {
                    genesisCol.find('select').val(null).trigger('change');
                }
            });
        }

        function addProjectRow() {
            const template = document.querySelector('#project-row-template');
            const clone = template.content.cloneNode(true);

            const $row = $(clone);
            projectRows.append($row);

            initSelect2($row);

            toggleProjectAreas();
        }

        $('#addProjectRow').on('click', function () {
            addProjectRow();
        });

        $(document).on('click', '.remove-row', function () {
            if ($('.project-row').length > 1) {
                $(this).closest('.project-row').remove();
            } else {
                alert('At least one row is required.');
            }
        });

        function refreshProjectVisibility() {

            const role = roleFilter.val();
            const isAdmin = role === 'Admin';

            const selectedProjects = getSelectedProjects();
            const hasProject = selectedProjects.length > 0;
            const onlyRegular = selectedProjects.length === 1 && $('input[name="type[]"]:checked').val() === 'Regular';

            if (!role || isAdmin || !hasProject || onlyRegular) {
                dynamicAreaWrapper.hide();
                projectRows.html('');
                return;
            }

            dynamicAreaWrapper.show();

            if ($('.project-row').length === 0) {
                addProjectRow();
            }

            toggleProjectAreas();
        }

        function toggleBusinessFields() {

            const selectedRole = roleFilter.val();

            if (!selectedRole) {
                businessFields.hide();
                attachmentFields.hide();
                adminFields.hide();
                nonAdminPersonalFields.show();
                locationFields.show();
                distributorDeliveryFields.hide();
                areaField.hide();

                businessName.prop('required', false);
                businessType.prop('required', false);
                partnerCode.prop('required', false);
                adminRequiredFields.prop('required', false);
                nonAdminPersonalInputs.prop('disabled', false);
                locationInputs.prop('disabled', false);
                distributorDeliveryRequiredFields.prop('required', false);
                areaSelect.prop('required', false);
                attachment.prop('required', false);

                partnerCode.val('');
                $('input[name="warehouse"]').prop('checked', false);
                $('#delivery_address').val('').prop('readonly', false);
                $('#same_as_address').prop('checked', false);
                $('#same_as_delivery_address').prop('checked', false);
                return;
            }

            const isAdmin = selectedRole === 'Admin';
            const isProvincialDistributor = selectedRole === 'Provincial Distributor';
            const isAreaDistributor = selectedRole === 'Area Distributor';
            const needsDeliveryAddress = isProvincialDistributor || isAreaDistributor;

            const selectedProjects = getSelectedProjects();
            const showArea = selectedProjects.includes('Project Rise') && !isAdmin;

            businessFields.toggle(!isAdmin);
            attachmentFields.toggle(isProvincialDistributor || isAreaDistributor);
            adminFields.toggle(isAdmin);
            nonAdminPersonalFields.toggle(!isAdmin);
            locationFields.toggle(!isAdmin);
            distributorDeliveryFields.toggle(needsDeliveryAddress);
            areaField.toggle(showArea);

            businessName.prop('required', !isAdmin);
            businessType.prop('required', !isAdmin);
            partnerCode.prop('required', !isAdmin);
            adminRequiredFields.prop('required', isAdmin);
            nonAdminPersonalInputs.prop('disabled', isAdmin);
            locationInputs.prop('disabled', isAdmin);
            distributorDeliveryRequiredFields.prop('required', needsDeliveryAddress);

            areaSelect.prop('required', showArea);
            areaSelect.prop('disabled', !showArea);

            attachment.prop('required', isProvincialDistributor || isAreaDistributor);

            if (!showArea) {
                areaSelect.val(null).trigger('change');
            }

            if (!isAdmin) {
                $('input[name="warehouse"]').prop('checked', false);
                $('#same_as_address').prop('checked', false);
                adminRequiredFields.val('');
            } else {
                nonAdminPersonalInputs.filter(':not([type="hidden"])').val('');
                locationInputs.filter(':not([type="hidden"])').val('');
                if (typeof toggleContactRequired === 'function') {
                    toggleContactRequired();
                }
            }

            if (!needsDeliveryAddress) {
                $('#delivery_address').val('').prop('readonly', false);
                $('#same_as_delivery_address').prop('checked', false);
            }

            if (!isAdmin) {
                generatePartnerCode();
            }
        }

        function generatePartnerCode() {

            const role = roleFilter.val();

            if (!role || role === 'Admin') {
                partnerCode.val('');
                return;
            }

            $.ajax({
                url: "{{ route('generate.partner.code') }}",
                type: "POST",
                data: {
                    role: role,
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {
                    partnerCode.val(res.success ? res.code : '');
                },
                error: function () {
                    partnerCode.val('');
                }
            });
        }

        roleFilter.on('change', function () {
            toggleBusinessFields();
            refreshProjectVisibility();
        });

        $(document).on('change', 'input[name="type[]"]', function () {
            toggleBusinessFields();
            refreshProjectVisibility();
            toggleProjectAreas();
        });

        $('#new_users').on('shown.bs.modal', function () {
            toggleBusinessFields();
            refreshProjectVisibility();
        });

        toggleBusinessFields();
        refreshProjectVisibility();
        toggleProjectAreas();
        

    });
</script>
@endsection
