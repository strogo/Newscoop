//alert(0);

var selectControl = null
//var map_click = null;

var mpan = null;

//var OpenLayers.Control.Hover = null;
//var OpenLayers.Control.Click = null;

var cur_pop_rank = 0;
var all_pop_objs = [];

var map = null;
var layer = null;
var texts = null;

var poi_markers = [];
//var circles = null;
//var markers = null;

var map_shown = false;
var map_obj = null
var markersArray = [];
var poi_rank_out = -1;

var descs_elm = null;
var descs_inner = "";
var descs_count = 0;

var ignore_click = false;

//var selectControl = null;
var popup = null;

var center_poi = function(index)
{
    var mlon = poi_markers[index].map_lon;
    var mlat = poi_markers[index].map_lat;
    var lonLat = new OpenLayers.LonLat(mlon, mlat);

    map.setCenter (lonLat);

}

var remove_poi = function(index)
{
    //alert(layer.features);
    //return;
    var real_index = -1;
    //alert(real_index);
    //var poi_markers_length = poi_markers.length;
    for (var rind = 0; rind <= index; rind++)
    {
        //alert(rind);
        //alert(poi_markers[rind]['usage']);
        if (poi_markers[rind]['usage']) {real_index += 1;}
    }
    //alert(real_index);

    var to_remove = [];
    to_remove.push(layer.features[real_index])
    layer.removeFeatures(to_remove);

    poi_markers[index].usage = false;

    update_poi_descs();
};

var update_poi_descs = function()
{
    //alert(poi_markers);
    max_ind = poi_markers.length - 1;

    descs_inner = "";
    var disp_index = 1;
    //alert("poi_rank_out: " + poi_rank_out);
    for(var pind = 0; pind <= max_ind; pind++)
    {
        //alert("pind: " + pind);
        var cur_poi = poi_markers[pind];
        if (!cur_poi.usage) {continue;}

        use_class = "poi_std";
        if (poi_rank_out == (pind + 0))
        {
            use_class = "poi_out";
        }
        //alert(use_class);
        descs_inner += "<tr class=\"" + use_class + "\"><td><b>" + disp_index + "</b>&nbsp;";
        descs_inner += " <i><a href='#' onclick='center_poi(" + pind + ");return false;'>(c)</a></i>&nbsp;";
        descs_inner += "<i><a href='#' onclick='remove_poi(" + pind + ");return false;'>(x)</a></i><br />";
        descs_inner += "lon: " + cur_poi.lon.toFixed(6) + "<br />lat: " + cur_poi.lat.toFixed(6) + "</td></tr>";
        //alert(descs_inner);
        disp_index += 1;
    }
    descs_elm.innerHTML = "<table>" + descs_inner + "</table>";
};

var poi_dragged = function(feature, pixel)
{
    //alert(feature.geometry);
    //alert(feature);
    //alert(feature.m_rank);

    var index = feature.attributes.m_rank;
    var cur_poi_info = poi_markers[index];

    //var vector = feature.clone();
    var lonlat = map.getLonLatFromViewPortPx(pixel);
    cur_poi_info['map_lon'] = lonlat.lon;
    cur_poi_info['map_lat'] = lonlat.lat;

    lonlat.transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
    //alert(lonlat);

    //alert(feature.m_rank);
    //var cur_poi_info = poi_markers[feature.attributes.m_rank];
    cur_poi_info['lon'] = lonlat.lon;
    cur_poi_info['lat'] = lonlat.lat;
    //cur_poi_info['map_lon'] = lonlat.lon;
    //cur_poi_info['map_lat'] = lonlat.lat;

    update_poi_descs();

    // do we want the centering?
    //center_poi(index);


    if (popup) {
        popup.moveTo(pixel);
    }
};


