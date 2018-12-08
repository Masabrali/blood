<div class="tab-pane active" id="aggregate">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <form method="POST" action="/settings/email/edit">
                @csrf
                <br />

                @include('layouts.status_alert')

                @if (!empty($user->email))
                    <h5>Your current Email Address is <strong>{{ $user->email }}</strong>. Enter a new Email Address below:</h5>

                    <br />
                @endif

                <div class="form-group row">
                    <label for="email" class="col-md-2 col-form-label text-md-right">
                        {{ __('Email') }}
                    </label>

                    <div class="col-md-9">
                        <input class="form-control{{ $errors->has('email') ? ' has-error is-invalid' : '' }}" type="text" id="email" name="email" placeholder="Email" value="{{ old('email') }}" required />

                        @if ($errors->has('email'))
                            <div class="invalid-feedback d-block">
                                <strong>{{ $errors->first('email') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-9 offset-md-2">

                        <button class="btn btn-warning float-right" type="submit">Change Email</button>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
