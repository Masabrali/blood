<select class="form-control selectpicker{{ $errors->has('group') ? ' has-error is-invalid' : '' }}" name="group" id="group" {{ (isset($view) || isset($delete))? 'readonly':'' }} {{ (isset($group_required))? 'required':'' }}>
    <?php

        $_group = NULL;

        if (!empty(old('group'))) $_group = old('group');
        else if (isset($data['group']) && !empty($data['group'])) $_group = $data['group'];

    ?>
    <option value>Group</option>
    @foreach (\App\Group::orderBy('name', 'ASC')->get() as $group)
        <option value="{{ $group->id }}" {{ ($group->id == $_group)? 'selected' : '' }}>
            {{ $group->name }}
        </option>
    @endforeach
</select>
