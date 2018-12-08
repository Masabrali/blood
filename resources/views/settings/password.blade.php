<div class="tab-pane active" id="aggregate">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <form method="POST" action="{{ route('editPassword') }}">
                @csrf
                <br />

                @include('layouts.status_alert')
                
                <div class="form-group row">
                    <label for="old_password" class="col-md-3 col-form-label text-md-right">
                        {{ __('Old Password') }}
                    </label>

                    <div class="col-md-9">
                        <input class="form-control{{ $errors->has('old_password') ? ' has-error is-invalid' : '' }}" type="password" id="old_password" name="old_password" placeholder="Old Password" required />

                        @if ($errors->has('old_password'))
                            <div class="invalid-feedback d-block">
                                <strong>{{ $errors->first('old_password') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password" class="col-md-3 col-form-label text-md-right">
                        {{ __('New Password') }}
                    </label>

                    <div class="col-md-9">
                        <input class="form-control{{ $errors->has('password') ? ' has-error is-invalid' : '' }}" type="password" id="password" name="password" placeholder="New Password" required />

                        @if ($errors->has('password'))
                            <div class="invalid-feedback d-block">
                                <strong>{{ $errors->first('password') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password_confirmation" class="col-md-3 col-form-label text-md-right">
                        {{ __('Confirm Password') }}
                    </label>

                    <div class="col-md-9">
                        <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required />
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-9 offset-md-3">

                        <button class="btn btn-warning float-right" type="submit">Edit Info</button>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
