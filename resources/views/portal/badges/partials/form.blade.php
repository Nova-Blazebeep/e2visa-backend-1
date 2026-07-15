@php
    $isEdit = isset($badge);
    $roleId = old('role_id', $isEdit ? $badge->role_id : '');
@endphp

<div class="mb-3">
    <label for="role_id" class="form-label">Role</label>
    <select name="role_id" id="role_id" class="form-control" required>
        <option value="">Select Role</option>
        @foreach (\Spatie\Permission\Models\Role::all() as $role)
            <option value="{{ $role->id }}" {{ $roleId == $role->id ? 'selected' : '' }}>
                {{ $role->name }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label for="icon" class="form-label">Icon</label>
    <input type="file" name="icon" id="icon" class="form-control" {{ $isEdit ? '' : 'required' }}
        accept="image/*">
</div>
