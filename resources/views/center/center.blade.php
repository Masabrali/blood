@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <a href="/centers" class="link">Back&nbsp;</a>

                    <span>
                        @if (isset($edit)) {{ 'Edit' }}
                        @elseif (isset($delete)) {{ 'Delete' }}
                        @elseif (isset($view)) {{ 'View' }}
                        @else {{ 'Add New' }}
                        @endif
                         Center
                    </span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ (isset($handler))? route($handler):'#' }}">
                        @csrf

                        @include('layouts.status_alert')

                        <div class="form-group row">
                            <label for="zone" class="col-md-2 col-form-label text-md-right">{{ __('Location') }}</label>

                            <div class="col-md-9">
                                <?php
                                    $location_required = true;
                                    $block_center = true;
                                ?>
                                @include('layouts.location_select')

                                @if ($errors->has('zone') || $errors->has('region') || $errors->has('district'))
                                    <div class="invalid-feedback d-block">
                                        <strong>
                                            @if ($errors->has('zone'))
                                                {{ $errors->first('zone') }}
                                            @elseif ($errors->has('region'))
                                                {{ $errors->first('region') }}
                                            @elseif ($errors->has('district'))
                                                {{ $errors->first('district') }}
                                            @endif
                                        </strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-2 col-form-label text-md-right">{{ __('Center') }}</label>

                            <div class="col-md-9">
                                <input class="form-control{{ $errors->has('name') ? ' has-error is-invalid' : '' }}" type="text" id="name" name="name" placeholder="Center" data-value="{{ old('name') }}"
                                    @if (!empty(old('name')))
                                        value="{{ old('name') }}"
                                    @elseif (isset($data['name']) && !empty($data['name']))
                                        value="{{ $data['name'] }}"
                                    @endif
                                {{ (isset($view) || isset($delete))? 'readonly':'' }} required />

                                @if ($errors->has('name'))
                                    <div class="invalid-feedback d-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="a_plus" class="col-md-2 col-form-label text-md-right">{{ __('Storage') }}</label>

                            <div class="col-md-9">

                                <?php $groups = App\Group::all(); ?>
                                <div class="row">
                                    @foreach ($groups as $_group => $group)
                                        @if (($_group % 2) == 0)

                                            <div class="col-md-3">
                                                <label class="control-label label" for="{{ $group->_name }}">{{ strtoupper($group->name) }}: </label>

                                                <input class="form-control{{ $errors->has($group->_name) ? ' has-error is-invalid' : '' }}" type="number" id="{{ $group->_name }}" name="{{ $group->_name }}" placeholder="{{ strtoupper($group->name) }}"
                                                    @if (!empty(old($group->_name)))
                                                        value={{ old($group->_name) }}
                                                    @elseif (isset($data[$group->_name]) && !empty($data[$group->_name]))
                                                        value={{ $data[$group->_name] }}
                                                    @endif
                                                {{ (isset($view) || isset($delete))? 'readonly':'' }} required />
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <br />
                                <div class="row">
                                    @foreach ($groups as $_group => $group)
                                        @if (($_group % 2) != 0)

                                            <div class="col-md-3">
                                                <label class="control-label label" for="{{ $group->_name }}">{{ strtoupper($group->name) }}: </label>

                                                <input class="form-control{{ $errors->has($group->_name) ? ' has-error is-invalid' : '' }}" type="number" id="{{ $group->_name }}" name="{{ $group->_name }}" placeholder="{{ strtoupper($group->name) }}" data-value="{{ old($group->_name) }}"
                                                    @if (!empty(old($group->_name)))
                                                        value={{ old($group->_name) }}
                                                    @elseif (isset($data[$group->_name]) && !empty($data[$group->_name]))
                                                        value={{ $data[$group->_name] }}
                                                    @endif
                                                {{ (isset($view) || isset($delete))? 'readonly':'' }} required />
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                @foreach ($groups as $group)

                                    @if ($errors->has($group->_name))
                                        <div class="invalid-feedback d-block">
                                            <strong>{{ $errors->first($group->_name) }}</strong>
                                        </div>
                                        <?php break; ?>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-2">

                                @if (isset($edit))
                                    <button class="btn btn-warning float-right" type="submit">Edit Center</button>
                                @elseif (isset($delete))
                                    <button class="btn btn-danger float-right" type="submit">Delete Center</button>
                                    <a href="/centers" class="btn btn-link float-right">Cancel</a>
                                @elseif (isset($view))
                                    <a href="{{ URL::previous() }}" class="btn btn-success float-right">Done</a>
                                @else
                                    <button class="btn btn-success float-right" type="submit">Add Center</button>
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
