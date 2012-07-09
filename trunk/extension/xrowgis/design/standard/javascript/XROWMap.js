XROWMap = function() {
}
XROWMap.prototype.start = function(element) {
    this.init(element);
}
XROWMap.prototype.init = function(element) {
    this.options = element.dataset;
    this.map;
    this.layer;
    this.styledPoint;
    this.lonLat;
    this.params;
    this.markers;
    this.config;

    jQuery.ez('xrowGIS_page::getConfig', {}, function(result){
        
        saveConfig(result.content);
        });
    
function saveConfig(e)
{
    this.options=e;
};
    console.log(this);
    
    if ($(element).height() == 0) {
        $(element).height($(element).width());
    }

    if (this.options.css == 'false' || this.options.image == 'false') {
        OpenLayers.Request.DEFAULT_CONFIG.url = location.host;// change the
                                                                // url from
                                                                // window.location.href
                                                                // to location
                                                                // .host
    }
    if (this.options.css == 'false') {
        this.options.css = "/extension/xrowgis/design/standard/javascript/OpenLayers/theme/default/style.css";
    }
    if (this.options.image == 'false') {
        this.options.image = "/extension/xrowgis/design/standard/javascript/OpenLayers/img/marker.png";
        this.size = new OpenLayers.Size(21, 25);
    }

    this.map = new OpenLayers.Map(
                {
                    controls : [],
                    theme : this.options.css,
                    displayProjection : new OpenLayers.Projection("EPSG:4326"),
                    units : "m",
                    panMethod : OpenLayers.Easing.Quad.easeInOut,
                    panDuration : 75,
                });
    
    switch (this.options.layer) {
    case 'LHH+Region':
        this.map.setOptions(
                {
                    maxExtent : new OpenLayers.Bounds(538000, 5794000, 562001, 5813000),
                    scales : [ 500, 1000, 2000, 4000, 10000, 15000, 20000 ]
                });
        this.layer = new OpenLayers.Layer.WMS("Hannover Stadt",
                "http://admin.hannover.de/geoserver/Hannover/wms",
                    {
                        layers : "Hannover:Hannover Stadt",
                        format : "image/png",
                        tiled : true
                    },
                    {
                        isBaseLayer : true,
                        buffer : 1
                    });
        break;
    default:
         this.layer = new OpenLayers.Layer.OSM('OSM_LAYER',
         "http://admin.hannover.de/osm-tiles/${z}/${x}/${y}.png");
        break;
    }
    
    this.markers = new OpenLayers.Layer.Markers("Markers");

    this.map.addLayers([ this.layer, this.markers ]);

    this.lonLat = new OpenLayers.LonLat(this.options.lon, this.options.lat)
            .transform(new OpenLayers.Projection(this.map.displayProjection),
                    this.map.getProjectionObject());

    // add simple Marker
    this.size = new OpenLayers.Size(this.options.width, this.options.height);
    this.xoffset = (this.size.w / 2) + (Number(this.options.xoffset));
    this.yoffset = (this.size.h) + (Number(this.options.yoffset));
    this.offset = new OpenLayers.Pixel(-this.xoffset, -this.yoffset);
    this.icon = new OpenLayers.Icon(this.options.image, this.size, this.offset);
    this.markers.addMarker(new OpenLayers.Marker(this.lonLat, this.icon));
    // add controls
    this.map.setCenter(this.lonLat, this.options.zoom);
    this.map.addControl(new OpenLayers.Control.Navigation());
    this.map.addControl(new OpenLayers.Control.PanPanel());
    this.map.addControl(new OpenLayers.Control.ZoomPanel());
    
/*
 * // support GetFeatureInfo var map = this.map;
 * this.map.events.register('click', map, function(e) { console.log(map); var
 * params_new = { REQUEST : "GetFeatureInfo", EXCEPTIONS :
 * "application/vnd.ogc.se_xml", BBOX : map.getExtent().toBBOX(), SERVICE :
 * "WMS", INFO_FORMAT : 'text/plain', QUERY_LAYERS :
 * map.layers[0].params.LAYERS, FEATURE_COUNT : 50, Layers : 'Hannover:Museen',
 * WIDTH : map.size.w, HEIGHT : map.size.h, format : 'image/png', styles :
 * map.layers[0].params.STYLES, srs : map.layers[0].params.SRS }; // handle the
 * wms 1.3 vs wms 1.1 madness if (map.layers[0].params.VERSION == "1.3.0") {
 * params_new.version = "1.3.0"; params_new.j = parseInt(e.xy.x); params_new.i =
 * parseInt(e.xy.y); } else { params_new.version = "1.1.1"; params_new.x =
 * parseInt(e.xy.x); params_new.y = parseInt(e.xy.y); }
 * 
 * OpenLayers.loadURL( "http://admin.hannover.de/geoserver/Hannover/wms",
 * params_new, this, setHTML, setHTML); OpenLayers.Event.stop(e); }); // sets
 * the HTML provided into the nodelist element function setHTML(response) {
 * console.log(response.responseText); }
 */
    // should we render the default Map?
    if (this.options.render == 'true') {
        this.map.render(element);
    }
}// end XROWMap init

$(document).ready(function() {
    $('.XROWMap').each(function(index) {
        switch ($(this)[0].dataset.maptype) {
        case 'RSSMap':
            var map = new RSSMap();
            break;
        default:
            var map = new XROWMap();
            $(this)[0].dataset.render = true;// render the default Map
        }
        map.start($(this)[0]);
    });
});
