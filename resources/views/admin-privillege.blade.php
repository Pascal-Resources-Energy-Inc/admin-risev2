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

<div class="modal fade" id="accessUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-header">
        <h5>User Access</h5>
      </div>

      <div class="modal-body">
        <input type="hidden" id="access_user_id">

        <!-- ADMIN PERMISSIONS -->
        <label>Permissions</label>

        <div class="form-check">
          <input type="checkbox" id="can_edit" class="form-check-input">
          <label>Edit</label>
        </div>

        <div class="form-check">
          <input type="checkbox" id="can_add" class="form-check-input">
          <label>Add</label>
        </div>

        <div class="form-check">
          <input type="checkbox" id="can_delete" class="form-check-input">
          <label>Delete</label>
        </div>

        <hr>

        <!-- REWARDS -->
        <label>Rewards</label>

        <div class="form-check">
          <input type="checkbox" id="can_edit_rewards" class="form-check-input">
          <label>Edit</label>
        </div>

        <div class="form-check">
          <input type="checkbox" id="can_add_rewards" class="form-check-input">
          <label>Add</label>
        </div>

        <div class="form-check">
          <input type="checkbox" id="can_delete_rewards" class="form-check-input">
          <label>Delete</label>
        </div>

      </div>

      <div class="modal-footer">
        <button class="btn btn-success" id="saveAccess">Save</button>
      </div>

    </div>
  </div>
</div>

<style>
    .form-check {
        margin-left: 10px !important;
    }
</style>