var trigger_on_map_click = function(e)
{
    //map_click.activate();
    //alert(e);
    //return;

    if (ignore_click) {
        ignore_click = false;
        return;
    }

    // setting the new poi on clicking
    var lonlat = map.getLonLatFromViewPortPx(e.xy);

    // making poi for features
    var features = [];
    //var features2 = [];
    var point = new OpenLayers.Geometry.Point(lonlat.lon, lonlat.lat);
    var vector = new OpenLayers.Feature.Vector(point, {title: "bah"});
    poi_rank_out = descs_count;
    vector.attributes.m_rank = descs_count;
    vector.attributes.m_title = "title - " + descs_count;
    vector.attributes.m_link = "";
    vector.attributes.m_description = "nothing here";
    vector.attributes.m_image = "";
    vector.attributes.m_embed = "";
    features.push(vector);
    //features2.push(vector.clone());

    // setting feature-based classical-shaped marker
    layer.addFeatures(features);

/*
    // setting the feature-based circle-shaped marker
    circles.addFeatures(features2);

    // setting a classical marker
    markers.addMarker(new OpenLayers.Marker(lonlat.clone()));
*/

    var map_lon = lonlat.lon;
    var map_lat = lonlat.lat;

    lonlat.transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
    //alert(lonlat);

    //lonlat.m_rank = descs_count;
    //lonlat.m_usage = true;
    poi_markers.push({'lon':lonlat.lon, 'lat':lonlat.lat, 'usage':true, 'map_lon': map_lon, 'map_lat': map_lat});

    update_poi_descs();

    descs_count += 1;
    //descs_inner += "<tr><td><b>" + descs_count + "</b><br />lon: " + lonlat.lon.toFixed(6) + "<br />lat: " + lonlat.lat.toFixed(6) + "</td></tr>";
    //descs_elm.innerHTML = "<table>" + descs_inner + "</table>";


/* */
/*
    layer.events.on({
        //'click': onFeatureClick,
        'featureselected': onFeatureSelect,
        'featureunselected': onFeatureUnselect
    });
*/
    selectControl.destroy();
    selectControl = new OpenLayers.Control.SelectFeature(layer);
    map.addControl(selectControl);
    selectControl.activate();

/* */

};

var openlayers_init = function(map_div_name, markers)
{
    var marker_main = markers['main'];
    //alert(map_div_name + ", " + marker_main);

    OpenLayers.Control.Hover = OpenLayers.Class(OpenLayers.Control, {
        defaultHandlerOptions: {
            //'delay': 100,
            //'pixelTolerance': 10,
            'delay': 200,
            'pixelTolerance': 2,
        },
        initialize: function(options) {
            this.handlerOptions = OpenLayers.Util.extend(
                {}, this.defaultHandlerOptions
            );
            OpenLayers.Control.prototype.initialize.apply(
                this, arguments
            );
            this.handler = new OpenLayers.Handler.Hover(
                this, {
                    'pause': this.trigger
                }, this.handlerOptions
            );
        },
        trigger: function(evt) {
            poi_hover = layer.getFeatureFromEvent(evt);
            if (poi_hover) {
                //alert('bah: ' + evt + ", " + oFeature);
                //alert(poi_hover.attributes);
                if (null !== poi_hover.attributes.m_rank) {
                    //alert("POI: " + poi_hover.attributes.m_rank);
                    poi_rank_out = poi_hover.attributes.m_rank;
                    update_poi_descs();
                }
            }
        }
    });

    OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {
        defaultHandlerOptions: {
            'single': true,
            'double': false,
            'pixelTolerance': 0,
            'stopSingle': false,
            'stopDouble': false,
            'projection': new OpenLayers.Projection("EPSG:900913"),
            'displayProjection': new OpenLayers.Projection("EPSG:900913"),
            //'displayProjection': new OpenLayers.Projection("EPSG:4326"),
        },

        initialize: function(options) {
            this.handlerOptions = OpenLayers.Util.extend(
                {}, this.defaultHandlerOptions
            );
            OpenLayers.Control.prototype.initialize.apply(
                this, arguments
            );
            this.handler = new OpenLayers.Handler.Click(
                this, {
                    //'click': this.trigger
                    'click': trigger_on_map_click
                }, this.handlerOptions
            );
        }, 

        //trigger:  trigger_on_map_click
/*
        trigger: function(e) {
            return;

            if (ignore_click) {
                ignore_click = false;
                return;
            }

            // setting the new poi on clicking
            var lonlat = map.getLonLatFromViewPortPx(e.xy);

            // making poi for features
            var features = [];
            //var features2 = [];
            var point = new OpenLayers.Geometry.Point(lonlat.lon, lonlat.lat);
            var vector = new OpenLayers.Feature.Vector(point, {title: "bah"});
            poi_rank_out = descs_count;
            vector.attributes.m_rank = descs_count;
            vector.attributes.m_title = "title - " + descs_count;
            vector.attributes.m_link = "";
            vector.attributes.m_description = "nothing here";
            vector.attributes.m_embed = "";
            features.push(vector);
            //features2.push(vector.clone());

            // setting feature-based classical-shaped marker
            layer.addFeatures(features);

/ ***
            // setting the feature-based circle-shaped marker
            circles.addFeatures(features2);

            // setting a classical marker
            markers.addMarker(new OpenLayers.Marker(lonlat.clone()));
*** /

            lonlat.transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
            //alert(lonlat);

            //lonlat.m_rank = descs_count;
            //lonlat.m_usage = true;
            poi_markers.push({'lon':lonlat.lon, 'lat':lonlat.lat, 'usage':true});

            update_poi_descs();

            descs_count += 1;
            //descs_inner += "<tr><td><b>" + descs_count + "</b><br />lon: " + lonlat.lon.toFixed(6) + "<br />lat: " + lonlat.lat.toFixed(6) + "</td></tr>";
            //descs_elm.innerHTML = "<table>" + descs_inner + "</table>";


            layer.events.on({
                //'click': onFeatureClick,
                'featureselected': onFeatureSelect,
                'featureunselected': onFeatureUnselect
            });

        }
*/

    });

    // making the map
    map = new OpenLayers.Map(map_div_name);
    // google map v3
    var map_gsm = new OpenLayers.Layer.Google(
        "Google Streets", // the default
        {numZoomLevels: 20, 'sphericalMercator': true}
    );
    // other google views
