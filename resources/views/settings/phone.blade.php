<div class="tab-pane active" id="aggregate">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <form method="POST" action="/settings/phone/edit">
                @csrf
                <br />

                @include('layouts.status_alert')

                @if (!empty($user->phone))
                    <h5>Your current Phone number is <strong>{{ $user->phone }}</strong>. Enter a new Phone number below:</h5>

                    <br />
                @endif

                <div class="form-group row">
                    <label for="phone" class="col-md-2 col-form-label text-md-right">
                        {{ __('Phone') }}
                    </label>

                    <div class="col-md-9">
                        <input class="form-control{{ $errors->has('phone') ? ' has-error is-invalid' : '' }}" type="text" id="phone" name="phone" placeholder="Phone" value="{{ old('phone') }}" required />

                        @if ($errors->has('phone'))
                            <div class="invalid-feedback d-block">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-9 offset-md-2">

                        <button class="btn btn-warning float-right" type="submit">Change Phone</button>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
