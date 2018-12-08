@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <a href="/districts" class="link">Back&nbsp;</a>

                    <span>
                        @if (isset($edit)) {{ 'Edit' }}
                        @elseif (isset($delete)) {{ 'Delete' }}
                        @elseif (isset($view)) {{ 'View' }}
                        @else {{ 'Add New' }}
                        @endif
                         District
                    </span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ (isset($handler))? route($handler):'#' }}">
                        @csrf

                        @include('layouts.status_alert')

                        <div class="form-group row">
                            <label for="zone" class="col-md-2 col-form-label text-md-right">
                                {{ __('Location') }}
                            </label>

                            <div class="col-md-9">
                                <?php
                                    $location_required = true;
                                    $block_district = true;
                                ?>
                                @include('layouts.location_select')

                                @if ($errors->has('zone') || $errors->has('region'))
                                    <div class="invalid-feedback d-block">
                                        <strong>
                                            @if ($errors->has('zone'))
                                                {{ $errors->first('zone') }}
                                            @elseif ($errors->has('region'))
                                                {{ $errors->first('region') }}
                                            @endif
                                        </strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-2 col-form-label text-md-right">
                                {{ __('District') }}
                            </label>

                            <div class="col-md-9">
                                <input class="form-control{{ $errors->has('name') ? ' has-error is-invalid' : '' }}" type="text" id="name" name="name" placeholder="District" data-value="{{ old('name') }}"
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
                            <div class="col-md-9 offset-md-2">

                                @if (isset($edit))
                                    <button class="btn btn-warning float-right" type="submit">Edit District</button>
                                @elseif (isset($delete))
                                    <button class="btn btn-danger float-right" type="submit">Delete District</button>
                                    <a href="/districts" class="btn btn-link float-right">Cancel</a>
                                @elseif (isset($view))
                                    <a href="{{ URL::previous() }}" class="btn btn-success float-right">Done</a>
                                @else
                                    <button class="btn btn-success float-right" type="submit">Add District</button>
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