/*
    var map_phy = new OpenLayers.Layer.Google(
        "Google Physical",
        {type: G_PHYSICAL_MAP, numZoomLevels: 20}
    );
    var map_hyb = new OpenLayers.Layer.Google(
        "Google Hybrid",
        {type: G_HYBRID_MAP, numZoomLevels: 20}
    );
    var map_sat = new OpenLayers.Layer.Google(
        "Google Satellite",
        {type: G_SATELLITE_MAP, numZoomLevels: 20, 'sphericalMercator': true}
    );
*/

    // openstreetmap
    var map_osm = new OpenLayers.Layer.OSM();

    // some other map providers

/*
    var map_vem = null;
    try {
        map_vem = new OpenLayers.Layer.VirtualEarth("VE", {animationEnabled: false});
    }
    catch (e) {
        alert(e);
        map_vem = null;
    }
*/

/*
    var map_vem = new OpenLayers.Layer.VirtualEarth("VE", {
        // turn off animated zooming
        animationEnabled: false,
        //minZoomLevel: 4,
        //maxZoomLevel: 6,
        //'type': VEMapStyle.Aerial
        minZoomLevel: 2,
        maxZoomLevel: 20,
    });
*/

    //var map_yam = new OpenLayers.Layer.Yahoo("Yahoo");

    // mapy.cz
    //var map_sez = new OpenLayers.Layer.Seznam("Seznam");
    // setting the map layers
    //map.addLayers([map_gsm, map_osm, map_sez]);
    map.addLayers([map_gsm, map_osm]);
    //map.addLayers([map_gsm, map_phy, map_hyb, map_sat, map_osm]);
