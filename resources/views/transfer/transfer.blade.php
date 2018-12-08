@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">

                    @if (isset($edit) || isset($delete) || isset($view))
                        <a href="/transfers/tabs/individual" class="link">Back&nbsp;</a>
                    @else
                        <a href="{{ $previous }}" class="link">Back&nbsp;</a>
                    @endif

                    <span>
                        @if (isset($edit)) {{ 'Edit' }}
                        @elseif (isset($delete)) {{ 'Delete' }}
                        @elseif (isset($view)) {{ 'View' }}
                        @else {{ 'Add New' }}
                        @endif
                         Transfer
                     </span>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ (isset($handler))? route($handler):'#' }}">
                        @csrf

                        @include('layouts.status_alert')

                        <div class="form-group row">
                            <label for="zone" class="col-md-2 col-form-label text-md-right">
                                {{ __('From') }}
                            </label>

                            <div class="col-md-9">
                                <?php
                                    $location_required = true;

                                    $names = [ 'zone'=>'from_zone', 'region'=>'from_region', 'district'=>'from_district', 'center'=>'from_center' ];
                                ?>
                                @include('layouts.location_select')

                                @if ($errors->has('from_zone') || $errors->has('from_region') || $errors->has('from_district') || $errors->has('from_center'))
                                    <span class="invalid-feedback d-block">
                                        <strong>
                                            @if ($errors->has('from_zone'))
                                                {{ $errors->first('from_zone') }}
                                            @elseif ($errors->has('from_region'))
                                                {{ $errors->first('from_region') }}
                                            @elseif ($errors->has('from_district'))
                                                {{ $errors->first('from_district') }}
                                            @elseif ($errors->has('from_center'))
                                                {{ $errors->first('from_center') }}
                                            @endif
                                        </strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="group" class="col-md-2 col-form-label text-md-right">
                                {{ __('To') }}
                            </label>

                            <div class="col-md-9">
                                <?php
                                    $location_required = true;

                                    $names = [ 'zone'=>'to_zone', 'region'=>'to_region', 'district'=>'to_district', 'center'=>'to_center' ];
                                ?>
                                @include('layouts.location_select')

                                @if ($errors->has('to_zone') || $errors->has('to_region') || $errors->has('to_district') || $errors->has('center'))
                                    <div class="invalid-feedback d-block">
                                        <strong>
                                            @if ($errors->has('to_zone'))
                                                {{ $errors->first('to_zone') }}
                                            @elseif ($errors->has('to_region'))
                                                {{ $errors->first('to_region') }}
                                            @elseif ($errors->has('to_district'))
                                                {{ $errors->first('to_district') }}
                                            @elseif ($errors->has('to_center'))
                                                {{ $errors->first('to_center') }}
                                            @endif
                                        </strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="zone" class="col-md-2 col-form-label text-md-right">
                                {{ __('Transfer') }}
                            </label>

                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="control-label label" for="group">Group:</label>
                                        <?php $group_required = true; ?>
                                        @include('layouts.group_select')
                                    </div>

                                    <div class="col-md-6">
                                        <label class="control-label label" for="units">Units:</label>

                                        <input class="form-control{{ $errors->has('units') ? ' has-error is-invalid' : '' }}" type="number" id="units" name="units" placeholder="Units"
                                            @if (!empty(old('units')))
                                                value={{ old('units') }}
                                            @elseif (isset($data['units']) && !empty($data['units']))
                                                value={{ $data['units'] }}
                                            @endif
                                        {{ (isset($view) || isset($delete))? 'readonly':'' }} required />
                                    </div>

                                    <div class="col-md-3">
                                        <label class="control-label label" for="units">Date:</label>

                                        <?php $date_required = true; ?>
                                        @include('layouts.datepicker')
                                    </div>
                                </div>

                                @if ($errors->has('group') || $errors->has('units'))
                                    <div class="invalid-feedback d-block">
                                        <strong>
                                            @if ($errors->has('group'))
                                                {{ $errors->first('group') }}
                                            @elseif ($errors->has('units'))
                                                {{ $errors->first('units') }}
                                            @endif
                                        </strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-2">

                                @if (isset($edit))
                                    <button class="btn btn-warning float-right" type="submit">Edit Transfer</button>
                                @elseif (isset($delete))
                                    <button class="btn btn-danger float-right" type="submit">Delete Transfer</button>
                                    <a href="/transfers/tabs/individual" class="btn btn-link float-right">Cancel</a>
                                @elseif (isset($view))
                                    <a href="/transfers/tabs/individual" class="btn btn-success float-right">Done</a>
                                @else
                                    <button class="btn btn-warning float-right" type="submit">Transfer</button>
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
