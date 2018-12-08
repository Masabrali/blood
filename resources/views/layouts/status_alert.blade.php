@if (session('success') || !$errors->isEmpty())
  <div class="form-group row">
      <div class="col-md-10 offset-md-1">
          <div class="alert
          @if (!$errors->isEmpty()) alert-danger
          @elseif (isset($activate) || session('verified')) alert-success
          @elseif (isset($edit) || isset($settings)) alert-warning
          @elseif (isset($delete) || isset($deactivate)) alert-danger
          @else alert-success
          @endif
          alert-dismissible fade show py-2" role="alert">
              <p class="my-1">
                  @if (!$errors->isEmpty())
                      A few errors have been detected
                  @else
                      {{ rtrim($title, 's') }}
                      @if (isset($edit)) Edited
                      @elseif (isset($delete)) Deleted
                        @elseif (isset($activate)) Activated
                      @elseif (isset($deactivate)) Deactivated
                      @elseif (session('verified')) Verified
                      @elseif (isset($settings)) Changed
                      @else Added
                      @endif
                      Successfully
                  @endif
              </p>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
              </button>
          </div>
      </div>
  </div>
@endif