/*
    //the other providers work somehow bad with openlayers
    if (map_vem) {
        map.addLayer(map_vem);
    }
    if (map_yam) {
        map.addLayer(map_yam);
    }
*/
    //map.addLayers([map_gsm, map_osm, map_vem, map_yam]);
    //map.addLayers([map_gsm, map_osm, map_vem]);
    // for switching between maps
    map.addControl(new OpenLayers.Control.LayerSwitcher());

    // two initial points for center and features/markers
    var lonLat_ini = {'lon': 14.424133, 'lat': 50.089926}
    var lonLat = new OpenLayers.LonLat(lonLat_ini.lon, lonLat_ini.lat)
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            map.getProjectionObject() // to Spherical Mercator Projection
          );
    var lonLat2_ini = {'lon': 15.783691, 'lat': 50.037031}
    var lonLat2 = new OpenLayers.LonLat(lonLat2_ini.lon, lonLat2_ini.lat)
          .transform(
            new OpenLayers.Projection("EPSG:4326"), // transform from WGS 1984
            map.getProjectionObject() // to Spherical Mercator Projection
          );

    //var zoom=16;
    //var zoom=8;
    //var zoom=6;
    //var zoom=4;
    var zoom=8; // the 4 should be (probably) the default

    // layer for features
    layer = new OpenLayers.Layer.Vector(
        "POI markers",
        {
            styleMap: new OpenLayers.StyleMap({
                // Set the external graphic and background graphic images.
                // classical looking markers are displaced
                externalGraphic: marker_main,
                //externalGraphic: "http://www.openlayers.org/api/img/marker-gold.png",
                pointRadius: 10,
                graphicYOffset: -20,
                graphicXOffset: -10,
                graphicZIndex: 10,
            }),
            isBaseLayer: false,
            rendererOptions: {yOrdering: true}
        }
    );
    map.addLayer(layer);

/*
    texts = new OpenLayers.Layer.Vector(
        "POI texts",
        {
            styleMap: new OpenLayers.StyleMap({
                // Set the external graphic and background graphic images.
                // classical looking markers are displaced
               // externalGraphic: marker_main,
                //externalGraphic: "http://www.openlayers.org/api/img/marker-gold.png",
                //pointRadius: 10,
                //graphicYOffset: -20,
                //graphicXOffset: -10,
            }),
            isBaseLayer: false,
            rendererOptions: {yOrdering: true}
        }
    );
    map.addLayer(texts);
*/

/*
    // layer for symmetric-shaped features
    circles = new OpenLayers.Layer.Vector(
        "Cirlce Markers",
        {
            styleMap: new OpenLayers.StyleMap({
                // Set the external graphic and background graphic images.
                // the circle (i.e. symmetric figure) looks to work correctly
                externalGraphic: './img/red_circle_20.png',
                //externalGraphic: 'http://www.latimes.com/includes/projects/img/red_circle_20.png',
                pointRadius: 6
            }),
            isBaseLayer: false,
            rendererOptions: {yOrdering: true}
        }
    );
    map.addLayer(circles);
*/

    // setting the feature pois
    var features = [];
    //var features2 = [];
    var point = null;
    var vector = null;
    //var vector_t = null;
    point = new OpenLayers.Geometry.Point(lonLat.lon, lonLat.lat);
    vector = new OpenLayers.Feature.Vector(point, {title: "bah 1"});
    vector.attributes.m_rank = 0;
    vector.attributes.m_title = "Prague";
    vector.attributes.m_link = "www.sourcefabric.org";
    vector.attributes.m_description = "some text";
    //vector.attributes.m_embed = '<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/k5JkHBC5lDs"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/k5JkHBC5lDs" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object>';
    vector.attributes.m_embed = '<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/CodZlYEMgec"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/CodZlYEMgec" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object>';
    //vector.attributes.m_embed = "";
    vector.attributes.m_image = "";
    //vector.attributes.m_usage = true;
    features.push(vector);
    //features2.push(vector.clone());

    //vector_t = vector.clone();
    //vector_t.transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
    poi_markers.push({'lon': lonLat_ini.lon, 'lat': lonLat_ini.lat, 'usage':true, 'map_lon': lonLat.lon, 'map_lat': lonLat.lat});

    point = new OpenLayers.Geometry.Point(lonLat2.lon, lonLat2.lat);
    vector2 = new OpenLayers.Feature.Vector(point, {title: "bah 2"});
    vector2.attributes.m_rank = 1;
    vector2.attributes.m_title = "Pard";
    vector2.attributes.m_link = "http://campsite.sourcefabric.org/";
    vector2.attributes.m_description = "some another";
    vector2.attributes.m_embed = "";
    vector2.attributes.m_image = "http://www.tangloid.net/seminar/spin/tangloid-004.png";
    //vector.attributes.m_usage = true;
    features.push(vector2);
    //features2.push(vector.clone());

    //vector_t = vector.clone();
    //vector_t.transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
    poi_markers.push({'lon': lonLat2_ini.lon, 'lat': lonLat2_ini.lat, 'usage':true, 'map_lon': lonLat2.lon, 'map_lat': lonLat2.lat});
    descs_count = 2;

    update_poi_descs();

