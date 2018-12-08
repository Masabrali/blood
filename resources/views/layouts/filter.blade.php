<div class="container mb-3">
    <?php
        $filter = (new \App\Http\Controllers\FilterController())->getFilter(Auth::id());

        if (empty($filter)) $filter = null;
    ?>
    <form method="POST" action="{{ route('filter') }}">

        {{ csrf_field() }}

        <div class="row">
            <div class="col-md-7">
                <?php $filter_form = true; ?>
                @include('layouts.location_select')
            </div>

            <div class="col-md-2">
                <label class="control-label label" for="from">From:</label>
                <input class="form-control datetimepicker" type="date" id="from" name="from"
                    @if (!empty($filter) && isset($filter->from))
                        value="{{ $filter->from }}"
                    @else
                        value="{{ (old('from'))? old('from'):date('Y-m-01') }}"
                    @endif
                />
            </div>
            <div class="col-md-2">
                <label class="control-label label" for="to">To:</label>
                <input class="form-control datetimepicker" type="date" id="to" name="to"
                    @if (!empty($filter) && isset($filter->to))
                        value="{{ $filter->to }}"
                    @else
                        value="{{ (old('to'))? old('to'):date('Y-m-t') }}"
                    @endif
                />
            </div>
            <div class="col-md-1">
                <a class="link d-block mb-1 mt-1" href="{{ route('resetFilter') }}">Reset</a>
                <button class="btn btn-primary" type="submit">Filter</button>
            </div>
        </div>
    </form>
</div>
