<div class="row">
    <?php

        use App\Http\Controllers\FilterController;
        use App\Http\Controllers\RestrictionController;

        $filter = (new FilterController())->getFilter(Auth::id());
        $restriction = (new RestrictionController())->getRestriction(Auth::id());

        if (!isset($names)) $names = [];

        if (!isset($names['zone'])) $names['zone'] = 'zone';

        if (!isset($names['region'])) $names['region'] = 'region';

        if (!isset($names['district'])) $names['district'] = 'district';

        if (!isset($names['center'])) $names['center'] = 'center';

        $names = (Object) $names;

    ?>
    <div
        @if (isset($block_center)) class="col-md-4"
        @elseif (isset($block_district)) class="col-md-6"
        @elseif (isset($block_region)) class="col-md-12"
        @else class="col-md-2"
        @endif
    >
        <label class="control-label label" for="{{ $names->zone }}">Zone:</label>
        <select class="form-control selectpicker zone-select{{ $errors->has($names->zone) ? ' has-error is-invalid' : '' }}" data-cascade="#{{ $names->region }}, #{{ $names->district }}, #{{ $names->center }}" data-style="select-with-transition" name="{{ $names->zone }}" id="{{ $names->zone }}" {{ (isset($view) || isset($delete) || isset($activate) || isset($deactivate))? 'readonly':'' }} {{ (isset($location_required))? 'required':'' }}>
            <?php

                if (!empty(old($names->zone))) $_zone = old($names->zone);
                else if (isset($data[$names->zone]) && !empty($data[$names->zone]))
                    $_zone = $data[$names->zone];
                else if (isset($filter_form) && isset($filter->zone) && !empty($filter->zone))
                    $_zone = $filter->zone;
                else if (isset($restriction->zone) && !empty($restriction->zone))
                    $_zone = $restriction->zone;
                else $_zone = NULL;

                $zones = \App\Zone::orderBy('name', 'ASC');

                if (isset($restriction->zone))
                    $zones = $zones->where('id', $restriction->zone);

                $zones = $zones->get();

            ?>

            @if (!isset($restriction->zone))
                <option value>Zone</option>
            @endif

            @foreach ($zones as $zone)
                <option value="{{ $zone->id }}" {{ ($zone->id == $_zone)? 'selected' : '' }}>
                    {{ $zone->name }}
                </option>
            @endforeach
        </select>
    </div>

    @if (!isset($block_region))

        <div
            @if (isset($block_center)) class="col-md-4"
            @elseif (isset($block_district)) class="col-md-6"
            @else class="col-md-3"
            @endif
        >
            <label class="control-label label" for="{{ $names->region }}">Region:</label>
            <select class="form-control selectpicker region-select{{ $errors->has($names->region) ? ' has-error is-invalid' : '' }}" data-cascade="#{{ $names->district }}, #{{ $names->center }}" data-style="select-with-transition" name="{{ $names->region }}" id="{{ $names->region }}" {{ (isset($_zone))? "data-zone=$_zone":'' }} {{ (isset($view) || isset($delete) || isset($activate) || isset($deactivate))? 'readonly':'' }} {{ (isset($location_required))? 'required':'' }}>
                <?php

                    if (!empty(old($names->region))) $_region = old($names->region);
                    else if (isset($data[$names->region]) && !empty($data[$names->region]))
                        $_region = $data[$names->region];
                    else if (isset($filter_form) && isset($filter->region) && !empty($filter->region))
                        $_region = $filter->region;
                    else if (isset($restriction->region) && !empty($restriction->region))
                        $_region = $restriction->region;
                    else $_region = NULL;

                    $regions = \App\Region::where('zone', $_zone)->orderBy('name', 'ASC');

                    if (!empty($_zone)) {

                        if (isset($restriction->zone))
                            $regions = $regions->where('zone', $restriction->zone);

                        if (isset($restriction->region))
                            $regions = $regions->where('id', $restriction->region);
                    }

                    $regions = $regions->get();

                ?>

                @if (!isset($restriction->region))
                    <option value>Region</option>
                @endif

                @foreach ($regions as $region)
                    <option value="{{ $region->id }}" {{ ($region->id == $_region)? 'selected' : '' }}>
                        {{ $region->name }}
                    </option>
                @endforeach
            </select>
        </div>

        @if (!isset($block_district))

            <div class="{{ (isset($block_center))? 'col-md-4':'col-md-3' }}">
                <label class="control-label label" for="{{ $names->district }}">District:</label>
                <select class="form-control selectpicker district-select{{ $errors->has($names->district) ? ' has-error is-invalid' : '' }}" data-cascade="#{{ $names->center }}" data-style="select-with-transition" name="{{ $names->district }}" id="{{ $names->district }}" {{ (isset($_zone))? "data-zone=$_zone":'' }} {{ (isset($view) || isset($delete) || isset($activate) || isset($deactivate))? 'readonly':'' }} {{ (isset($_region))? "data-region=$_region":'' }} {{ (isset($location_required))? 'required':'' }}>

                    <?php

                        if (!empty(old($names->district))) $_district = old($names->district);
                        else if (isset($data[$names->district]) && !empty($data[$names->district]))
                            $_district = $data[$names->district];
                        else if (isset($filter_form) && isset($filter->district) && !empty($filter->district))
                            $_district = $filter->district;
                        else if (isset($restriction->district) && !empty($restriction->district))
                            $_district = $restriction->district;
                        else $_district = NULL;

                        $districts = \App\District::where('region', $_region)->orderBy('name', 'ASC');

                        if (!empty($_region)) {

                            if (isset($restriction->region))
                                $districts = $districts->where('region', $restriction->region);

                            if (isset($restriction->district))
                                $districts = $districts->where('id', $restriction->district);
                        }

                        $districts = $districts->get();

                    ?>

                    @if (!isset($restriction->district))
                        <option value>District</option>
                    @endif

                    @foreach ($districts as $district)
                        <option value="{{ $district->id }}" {{ ($district->id == $_district)? 'selected' : '' }}>
                            {{ $district->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if (!isset($block_center))

                <div class="col-md-4">
                    <label class="control-label label" for="{{ $names->center }}">Center:</label>
                    <select class="form-control selectpicker{{ $errors->has($names->center) ? ' has-error is-invalid' : '' }}" data-style="select-with-transition" name="{{ $names->center }}" id="{{ $names->center }}" {{ (isset($_zone))? "data-zone=$_zone":'' }} {{ (isset($_region))? "data-region=$_region":'' }} {{ (isset($_district))? "data-district=$_district":'' }} {{ (isset($view) || isset($delete) || isset($activate) || isset($deactivate))? 'readonly':'' }} {{ (isset($location_required))? 'required':'' }}>
                        <?php

                            if (!empty(old($names->center))) $_center = old($names->center);
                            else if (isset($data[$names->center]) && !empty($data[$names->center]))
                                $_center = $data[$names->center];
                            else if (isset($filter_form) && isset($filter->center) && !empty($filter->center))
                                $_center = $filter->center;
                            else if (isset($restriction->center) && !empty($restriction->center))
                                $_center = $restriction->center;
                            else $_center = NULL;

                            $centers = \App\Center::where('district', $_district)->orderBy('name', 'ASC');

                            if (!empty($_district)) {

                                if (isset($restriction->district))
                                    $centers = $centers->where('district', $restriction->district);

                                if (isset($restriction->center))
                                    $centers = $centers->where('id', $restriction->center);

                            }

                            $centers = $centers->get();

                        ?>

                        @if (!isset($restriction->center))
                            <option value>Center</option>
                        @endif

                        @foreach ($centers as $center)
                            <option value="{{ $center->id }}" {{ ($center->id == $_center)? 'selected' : '' }}>
                                {{ $center->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        @endif
    @endif
</div>