/*
    layer.events.on({
        //'click': onFeatureClick,
        'featureselected': onFeatureSelect,
        'featureunselected': onFeatureUnselect
    });
*/

    layer.addFeatures(features);
/*
    circles.addFeatures(features2);

    // layer for markers
    markers = new OpenLayers.Layer.Markers( "Markers" );
    map.addLayer(markers);
    markers.addMarker(new OpenLayers.Marker(lonLat));
    markers.addMarker(new OpenLayers.Marker(lonLat2));
*/

    // setting map center
    map.setCenter (lonLat, zoom);

    // registering for click events
    var click = new OpenLayers.Control.Click();
    map.addControl(click);
    click.activate();

    var hover = new OpenLayers.Control.Hover();
    map.addControl(hover);
    hover.activate();

    // registering for (classical) feature dragging
    var dragFeature = new OpenLayers.Control.DragFeature(layer);
    dragFeature.onComplete = poi_dragged;
    map.addControl(dragFeature);
    dragFeature.activate();

    selectControl = new OpenLayers.Control.SelectFeature(layer);
    map.addControl(selectControl);
    selectControl.activate();

    layer.events.on({
        //'click': onFeatureClick,
        'featureselected': onFeatureSelect,
        'featureunselected': onFeatureUnselect
    });
    //selectControl.deactivate();
    //selectControl.destroy();
    //selectControl = new OpenLayers.Control.SelectFeature(layer);
    //map.addControl(selectControl);
    selectControl.activate();

/*
    // registering for (circle) feature dragging
    // can not has it for two layers?
    var dragFeature2 = new OpenLayers.Control.DragFeature(circles);
    map.addControl(dragFeature2);
    //dragFeature2.activate();
*/

    /*
    // marker dragging does not work
    var dragMarkers = new OpenLayers.Control.DragMarker(markers);
    map.addControl(dragMarkers);
    dragMarkers.activate();
    */

    //remove_poi(1);

    mpan = new OpenLayers.Control.DragPan();
    mpan.panMapDone = function(xy)
    {
        selectControl.destroy();
        selectControl = new OpenLayers.Control.SelectFeature(layer);
        map.addControl(selectControl);
        selectControl.activate();

        //alert(xy);
    }
    map.addControl(mpan);
    mpan.activate();
    //alert("init end");

};

//var onFeatureClick = function(evt)
//{
//    var feature = evt.feature;
//    alert("click: " + feature.attributes.m_rank);
//}
            // Needed only for interaction, not for the display.
var onPopupClose = function(evt) {
/*
    popup.toggle();
    popup.hide();

    var cur_popup = document.getElementById ? document.getElementById(this.feature.popup.id) : null;
    if (cur_popup) {
        cur_popup.innerHTML = "";
        cur_popup.className = "map-hidden";
    }

    //return;


    //alert(222);
    // 'this' is the popup.
    ignore_click = true;
    //alert(222 + ": " + selectControl);
    //selectControl.unselect(this.feature);

    map.removePopup(popup);
    popup.hide();
    popup.destroy();

    if (this.feature.popup) {
        //alert(111 + ": " + popup);
        //alert(33311 + ": " + this.feature);
        //alert(33311 + "a: " + this.feature.popup);
        this.feature.popup.hide();
        try {
            texts.removePopup(popup);
        }
        catch (e) {alert("l asdf: " + e);}
        try {
            map.removePopup(popup);
        }
        catch (e) {alert("m asdf: " + e);}

        //alert(33311 + "b: " + this.feature.popup);
        map.removePopup(this.feature.popup);
        //alert(33312 + ": " + this.feature.popup);
        this.feature.popup.destroy();
        //alert(33313);
        this.feature.popup = null;
        //alert(333 + ": " + popup);
        popup.feature = null;
        //alert(333 + " end");
    }
*/

    ignore_click = true;
    selectControl.unselect(this.feature);

    //alert(222 + " end");
};

