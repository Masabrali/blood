@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            @include('layouts.filter')

            <div class="card">
                <div class="card-header">

                  <h3 class="float-left">Settings</h3>

                  <nav class="nav nav-pills justify-content-center " role="tablist">
                      <a class="nav-item nav-link {{ ($settings == 'avatar')? 'active':'' }}" href="{{ ($settings == 'avatar')? '#avatar':'/settings/avatar' }}">
                          Avatar
                      </a>
                      <a class="nav-item nav-link {{ ($settings == 'info')? 'active':'' }}" href="{{ ($settings == 'info')? '#info':'/settings/info' }}">
                          Info
                      </a>
                      <a class="nav-item nav-link {{ ($settings == 'phone')? 'active':'' }}" href="{{ ($settings == 'phone')? '#phone':'/settings/phone' }}">
                          Phone
                      </a>
                      <a class="nav-item nav-link {{ ($settings == 'email')? 'active':'' }}" href="{{ ($settings == 'email')? '#email':'/settings/email' }}">
                          Email
                      </a>
                      <a class="nav-item nav-link {{ ($settings == 'password')? 'active':'' }}" href="{{ ($settings == 'password')? '#password':'/settings/password' }}">
                          Password
                      </a>
                  </nav>
                </div>

                <div class="card-body">
                    <div class="tab-content">
                        @if ($settings == 'avatar') @include('settings.avatar')
                        @elseif ($settings == 'info') @include('settings.info')
                        @elseif (($settings == 'phone' || $settings == 'email') && !empty($verification))
                            @include('settings.verify')
                        @elseif ($settings == 'phone') @include('settings.phone')
                        @elseif ($settings == 'email') @include('settings.email')
                        @elseif ($settings == 'password') @include('settings.password')
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
