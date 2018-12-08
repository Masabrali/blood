<script type="text/javascript">

(function ($) {

    var _Locations;

    try {
        var _Locations = JSON.parse( <?php echo("'".(\App\Location::find(1)->first()->locations)."'"); ?> );

        var _Restrictions = JSON.parse( <?php echo("'".json_encode(\App\Restriction::where('user', Auth::id())->first())."'"); ?> );
    } catch (ex) {
        console.error(ex);
    }
    const Locations = _Locations;
    _Locations = undefined;

    const Restrictions = _Restrictions;
    _Restrictions = undefined;

    const toArray = function (obj) {
        return Object.keys(obj).map(function (key) { return obj[key]; });
    }

    const sort = function (property) {

        var sortOrder = 1;

        if(property[0] === "-") {
            sortOrder = -1;
            property = property.substr(1);
        }

        return function (a,b) {
            var result = (a[property] < b[property]) ? -1 : (a[property] > b[property]) ? 1 : 0;
            return result * sortOrder;
        }
    }

    /**
    * Zone Cascade
    */
    $(document).delegate("select.zone-select[data-cascade]", "change", function (e) {

        var $_this = $(this);
        var $_cascade = $($_this.data('cascade')); // console.dir($_cascade);
        var $_region = $($_cascade[0]);
        var $_district = $($_cascade[1]);
        var $_center = $($_cascade[2]);
        var zone = $_this.val();
        var regions, _regions;

        $_cascade.data("zone", zone).val('');

        regions = "<option value>Region</option>";
        if (zone) {

            if (Restrictions != undefined && Restrictions.region != undefined)
                _regions = [ Locations[zone]['regions'][Restrictions.region] ];
            else _regions = toArray(Locations[zone]['regions']).sort(sort('name'));

            $.each(_regions, function (index, region) {
                regions += '<option value=' + region['id'] + '>' + region['name'] + '</option>';
            });
        }
        $_region[0].innerHTML = regions;
        // $_district.selectpicker('refresh');

        $_district[0].innerHTML = "<option value>District</option>";
        // $_ward.selectpicker('refresh');

        $_center[0].innerHTML = "<option value>Center</option>";
        // $_street.selectpicker('refresh');

    });

    /**
    * Region Cascade
    */
    $(document).delegate("select.region-select[data-cascade]", "change", function (e) {

        var $_this = $(this);
        var $_cascade = $($_this.data('cascade')); // console.dir($_cascade);
        var $_district = $($_cascade[0]);
        var $_center = $($_cascade[1]);
        var zone = $_this.data('zone');
        var region = $_this.val();
        var districts, _districts;

        $_cascade.data("region", region).val('');

        districts = "<option value>District</option>";
        if (zone && region) {

            if (Restrictions != undefined && Restrictions.district != undefined)
                _districts = [ Locations[zone]['regions'][region]['districts'][Restrictions.district] ];
            else _districts = toArray(Locations[zone]['regions'][region]['districts']).sort(sort('name'));

            $.each(_districts, function (index, district) {
                districts += '<option value=' + district['id'] + '>' + district['name'] + '</option>';
            });
        }
        $_district[0].innerHTML = districts;
        // $_district.selectpicker('refresh');

        $_center[0].innerHTML = "<option value>Center</option>";
        // $_street.selectpicker('refresh');

    });

    /**
    * District Cascade
    */
    $(document).delegate("select.district-select[data-cascade]", "change", function (e) {

        var $_this = $(this);
        var $_cascade = $($_this.data('cascade'));
        var $_center = $($_cascade[0]);
        var zone = $_this.data('zone');
        var region = $_this.data('region');
        var district = $_this.val();
        var centers, _centers;

        $_cascade.data("district", district).val('');

        centers = "<option value>Center</option>";
        if (zone && region && district) {

            if (Restrictions != undefined && Restrictions.center != undefined)
                _centers = [ Locations[zone]['regions'][region]['districts'][district]['centers'][Restrictions.center] ];
            else _centers = toArray(Locations[zone]['regions'][region]['districts'][district]['centers']).sort(sort('name'));

            $.each(_centers, function (index, ward) {
                centers += '<option value=' + ward['id'] + '>' + ward['name'] + '</option>';
            });
        }
        $_center[0].innerHTML = centers;
        // $_ward.selectpicker('refresh');
    });

}($ = $ || window.$));

</script>