var onFeatureUnselect = function(evt)
{
    //alert(111);
    var feature = evt.feature;
    //alert(111 + ": " + feature);
    //alert("unselect: " + feature.attributes.m_rank);

    if (feature.popup) {
        //alert(111 + ": " + popup);
        popup.feature = null;
        //popup.hide();
        map.removePopup(feature.popup);
        feature.popup.destroy();
        feature.popup = null;
        //alert(111 + " end if");
    }

    //alert(111 + " end");

    // auxiliary 
    //map.setCenter(map.center);

};

var onFeatureSelect = function(evt)
{
    //alert("popup 001");
    //alert("wtf");
    //alert(0 + ": " + evt);
    var feature = evt.feature;
    //alert(1 + ": " + feature);

    //alert("select: " + feature.attributes.m_rank);
    var pop_link = feature.attributes.m_link;
    if (0 < pop_link.length) {
        var to_prepend = true;
        //alert('"' + pop_link.substr(0, 7) + '"');
        if ("http://" == pop_link.substr(0, 7)) {
            to_prepend = false;
        }
        if ("https://" == pop_link.substr(0, 8)) {
            to_prepend = false;
        }
        if ("ftp://" == pop_link.substr(0, 6)) {
            to_prepend = false;
        }
        if ("ftps://" == pop_link.substr(0, 7)) {
            to_prepend = false;
        }
        if (to_prepend) {pop_link = "http://" + pop_link;}
    }
    var pop_text = "";
    //alert("popup 007");
    //alert(pop_link);
    if (0 < pop_link.length) {
        pop_text += "<a href=\"" + pop_link + "\" target=\"_blank\">";
    }
    pop_text += "<h2>" + feature.attributes.m_title + "</h2>";
    if (0 < pop_link.length) {
        pop_text += "</a>";
    }

    var with_embed = false;
    if (feature.attributes.m_embed)
    {
        pop_text += "<br />" + feature.attributes.m_embed + "<br />";
        with_embed = true;
    }
    else
    {
        if (feature.attributes.m_image)
        {
            pop_text += "<br /><img src=\"" + feature.attributes.m_image + "\"><br />";
        }
    }

    pop_text += feature.attributes.m_description;
    cur_pop_rank += 1;
    popup = new OpenLayers.Popup.FramedCloud("featurePopup_" + cur_pop_rank,
        feature.geometry.getBounds().getCenterLonLat(),
        new OpenLayers.Size(100,100),
        pop_text,
        null, true, onPopupClose);

    //alert(popup.maxSize); // 1200x660
    if (with_embed) {
        popup.minSize = new OpenLayers.Size(425 + 100, 350 + 250);
    }
    //popup.closeOnMove = true;

    feature.popup = popup;
    popup.feature = feature;
    map.addPopup(popup);
    //alert("popup added");
    //texts.addPopup(popup);

/*
    for(var lind = 0; pind <= max_ind; pind++)
    {
        var descs_inner_link = document.getElementById ? document.getElementById("descs_link_" + pind) : null;
        if (descs_inner_link)
        {
            //id='descs_link_" + pind + "' 
            escs_inner_link.href = "";
        }
    }
*/

};

