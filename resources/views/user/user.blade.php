@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <a href="/users" class="link">Back&nbsp;</a>

                    <span>
                        @if (isset($edit)) {{ 'Edit' }}
                        @elseif (isset($activate)) {{ 'Activate' }}
                        @elseif (isset($deactivate)) {{ 'Deactivate' }}
                        @elseif (isset($view)) {{ 'View' }}
                        @else {{ 'Add New' }}
                        @endif
                         User
                    </span>
                </div>
                <div class="card-body">

                    <form method="POST" action="{{ (isset($handler))? route($handler):'#' }}">
                        @csrf

                        @include('layouts.status_alert')

                        <div class="form-group row">
                            <label for="firstname" class="col-md-2 col-form-label text-md-right">
                                {{ __('Name') }}
                            </label>

                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label class="control-label label" for="firstname">Firstname:</label>

                                        <input class="form-control{{ $errors->has('firstname') ? ' has-error is-invalid' : '' }}" type="text" id="firstname" name="firstname" placeholder="Firstname" data-value="{{ old('firstname') }}"
                                            @if (!empty(old('firstname')))
                                                value="{{ old('firstname') }}"
                                            @elseif (isset($data['firstname']) && !empty($data['firstname']))
                                                value="{{ $data['firstname'] }}"
                                            @endif
                                        {{ (isset($view) || isset($activate) || isset($deactivate))? 'readonly':'' }} required />
                                    </div>

                                    <div class="col-md-4">
                                        <label class="control-label label" for="middlename">Middlename:</label>

                                        <input class="form-control{{ $errors->has('middlename') ? ' has-error is-invalid' : '' }}" type="text" id="middlename" name="middlename" placeholder="Middlename" data-value="{{ old('middlename') }}"
                                            @if (!empty(old('middlename')))
                                                value="{{ old('middlename') }}"
                                            @elseif (isset($data['middlename']) && !empty($data['middlename']))
                                                value="{{ $data['middlename'] }}"
                                            @endif
                                        {{ (isset($view) || isset($activate) || isset($deactivate))? 'readonly':'' }} />
                                    </div>

                                    <div class="col-md-4">
                                        <label class="control-label label" for="lastname">Lastname:</label>

                                        <input class="form-control{{ $errors->has('lastname') ? ' has-error is-invalid' : '' }}" type="text" id="lastname" name="lastname" placeholder="Lastname" data-value="{{ old('lastname') }}"
                                            @if (!empty(old('lastname')))
                                                value="{{ old('lastname') }}"
                                            @elseif (isset($data['lastname']) && !empty($data['lastname']))
                                                value="{{ $data['lastname'] }}"
                                            @endif
                                        {{ (isset($view) || isset($activate) || isset($deactivate))? 'readonly':'' }} required />
                                    </div>
                                </div>

                                @if ($errors->has('firstname') || $errors->has('middlename') || $errors->has('lastname'))
                                    <div class="invalid-feedback d-block">
                                        <strong>
                                            @if ($errors->has('firstname'))
                                                {{ $errors->first('firstname') }}
                                            @elseif ($errors->has('middlename'))
                                                {{ $errors->first('middlename') }}
                                            @elseif ($errors->has('lastname'))
                                                {{ $errors->first('lastname') }}
                                            @endif
                                        </strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="firstname" class="col-md-2 col-form-label text-md-right">
                                {{ __('Account') }}
                            </label>

                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="control-label label" for="email">Email:</label>

                                        <input class="form-control{{ $errors->has('email') ? ' has-error is-invalid' : '' }}" type="email" id="email" name="email" placeholder="Email" data-value="{{ old('email') }}"
                                            @if (!empty(old('email')))
                                                value="{{ old('email') }}"
                                            @elseif (isset($data['email']) && !empty($data['email']))
                                                value="{{ $data['email'] }}"
                                            @endif
                                        {{ (isset($view) || isset($activate) || isset($deactivate))? 'readonly':'' }} />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label label" for="phone">Phone:</label>

                                        <input class="form-control{{ $errors->has('phone') ? ' has-error is-invalid' : '' }}" type="text" size="13" min="10" max="13" id="phone" name="phone" placeholder="Phone" data-value="{{ old('phone') }}"
                                            @if (!empty(old('phone')))
                                                value="{{ old('phone') }}"
                                            @elseif (isset($data['phone']) && !empty($data['phone']))
                                                value="{{ $data['phone'] }}"
                                            @endif
                                        {{ (isset($view) || isset($activate) || isset($deactivate))? 'readonly':'' }} />
                                    </div>
                                </div>

                                @if ($errors->has('email') || $errors->has('phone'))
                                    <div class="invalid-feedback d-block">
                                        <strong>
                                            @if ($errors->has('email'))
                                                {{ $errors->first('email') }}
                                            @elseif ($errors->has('phone'))
                                                {{ $errors->first('phone') }}
                                            @endif
                                        </strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="firstname" class="col-md-2 col-form-label text-md-right">
                                {{ __('Restrictions') }}
                            </label>

                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label class="control-label label" for="email">Role:</label>

                                        <?php $role_required = true; ?>
                                        @include('layouts.role_select')
                                    </div>

                                    <div class="col-md-10">
                                        @include('layouts.location_select')
                                    </div>
                                </div>

                                @if ($errors->has('role') || $errors->has('zone') || $errors->has('region') || $errors->has('district') || $errors->has('center'))
                                    <div class="invalid-feedback d-block">
                                        <strong>
                                            @if ($errors->has('role'))
                                                {{ $errors->first('role') }}
                                            @elseif ($errors->has('zone'))
                                                {{ $errors->first('zone') }}
                                            @elseif ($errors->has('region'))
                                                {{ $errors->first('region') }}
                                            @elseif ($errors->has('district'))
                                                {{ $errors->first('district') }}
                                            @elseif ($errors->has('center'))
                                                {{ $errors->first('center') }}
                                            @endif
                                        </strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-2">

                                @if (isset($edit))
                                    <button class="btn btn-warning float-right" type="submit">Edit User</button>
                                @elseif (isset($activate) || isset($deactivate))
                                    @if (isset($activate))
                                        <button class="btn btn-success float-right" type="submit">Activate User</button>
                                    @elseif (isset($deactivate))
                                        <button class="btn btn-danger float-right" type="submit">Deactivate User</button>
                                    @endif
                                    <a href="/users" class="btn btn-link float-right">Cancel</a>
                                @elseif (isset($view))
                                    <a href="{{ URL::previous() }}" class="btn btn-success float-right">Done</a>
                                @else
                                    <button class="btn btn-success float-right" type="submit">Add User</button>
                                @endif

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
