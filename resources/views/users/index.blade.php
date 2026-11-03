@extends('layouts.header')
<link rel="icon" type="image/png" href="{{asset('images/logo_nya.png')}}">
@section('css')
<style>
    .users-page{display:grid;gap:18px;padding:4px 0 28px}
    .users-head{align-items:flex-end;display:flex;gap:20px;justify-content:space-between}
    .users-eyebrow{color:#1688b3;font-size:11px;font-weight:900;letter-spacing:.08em;margin:0;text-transform:uppercase}
    .users-head h1{color:#102a43;font-size:28px;font-weight:900;letter-spacing:-.04em;margin:4px 0}
    .users-head-copy{color:#718197;font-size:13px;margin:0}
    .users-add{align-items:center;background:#1592bd;border:0;border-radius:9px;box-shadow:0 7px 16px rgba(21,146,189,.2);color:#fff;display:inline-flex;font-size:12px;font-weight:800;gap:7px;min-height:40px;padding:10px 14px;white-space:nowrap}
    .users-add:hover{background:#117da4;color:#fff}
    .users-stats{display:grid;gap:14px;grid-template-columns:repeat(4,minmax(0,1fr))}
    .users-stat{align-items:center;background:#fff;border:1px solid #e1e9f0;border-radius:12px;box-shadow:0 8px 20px rgba(20,54,79,.04);display:flex;gap:12px;min-height:91px;padding:15px}
    .users-stat-icon{align-items:center;border-radius:10px;display:grid;flex:0 0 42px;height:42px;justify-content:center;width:42px}
    .users-stat-icon i{display:block!important;font-size:20px!important;line-height:1!important}
    .users-stat-icon.total{background:#e4f7fd;color:#1592bd}.users-stat-icon.admin{background:#edf0ff;color:#6963d9}.users-stat-icon.dealer{background:#e8f8ee;color:#198754}.users-stat-icon.customer{background:#fff4dc;color:#d98500}
    .users-stat small,.users-stat span{color:#8997a5;font-size:11px}.users-stat strong{color:#162e49;display:block;font-size:23px;font-weight:900;line-height:1;margin:4px 0}
    .users-panel{background:#fff;border:1px solid #dfe8f0;border-radius:13px;box-shadow:0 12px 30px rgba(25,57,85,.055);overflow:hidden}
    .users-panel-head{align-items:center;background:linear-gradient(135deg,#fff,#f8fbfc);border-bottom:1px solid #edf1f4;display:flex;justify-content:space-between;padding:19px 21px 15px}
    .users-panel-head h2{color:#172f4a;font-size:17px;font-weight:900;margin:0}.users-panel-head p{color:#8391a0;font-size:12px;margin:4px 0 0}
    .users-total-badge{background:#e4f7fd;border-radius:99px;color:#18799b;font-size:11px;font-weight:800;padding:7px 10px;white-space:nowrap}
    .users-toolbar{align-items:center;border-bottom:1px solid #edf1f4;display:flex;flex-wrap:wrap;gap:12px;justify-content:space-between;padding:14px 21px}
    .users-search{align-items:center;background:#fafcfe;border:1px solid #dce6ed;border-radius:8px;color:#8795a4;display:flex;gap:8px;max-width:420px;padding:0 11px;width:100%}
    .users-search:focus-within{border-color:#58a6cb;box-shadow:0 0 0 3px #e4f5fc}.users-search input{background:transparent;border:0;color:#25364b;font-size:13px;height:39px;outline:0;width:100%}
    .users-role-filter{align-items:center;display:flex;gap:9px}.users-role-filter label{color:#718197;font-size:11px;font-weight:800;white-space:nowrap}.users-role-filter select{background:#fff;border:1px solid #dce6ed;border-radius:8px;color:#354c63;font-size:12px;min-height:39px;min-width:175px;padding:0 30px 0 11px}
    .users-table-wrap{overflow-x:auto;padding:0 21px}.users-table{margin:0!important;min-width:830px;width:100%!important}.users-table thead th{background:#f8fafc;border-bottom:1px solid #e8eef3!important;color:#718196;font-size:10px;font-weight:900;letter-spacing:.055em;padding:12px 13px!important;text-transform:uppercase;white-space:nowrap}.users-table tbody td{border-bottom:1px solid #edf2f5;color:#354b61;font-size:12px;padding:13px!important;vertical-align:middle}.users-table tbody tr:hover{background:#f7fcfe}.users-table tbody tr:last-child td{border-bottom:0}
    .users-table .dataTables_empty{text-align:center;padding:40px!important;color:#8291a1}.user-name-wrap{align-items:center;display:flex;gap:9px;min-width:180px}.user-avatar-initial{align-items:center;background:#dff3fb;border-radius:10px;color:#176d96;display:inline-flex;flex:0 0 34px;font-size:12px;font-weight:900;height:34px;justify-content:center}.user-name-cell{color:#203a54;font-weight:800}.user-email-cell{color:#526b81}.user-address-cell{color:#718197;display:block;max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.user-status-pill,.user-role-pill{border-radius:99px;display:inline-flex;font-size:10px;font-weight:900;padding:6px 9px;text-transform:uppercase;white-space:nowrap}.user-status-pill.active{background:#e8f8ee;color:#16834a}.user-status-pill.inactive{background:#fff0ef;color:#c43c31}.user-status-pill.other{background:#f0f3f6;color:#64748b}.user-role-pill.admin{background:#edf0ff;color:#6258cf}.user-role-pill.dealer{background:#e8f8ee;color:#16834a}.user-role-pill.client{background:#e4f7fd;color:#18799b}.user-role-pill.other{background:#f0f3f6;color:#64748b}
    .users-actions{display:flex;gap:6px;justify-content:flex-start;white-space:nowrap}.users-actions .btn{align-items:center;border:1px solid transparent;border-radius:7px;display:inline-flex;font-size:11px;font-weight:800;gap:5px;min-height:31px;padding:5px 9px}.users-actions .btn-edit-user{background:#edf6ff;border-color:#cfe5f7;color:#1876a2}.users-actions .btn-edit-user:hover{background:#dff1fb}.users-actions .btn-access-user{background:#fff5e5;border-color:#f7dfb6;color:#a35d00}.users-actions .btn-access-user:hover{background:#ffedce}
    .users-panel .dataTables_wrapper{padding:0 21px 15px}.users-panel .dataTables_filter{display:none}.users-dt-footer{align-items:center;border-top:1px solid #edf2f5;display:flex;flex-wrap:wrap;gap:10px;justify-content:space-between;padding-top:13px}.users-panel .dataTables_length{color:#7b8795;font-size:11px;padding:12px 0}.users-panel .dataTables_length select{background:#fff;border:1px solid #dbe5ed;border-radius:6px;color:#36536a;font-size:11px;margin:0 5px;padding:5px 22px 5px 8px}.users-panel .dataTables_info{color:#77889a;font-size:11px;padding:0}.users-panel .dataTables_paginate{padding:0}.users-panel .dataTables_processing{background:rgba(255,255,255,.94)!important;border:1px solid #dce8f0!important;border-radius:10px;box-shadow:0 8px 24px rgba(25,57,85,.09);color:#1688b3!important;font-size:12px!important;font-weight:800;left:50%!important;padding:13px 20px!important;top:50%!important;transform:translate(-50%,-50%);width:auto!important}
    @media(max-width:900px){.users-stats{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:640px){.users-page{gap:14px}.users-head{align-items:stretch;flex-direction:column}.users-head h1{font-size:24px}.users-add{justify-content:center}.users-stats{gap:9px}.users-stat{min-height:78px;padding:11px}.users-stat-icon{flex-basis:36px;height:36px;width:36px}.users-stat strong{font-size:20px}.users-panel-head,.users-toolbar{padding-left:14px;padding-right:14px}.users-toolbar{align-items:stretch;flex-direction:column}.users-search{max-width:none}.users-role-filter{align-items:stretch;flex-direction:column;gap:5px}.users-role-filter select{width:100%}.users-table-wrap{padding:0 14px}.users-panel .dataTables_wrapper{padding:0 14px 14px}.users-panel .dataTables_paginate{overflow-x:auto;max-width:100%;white-space:nowrap}}
    @media(max-width:390px){.users-stats{grid-template-columns:1fr}.users-panel-head{align-items:flex-start}.users-total-badge{font-size:10px}}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

@endsection
@section('content')
<div class="users-page">
    <header class="users-head">
        <div>
            <p class="users-eyebrow"><i class="bi bi-people-fill"></i> Account management</p>
            <h1>Users</h1>
            <p class="users-head-copy">Manage administrator access and review customer and dealer accounts.</p>
        </div>
        @php
            $currentUser = auth()->user();
            $canShowAddAdmin = $currentUser && $currentUser->role === 'Admin' && $currentUser->can_add === 'on';
        @endphp
        @if($canShowAddAdmin)
            <button type="button" class="users-add" data-bs-toggle="modal" data-bs-target="#new_users">
                <i class="bi bi-plus-lg"></i> Add admin user
            </button>
        @endif
    </header>

    <section class="users-stats" aria-label="User account summary">
        <article class="users-stat"><span class="users-stat-icon total"><i class="bi bi-people-fill" aria-hidden="true"></i></span><div><small>Total users</small><strong>{{ number_format($userStats['total']) }}</strong><span>All account types</span></div></article>
        <article class="users-stat"><span class="users-stat-icon admin"><i class="bi bi-shield-lock-fill" aria-hidden="true"></i></span><div><small>Administrators</small><strong>{{ number_format($userStats['admins']) }}</strong><span>System access accounts</span></div></article>
        <article class="users-stat"><span class="users-stat-icon dealer"><i class="bi bi-shop" aria-hidden="true"></i></span><div><small>Dealers</small><strong>{{ number_format($userStats['dealers']) }}</strong><span>Dealer and mega dealer accounts</span></div></article>
        <article class="users-stat"><span class="users-stat-icon customer"><i class="bi bi-person-fill" aria-hidden="true"></i></span><div><small>Customers</small><strong>{{ number_format($userStats['customers']) }}</strong><span>Customer accounts</span></div></article>
    </section>

    <section class="users-panel">
        <div class="users-panel-head">
            <div><h2>User directory</h2><p>Search accounts, filter by role, and manage user access.</p></div>
            <span class="users-total-badge">{{ number_format($userStats['total']) }} records</span>
        </div>
        <div class="users-toolbar">
            <label class="users-search" for="userSearch"><i class="bi bi-search"></i><input type="search" id="userSearch" placeholder="Search name, email, address, or status" autocomplete="off"></label>
            <div class="users-role-filter"><label for="roleFilter">Filter by role</label><select id="roleFilter"><option value="">All roles</option><option value="Admin">Admin</option><option value="Dealer">Dealer</option><option value="Mega Dealer">Mega Dealer</option><option value="Client">Customer</option><option value="Area Distributor">Area Distributor</option></select></div>
        </div>
        <table id="example" class="table users-table" style="width:100%">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Status</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </section>
</div>

@include('new_admin')
@if(auth()->user()->role === 'Admin' && auth()->user()->can_add === 'on')
    @include('users.create')
@endif
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
            pageLength: 10,
            lengthMenu: [10, 15, 25, 50],
            dom: 'lrt<"users-dt-footer"ip>',
            language: {
                processing: 'Loading users…',
                emptyTable: 'No user accounts found.',
                zeroRecords: 'No users match your search.'
            },
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
        $('#userSearch').on('input', function () {
            table.search(this.value).draw();
        });

        $(document).on('click', '.btn-edit-user', function () {

            let id = $(this).data('id');

            $.get('/users/' + id + '/show', function (res) {
                $('#edit_user_id').val(res.id);
                const isAdmin = res.role === 'Admin';
                $('#edit_user_id').val(res.id);
                $('#edit_role').val(res.role || '');
                $('#editUserTitle').text(isAdmin ? 'Edit Admin User' : 'Edit User');
                $('#editUserSubtitle').text(isAdmin
                    ? 'Update administrator details, work information, and account status.'
                    : 'Update account name and email address.');
                $('#editAdminFields').prop('hidden', !isAdmin);
                $('#editBasicFields').prop('hidden', isAdmin);

                if (isAdmin) {
                    const nameParts = String(res.name || '').trim().split(/\s+/).filter(Boolean);
                    const fallbackFirst = nameParts.shift() || '';
                    const fallbackLast = nameParts.length ? nameParts.pop() : '';
                    const fallbackMiddle = nameParts.join(' ');
                    $('#edit_first_name').val(res.first_name || fallbackFirst);
                    $('#edit_middle_name').val(res.middle_name || fallbackMiddle);
                    $('#edit_last_name').val(res.last_name || fallbackLast);
                    $('#edit_email').val(res.email || '');
                    $('#edit_employee_number').val(res.employee_number || '');
                    $('#edit_department').val(res.department || '');
                    $('#edit_designation').val(res.designation || '');
                    $('#edit_status').val(res.status || 'Active');
                } else {
                    $('#edit_name').val(res.name || '');
                    $('#edit_email_basic').val(res.email || '');
                }

                $('#editUserModal').modal('show');
            });
        });

        $('#saveUser').click(function () {
            const isAdmin = $('#edit_role').val() === 'Admin';
            const payload = {
                id: $('#edit_user_id').val(),
                role: $('#edit_role').val(),
                email: isAdmin ? $('#edit_email').val() : $('#edit_email_basic').val()
            };

            if (isAdmin) {
                payload.first_name = $('#edit_first_name').val().trim();
                payload.middle_name = $('#edit_middle_name').val().trim();
                payload.last_name = $('#edit_last_name').val().trim();
                payload.designation = $('#edit_designation').val().trim();
                payload.employee_number = $('#edit_employee_number').val().trim();
                payload.department = $('#edit_department').val().trim();
                payload.status = $('#edit_status').val();
            } else {
                payload.name = $('#edit_name').val().trim();
            }

            $.post('/users/update', payload, function (res) {

                if (res.success) {
                    $('#editUserModal').modal('hide');
                    $('#example').DataTable().ajax.reload(null, false);
                    Swal.fire({
                        icon: 'success',
                        title: 'User updated',
                        text: res.message || 'User details were saved successfully.',
                        confirmButtonText: 'Continue',
                        confirmButtonColor: '#1592bd'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update failed',
                        text: res.message || 'The user details could not be saved.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#1592bd'
                    });
                }
            }).fail(function (xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'The user details could not be saved. Please try again.';
                Swal.fire({
                    icon: 'error',
                    title: 'Update failed',
                    text: message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#1592bd'
                });
            });
        });

        $(document).on('click', '.btn-access-user', function () {

            let id = $(this).data('id');

            $.get('/users/' + id + '/show', function (res) {

                $('#access_user_id').val(res.id);
                $('#accessAdminName').text(res.name || 'this administrator');

                // ✅ FIX: CHECKBOX (not select anymore)
                $('#can_edit').prop('checked', res.can_edit === 'on');
                $('#can_add').prop('checked', res.can_add === 'on');
                $('#can_delete').prop('checked', res.can_delete === 'on');

                $('#can_edit_rewards').prop('checked', res.can_edit_rewards === 'on');
                $('#can_add_rewards').prop('checked', res.can_add_rewards === 'on');
                $('#can_delete_rewards').prop('checked', res.can_delete_rewards === 'on');

                const moduleAccess = res.module_access;
                const hasModuleSettings = moduleAccess && Object.keys(moduleAccess).length > 0;
                $('.module-access-toggle').each(function () {
                    const module = $(this).data('module');
                    const enabled = module === 'dashboard' || !hasModuleSettings
                        || moduleAccess[module] === true || moduleAccess[module] === 'on';
                    $(this).prop('checked', enabled);
                });

                $('#accessUserModal').modal('show');
            });
        });

        $('#selectAllModules').click(function () {
            $('.module-access-toggle:not(:disabled)').prop('checked', true);
        });

        $('#saveAccess').click(function () {

            const moduleAccess = {};
            $('.module-access-toggle:checked').each(function () {
                moduleAccess[$(this).data('module')] = true;
            });

            $.post('/users/access-update', {
                id: $('#access_user_id').val(),

                can_edit: $('#can_edit').is(':checked') ? 'on' : 'off',
                can_add: $('#can_add').is(':checked') ? 'on' : 'off',
                can_delete: $('#can_delete').is(':checked') ? 'on' : 'off',

                can_edit_rewards: $('#can_edit_rewards').is(':checked') ? 'on' : 'off',
                can_add_rewards: $('#can_add_rewards').is(':checked') ? 'on' : 'off',
                can_delete_rewards: $('#can_delete_rewards').is(':checked') ? 'on' : 'off',
                module_access: moduleAccess,

            }, function (res) {

                if (res.success) {
                    $('#accessUserModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Access updated',
                        text: 'The admin module access was saved.',
                        confirmButtonText: 'Continue',
                        confirmButtonColor: '#1592bd'
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Could not save access', text: res.message || 'Please try again.' });
                }
            }).fail(function (xhr) {
                const message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'Please try again.';
                Swal.fire({ icon: 'error', title: 'Could not save access', text: message });
            });

        });

    });
</script>

@endsection

@section('js')
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: 'Admin user created',
        text: @json(session('success')),
        confirmButtonText: 'Continue',
        confirmButtonColor: '#1592bd'
    });
</script>
@endif
@endsection