var selecting_locations = function (geocodingdir, div_name, descs_name, names_show, names_hide, editing)
{
    var map_canvas = document.getElementById ? document.getElementById(div_name) : null;
    //alert(descs_name);
    descs_elm = document.getElementById ? document.getElementById(descs_name) : null;

    var divs_show = [];
    var divs_hide = [];

    var show_obj = null;
    var hide_obj = null;

    if (names_show) {
        var divs_show_names = names_show.split(",");
        //alert(divs_showhide + ": " + divs_other_names)
        var len_show_names = divs_show_names.length;
        for (var nsind = len_show_names - 1; nsind >= 0; nsind--)
        {
            show_obj = null;
            show_obj = document.getElementById ? document.getElementById(divs_show_names[nsind]) : null;
            //alert(divs_show_names[nsind] + ": " + show_obj);
            if (show_obj) {divs_show.push(show_obj);}
        }
    }

    if (names_hide) {
        var divs_hide_names = names_hide.split(",");
        //alert(divs_showhide + ": " + divs_other_names)
        var len_hide_names = divs_hide_names.length;
        for (var nhind = len_hide_names - 1; nhind >= 0; nhind--)
        {
            hide_obj = null;
            hide_obj = document.getElementById ? document.getElementById(divs_hide_names[nhind]) : null;
            //alert(divs_hide_names[nhind] + ": " + hide_obj);
            if (hide_obj) {divs_hide.push(hide_obj);}
        }
    }

    //alert(1);

    var use_show_class = "map-shown";
    var use_hide_class = "map-hidden";
    if (map_shown)
    {
        use_show_class = "map-hidden";
        use_hide_class = "map-shown";
    }

    {
        var len_show = divs_show.length;
        //var show_obj = null;
        for (var dsind = len_show - 1; dsind >= 0; dsind--)
        {
            show_obj = divs_show[dsind];
            show_obj.className = use_show_class;
        }

        var len_hide = divs_hide.length;
        //var show_obj = null;
        for (var dhind = len_hide - 1; dhind >= 0; dhind--)
        {
            hide_obj = divs_hide[dhind];
            hide_obj.className = use_hide_class;
        }
    }

    if (map_shown) {
        //map_canvas.className = "map-hidden";
        map_shown = false;
        return;
    }

    map_shown = true;
    //map_canvas.className = "map-shown";

    if (map_obj) {return;}
    map_obj = true;

    var desc_table = "<table>";
    var desc_info = [{'name': '1', 'lon':14.424133, 'lat':50.089926}, {'name': '2', 'lon':15.783691, 'lat':50.037031}];
    descs_inner = "";

/*
    for (var diind = 0; diind < 2; diind++)
    {
        var one_desc_info = desc_info[diind];
        descs_inner += "<tr><td>";
        descs_inner += "<b>" + one_desc_info['name'] + "</b><br />lon: " + one_desc_info['lon'] + "<br />lat: " + one_desc_info['lat'];
        descs_inner += "</td></tr>";
    }
    desc_table += descs_inner;
    desc_table += "</table>";
    descs_elm.innerHTML = desc_table;
    descs_count = 2;
    //alert(descs_elm.innerHTML);
    //descs_elm.innerHTML = "wtf?";
    //alert(descs_elm.innerHTML);
*/

    //var map;

    var marker_files = {};
    marker_files['main'] = geocodingdir + "openlayers/img/marker-gold.png";
    openlayers_init(div_name, marker_files);

/*

    var latlng = new google.maps.LatLng(-34.397, 150.644);
    var myOptions = {
        zoom: 8,
        center: latlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    map_obj = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

    google.maps.event.addListener(map_obj, 'click', function(event) {
      addMarker(event.latLng);
    });

  function addMarker(location) {
    marker = new google.maps.Marker({
      position: location,
      map: map_obj
    });
    markersArray.push(marker);
  }

 // Removes the overlays from the map, but keeps them in the array
  function clearOverlays() {
    if (markersArray) {
      for (i in markersArray) {
        markersArray[i].setMap(null);
      }
    }
  }

  // Shows any overlays currently in the array
  function showOverlays() {
    if (markersArray) {
      for (i in markersArray) {
        markersArray[i].setMap(map_obj);
      }
    }
  }

  // Deletes all markers in the array by removing references to them
  function deleteOverlays() {
    if (markersArray) {
      for (i in markersArray) {
        markersArray[i].setMap(null);
      }
      markersArray.length = 0;
    }
  }
*/

};

