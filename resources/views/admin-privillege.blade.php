{{-- <div class="modal fade" id="access-admin-{{ $user->id }}" tabindex="-1" aria-labelledby="accessAdminModalLabel-{{ $user->id }}" aria-hidden="true" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accessAdminModalLabel-{{ $user->id }}">Access Control</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ url('admin-privillege', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Admin Name</label>
                        <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        
                        <div class="form-check mb-2">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                name="can_edit" 
                                id="can_edit-{{ $user->id }}" 
                                value="on"
                                {{ ($user->can_edit ?? false) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="can_edit-{{ $user->id }}">
                                Edit
                            </label>
                        </div>
                        
                        <div class="form-check">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                name="can_add" 
                                id="can_add-{{ $user->id }}" 
                                value="on"
                                {{ ($user->can_add ?? false) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="can_add-{{ $user->id }}">
                                Add
                            </label>
                        </div>
                        <div class="form-check">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                name="can_delete" 
                                id="can_delete-{{ $user->id }}" 
                                value="on"
                                {{ ($user->can_delete ?? false) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="can_delete-{{ $user->id }}">
                                Delete
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Rewards</label>
                        
                        <div class="form-check mb-2">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                name="can_edit_rewards" 
                                id="can_edit_rewards-{{ $user->id }}" 
                                value="on"
                                {{ ($user->can_edit_rewards ?? false) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="can_edit_rewards-{{ $user->id }}">
                                Edit
                            </label>
                        </div>
                        
                        <div class="form-check">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                name="can_add_rewards" 
                                id="can_add_rewards-{{ $user->id }}" 
                                value="on"
                                {{ ($user->can_add_rewards ?? false) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="can_add_rewards-{{ $user->id }}">
                                Add
                            </label>
                        </div>
                        <div class="form-check">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                name="can_delete_rewards" 
                                id="can_delete_rewards-{{ $user->id }}" 
                                value="on"
                                {{ ($user->can_delete_rewards ?? false) ? 'checked' : '' }}
                            >
                            <label class="form-check-label" for="can_delete_rewards-{{ $user->id }}">
                                Delete
                            </label>
                        </div>
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
</div> --}}

<div class="modal fade admin-access-modal" id="accessUserModal" tabindex="-1" aria-labelledby="userAccessTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title text-white" id="userAccessTitle">Admin Access</h5>
          <p>Choose which sidebar modules <strong id="accessAdminName">this administrator</strong> can open.</p>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="access_user_id">
        <div class="access-modules-heading">
          <div>
            <h6>Sidebar modules</h6>
            <small>Dashboard stays available so the account always has a safe landing page.</small>
          </div>
          <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllModules">Select all</button>
        </div>
        <div class="access-modules-grid">
          @foreach(config('admin_modules') as $key => $module)
            <label class="access-module-card" for="module_{{ $key }}">
              <span class="access-module-icon"><i class="bi {{ $module['icon'] }}"></i></span>
              <span class="access-module-copy">
                <strong>{{ $module['label'] }}</strong>
                <small>{{ $key === 'dashboard' ? 'Always enabled' : 'Show in sidebar and allow module access' }}</small>
              </span>
              <input type="checkbox" class="form-check-input module-access-toggle" id="module_{{ $key }}" data-module="{{ $key }}" {{ $key === 'dashboard' ? 'checked disabled' : '' }}>
            </label>
          @endforeach
        </div>

        <details class="access-advanced">
          <summary>Advanced permissions</summary>
          <div class="access-advanced-grid">
            <fieldset>
              <legend>General</legend>
              <label><input type="checkbox" id="can_edit"> Edit records</label>
              <label><input type="checkbox" id="can_add"> Add records</label>
              <label><input type="checkbox" id="can_delete"> Delete records</label>
            </fieldset>
            <fieldset>
              <legend>Rewards</legend>
              <label><input type="checkbox" id="can_edit_rewards"> Edit rewards</label>
              <label><input type="checkbox" id="can_add_rewards"> Add rewards</label>
              <label><input type="checkbox" id="can_delete_rewards"> Delete rewards</label>
            </fieldset>
          </div>
        </details>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveAccess"><i class="bi bi-check2 me-1"></i> Save access</button>
      </div>
    </div>
  </div>
</div>

<style>
  .admin-access-modal .modal-content { overflow: hidden; border: 0; border-radius: 18px; box-shadow: 0 24px 70px rgba(16,42,67,.24); }
  .admin-access-modal .modal-header { padding: 20px 24px; color: #fff; background: linear-gradient(120deg,#123c61,#1592bd); border: 0; }
  .admin-access-modal .modal-title { font-size: 18px; font-weight: 900; }
  .admin-access-modal .modal-header p { margin: 4px 0 0; color: rgba(255,255,255,.78); font-size: 12px; }
  .admin-access-modal .modal-body { padding: 22px 24px; }
  .access-modules-heading { display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:14px; }
  .access-modules-heading h6 { margin:0 0 3px; color:#193b59; font-weight:900; }
  .access-modules-heading small { color:#718096; font-size:11px; }
  .access-modules-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; }
  .access-module-card { display:flex; align-items:center; gap:11px; min-width:0; padding:12px; border:1px solid #e1e9f0; border-radius:12px; background:#fff; cursor:pointer; transition:.18s ease; }
  .access-module-card:hover { border-color:#1592bd; background:#f7fcff; }
  .access-module-icon { display:grid; flex:0 0 38px; width:38px; height:38px; place-items:center; color:#1688b3; background:#e6f7fc; border-radius:10px; font-size:16px; }
  .access-module-copy { flex:1; min-width:0; }
  .access-module-copy strong,.access-module-copy small { display:block; }
  .access-module-copy strong { color:#193b59; font-size:12px; }
  .access-module-copy small { margin-top:2px; color:#8795a4; font-size:10px; }
  .access-module-card .form-check-input { flex:0 0 auto; width:18px; height:18px; margin:0; accent-color:#1592bd; }
  .access-advanced { margin-top:18px; padding:12px 14px; border:1px solid #e7edf3; border-radius:12px; background:#f8fafc; }
  .access-advanced summary { color:#40556b; font-size:12px; font-weight:800; cursor:pointer; }
  .access-advanced-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; margin-top:14px; }
  .access-advanced fieldset { display:grid; gap:8px; margin:0; padding:0; border:0; }
  .access-advanced legend { margin-bottom:6px; color:#193b59; font-size:11px; font-weight:900; }
  .access-advanced label { color:#52677c; font-size:12px; }
  .access-advanced input { margin-right:6px; accent-color:#1592bd; }
  .admin-access-modal .modal-footer { padding:14px 24px; border-top-color:#edf1f5; }
  .admin-access-modal .modal-footer .btn { min-height:39px; padding-inline:15px; border-radius:9px; font-size:12px; font-weight:800; }
  .admin-access-modal .modal-footer .btn-primary { background:#1592bd; border-color:#1592bd; }
  @media(max-width:575px) { .access-modules-grid,.access-advanced-grid { grid-template-columns:1fr; } .admin-access-modal .modal-header,.admin-access-modal .modal-body { padding:17px; } .admin-access-modal .modal-footer { padding:12px 17px; } }
</style>
