<div class="tab-pane active" id="aggregate">
    <h4 class="text-center">{{ ucfirst($settings) }} Verification</h4>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <form method="POST" action="/settings/{{ $settings }}/verify">
                @csrf
                <br />

                @include('layouts.status_alert')

                <h5>A verification code has been sent to <strong>{{ $verification }}</strong>. Enter the code below:</h5>

                <h5>Have not received code? <a class="link" href="/settings/{{ $settings }}/verify/resend">Resend Code</a></h5>

                <br/>
                <div class="form-group row">
                    <label for="code" class="col-md-3 col-form-label text-md-right">
                        {{ __('Verification Code') }}
                    </label>

                    <div class="col-md-9">
                        <input class="form-control{{ $errors->has('code') ? ' has-error is-invalid' : '' }}" type="text" id="code" name="code" placeholder="Verification Code" size="6" min="6" max="6" value="{{ old('code') }}" required />

                        @if ($errors->has('code'))
                            <div class="invalid-feedback d-block">
                                <strong>{{ $errors->first('code') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-9 offset-md-3">

                        <button class="btn btn-warning float-right" type="submit">Verify {{ ucfirst($settings) }}</button>

                        @if (!empty($user->email_verification) || !empty($user->phone_verification))
                            <a href="/settings/{{ $settings }}/verify/cancel" class="btn btn-link float-right">Cancel</a>
                        @endif

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
