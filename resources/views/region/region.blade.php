@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <a href="/regions" class="link">Back&nbsp;</a>

                    <span>
                        @if (isset($edit)) {{ 'Edit' }}
                        @elseif (isset($delete)) {{ 'Delete' }}
                        @elseif (isset($view)) {{ 'View' }}
                        @else {{ 'Add New' }}
                        @endif
                         Region
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
                                    $block_region = true;
                                ?>
                                @include('layouts.location_select')

                                @if ($errors->has('zone'))
                                    <div class="invalid-feedback d-block">
                                        <strong>{{ $errors->first('zone') }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="name" class="col-md-2 col-form-label text-md-right">
                                {{ __('Region') }}
                            </label>

                            <div class="col-md-9">
                                <input class="form-control{{ $errors->has('name') ? ' has-error is-invalid' : '' }}" type="text" id="name" name="name" placeholder="Region" data-value="{{ old('name') }}"
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
                                    <button class="btn btn-warning float-right" type="submit">Edit Region</button>
                                @elseif (isset($delete))
                                    <button class="btn btn-danger float-right" type="submit">Delete Region</button>
                                    <a href="/regions" class="btn btn-link float-right">Cancel</a>
                                @elseif (isset($view))
                                    <a href="{{ URL::previous() }}" class="btn btn-success float-right">Done</a>
                                @else
                                    <button class="btn btn-success float-right" type="submit">Add Region</button>
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
