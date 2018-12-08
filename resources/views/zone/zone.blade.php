@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <a href="/zones" class="link">Back&nbsp;</a>

                    <span>
                        @if (isset($edit)) {{ 'Edit' }}
                        @elseif (isset($delete)) {{ 'Delete' }}
                        @elseif (isset($view)) {{ 'View' }}
                        @else {{ 'Add New' }}
                        @endif
                         Zone
                    </span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ (isset($handler))? route($handler):'#' }}">
                        @csrf

                        @include('layouts.status_alert')

                        <div class="form-group row">
                            <label for="name" class="col-md-2 col-form-label text-md-right">
                                {{ __('Zone') }}
                            </label>

                            <div class="col-md-9">
                                <input class="form-control{{ $errors->has('name') ? ' has-error is-invalid' : '' }}" type="text" id="name" name="name" placeholder="Zone" data-value="{{ old('name') }}"
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
                                    <button class="btn btn-warning float-right" type="submit">Edit Zone</button>
                                @elseif (isset($delete))
                                    <button class="btn btn-danger float-right" type="submit">Delete Zone</button>
                                    <a href="/zones" class="btn btn-link float-right">Cancel</a>
                                @elseif (isset($view))
                                    <a href="{{ URL::previous() }}" class="btn btn-success float-right">Done</a>
                                @else
                                    <button class="btn btn-success float-right" type="submit">Add Zone</button>
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
