<div class="tab-pane active" id="aggregate">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <form method="POST" action="{{ route('editInfo') }}">
                @csrf
                <br />

                @include('layouts.status_alert')
                
                <div class="form-group row">
                    <label for="firstname" class="col-md-2 col-form-label text-md-right">
                        {{ __('Firstname') }}
                    </label>

                    <div class="col-md-9">
                        <input class="form-control{{ $errors->has('firstname') ? ' has-error is-invalid' : '' }}" type="text" id="firstname" name="firstname" placeholder="Firstname" data-value="{{ old('firstname') }}"
                            @if (!empty(old('firstname')))
                                value="{{ old('firstname') }}"
                            @elseif (isset($user->firstname) && !empty($user->firstname))
                                value="{{ $user->firstname }}"
                            @endif
                        required />

                        @if ($errors->has('firstname'))
                            <div class="invalid-feedback d-block">
                                <strong>{{ $errors->first('firstname') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label for="middlename" class="col-md-2 col-form-label text-md-right">
                        {{ __('Middlename') }}
                    </label>

                    <div class="col-md-9">
                        <input class="form-control{{ $errors->has('middlename') ? ' has-error is-invalid' : '' }}" type="text" id="middlename" name="middlename" placeholder="Firstname" data-value="{{ old('middlename') }}"
                            @if (!empty(old('middlename')))
                                value="{{ old('middlename') }}"
                            @elseif (isset($user->middlename) && !empty($user->middlename))
                                value="{{ $user->middlename }}"
                            @endif
                        />

                        @if ($errors->has('middlename'))
                            <div class="invalid-feedback d-block">
                                <strong>{{ $errors->first('middlename') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label for="lastname" class="col-md-2 col-form-label text-md-right">
                        {{ __('Lastname') }}
                    </label>

                    <div class="col-md-9">
                        <input class="form-control{{ $errors->has('lastname') ? ' has-error is-invalid' : '' }}" type="text" id="lastname" name="lastname" placeholder="Firstname" data-value="{{ old('lastname') }}"
                            @if (!empty(old('lastname')))
                                value="{{ old('lastname') }}"
                            @elseif (isset($user->lastname) && !empty($user->lastname))
                                value="{{ $user->lastname }}"
                            @endif
                        required />

                        @if ($errors->has('lastname'))
                            <div class="invalid-feedback d-block">
                                <strong>{{ $errors->first('lastname') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-9 offset-md-2">

                        <button class="btn btn-warning float-right" type="submit">Edit Info</button>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
