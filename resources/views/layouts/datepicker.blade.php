<input type="date" class="form-control datetimepicker{{ $errors->has('group') ? ' has-error is-invalid' : '' }}" name="date" id="date"
    @if (!empty(old('date')))
        value={{ old('date') }}
    @elseif (isset($data['date']) && !empty($data['date']))
        value={{ $data['date'] }}
    @else
        value={{ date('Y-m-d') }}
    @endif
{{ (isset($view) || isset($delete))? 'readonly':'' }} {{ (isset($date_required))? 'required':'' }} />
