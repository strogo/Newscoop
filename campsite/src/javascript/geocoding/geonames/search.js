// reading a requested cookie
var geo_names = {};

geo_names.display_strings = {
    cc: "cc",
    city: "city",
    no_city_was_found: "sorry, no city was found",
};

geo_names.set_display_strings = function(local_strings)
{
    if (!local_strings) {return;}

    var display_string_names = [
        "cc",
        "city",
        "no_city_was_found",
    ];

    var str_count = display_string_names.length;
    for (var sind = 0; sind < str_count; sind++)
    {
        var cur_str_name = display_string_names[sind];

        if (undefined !== local_strings[cur_str_name])
        {
            this.display_strings[cur_str_name] = local_strings[cur_str_name];
        }
    }

};

// initializes the ajax query on position search
geo_names.askForNearCities = function(longitude, latitude, script_dir, results_div)
{
    callServer(['Geo_Names', 'FindCitiesByPosition'], [
        longitude,
        latitude,
        ], function(json) {
            geo_names.gotSearchData(json, results_div);
        });
};

// initializes the ajax query on city search
geo_names.askForCityLocation = function(city_name, country_code, script_dir, results_div)
{
    if (undefined === country_code) {
       country_code = "";
    }

    callServer(['Geo_Names', 'FindCitiesByName'], [
        city_name,
        country_code,
        ], function(json) {
            geo_names.gotSearchData(json, results_div);
        });
}

// the main action on ajax data retrieval for cities search
geo_names.gotSearchData = function (cities, results_div)
{
    found_locs = '<table class="geonames_result_table">';
    found_locs += '<thead><tr>';
    found_locs += '<th width="40">' + this.display_strings.cc + '</th>'
    found_locs += '<th width="120">' + this.display_strings.city + '</th>';
    found_locs += '</tr></thead>';
    
    found_locs += '<tbody>';
    var output_len = cities.length;
    for (var lind = 0; lind < output_len; lind++)
    {
        var one_city = cities[lind];
        
        var pop_show_ini = "" + one_city.population;
        var str_rest_len = pop_show_ini.length % 3;
        var pop_show = "";
        if (0 < str_rest_len)
        {
            pop_show = pop_show_ini.substr(0, str_rest_len) + " ";
        }
        var str_parts_count = (pop_show_ini.length - str_rest_len) / 3;
        if (0 < pop_show_ini.length)
        {
            while (true)
            {
                pop_show += pop_show_ini.substr(str_rest_len, 3) + " ";
                str_rest_len += 3;
          
                str_parts_count -= 1;
                if (0 >= str_parts_count)
                {
                    break;
                }
            }
        }
        else
        {
            pop_show = "0";
        }
        
        var country_name = "";
        try
        {
            country_name = country_codes_alpha_2_countries[one_city.country.toUpperCase()];
        }
        catch (e)
        {
            country_name = "";
        }
        var city_name = one_city.name.replace(/'/gi,"\\'");
        var country_link = "<a href=\"#\" class=\"map-no-link\" title=\"" + country_name + "\" onClick=\"geo_locations.center_lonlat('" + one_city.longitude + "', '" + one_city.latitude + "'); return false;\">" + one_city.country.toLowerCase() + "</a>";
        var city_link = "<a href=\"#\" title=\"" + pop_show + "\" onClick=\"geo_locations.center_lonlat('" + one_city.longitude + "', '" + one_city.latitude + "'); geo_locations.insert_poi('EPSG:4326', null, '" + one_city.longitude + "', '" + one_city.latitude + "', '" + city_name + "'); return false;\">" + one_city.name + "</a>";
        
        found_locs += "<tr>";
        found_locs += "<td>" + country_link + "</td>";
        
        found_locs += "<td>" + city_link + "</td>";
        found_locs += "</tr>";
    
    }
    
    found_locs += '</tbody>';
    found_locs += "</table>";
    
    var display_obj = document.getElementById ? document.getElementById(results_div) : null;
    display_obj.innerHTML = found_locs;
    
    showLocation();
    
    var use_class = "search_results";
    var rem_class = "search_results_limit";
    var flexi_height = 'auto';
    if (5 < output_len)
    {
        use_class = "search_results_limit";
        rem_class = "search_results";
        flexi_height = 160;
    }
    
    var removal = new RegExp("(^|\\s)" + rem_class + "(\\s" + rem_class + ")*" + "(\\s|$)", "g");
    var new_className = display_obj.className.replace(removal, " ");
    new_className += " " + use_class;
    new_className = new_className.replace(/\s\s+/g, " ");
    display_obj.className = new_className;
    
    //$('.geonames_result_table').flexigrid({height: flexi_height, resizable: false});
    //$('.geonames_result_table').dataTable({'sScrollY': flexi_height, 'bScrollCollapse': true, 'sDom': 't', "iDisplayLength": 100, "bJQueryUI": true, "aoColumnDefs": [{ "bSortable": false, "aTargets": [ '_all' ] }], "aaSorting": [], "oLanguage": {'sEmptyTable': "<div class=\"no_city_found\">sorry, no city was found</div>"}});
    $('.geonames_result_table').dataTable({'sScrollY': flexi_height, 'bScrollCollapse': true, 'sDom': 't', "iDisplayLength": 100, "bJQueryUI": true, "aoColumnDefs": [{ "bSortable": false, "aTargets": [ '_all' ] }], "aaSorting": [], "oLanguage": {'sEmptyTable': "<div class=\"no_city_found\">" + this.display_strings.no_city_was_found + "</div>"}});
    geo_locations.map_update_side_desc_height();
    
    return false;
};

