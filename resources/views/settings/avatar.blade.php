<div class="tab-pane active" id="aggregate">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <form method="POST" action="/settings/avatar/edit" enctype="multipart/form-data">
                @csrf

                <br />

                @include('layouts.status_alert')

                <div class="row">
                    <?php
                        $settings_controller = new \App\Http\Controllers\SettingsController();
                    ?>

                    @foreach ($settings_controller->getAvatars($user->id) as $_avatar => $avatar)

                        <div class="col-md-3">
                            <label class="d-block" for="_avatar_{{ $_avatar }}">
                                <div class="avatar" style="overflow: hidden; width: 100%; height: 100%;">
                                    <img src="{{ Storage::url($avatar->url) }}" style="min-height: 100%; width: 100%; height: auto;" />
                                </div>
                                <br/>
                                <h6>
                                    <input type="radio" id="_avatar_{{ $_avatar }}" name="_avatar" value="{{ $avatar->id }}" {{ ($avatar->url == $user->avatar)? 'checked':'' }} />
                                    &nbsp;{{ $avatar->name }}
                                </h6>
                            </labe>
                        </div>

                    @endforeach
                </div>
                <br />
                <div class="form-group row">
                    <label for="avatar" class="col-md-2 col-form-label text-md-right">
                        {{ __('New Avatar') }}
                    </label>

                    <div class="col-md-9">
                        <input class="form-control{{ $errors->has('avatar') ? ' has-error is-invalid' : '' }}" type="file" id="avatar" name="avatar" placeholder="Choose Avatar" value="{{ old('avatar') }}" accept=".jpeg,.jpg,.png" />

                        @if ($errors->has('avatar'))
                            <div class="invalid-feedback d-block">
                                <strong>{{ $errors->first('avatar') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-md-9 offset-md-2">

                        <button class="btn btn-warning float-right" type="submit">Change Avatar</button>

                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
