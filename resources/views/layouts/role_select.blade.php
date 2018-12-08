<select class="form-control selectpicker{{ $errors->has('role') ? ' has-error is-invalid' : '' }}" name="role" id="role" {{ (isset($view) || isset($delete) || isset($activate) || isset($deactivate))? 'readonly':'' }} {{ (isset($role_required))? 'required':'' }}>
    <?php

        $_role = NULL;

        if (!empty(old('role'))) $_role = old('role');
        else if (isset($data['role']) && !empty($data['role'])) $_role = $data['role'];

    ?>
    <option value>Role</option>
    @foreach (\App\Role::orderBy('display_name', 'ASC')->get() as $role)
        <option value="{{ $role->id }}" {{ ($role->id == $_role)? 'selected' : '' }}>
            {{ $role->display_name }}
        </option>
    @endforeach
</select>
