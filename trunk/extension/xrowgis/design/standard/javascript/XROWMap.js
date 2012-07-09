XROWMap = function() {
}
XROWMap.prototype.start = function(element) {
    this.init(element);
}
XROWMap.prototype.init = function(element) {
    this.options = element.dataset;
    this.config = config;
    this.map;
    this.layer;
    this.styledPoint;
    this.lonLat;
    this.params;
    this.markers;
    this.zoom = this.config[this.options.view].Zoom;
    
    if ($(element).height() == 0) {
        if(typeof(this.config[this.options.view].height)=='undefined')
        {
            $(element).height($(element).width());
        }
        else
        {
            $(element).height(this.config[this.options.view].height);
        }
        
    }

    if (this.options.css == 'false' || this.options.image == 'false') {
        OpenLayers.Request.DEFAULT_CONFIG.url = location.host;// change the url from window.location.href to location  .host
    }
    if (this.options.css == 'false') {
        this.options.css = this.config.MapGenerals.css;
    }
    if (this.options.image == 'false') {
        this.options.image =  this.config.MapGenerals.icon.src;
        this.size = new OpenLayers.Size(this.config.MapGenerals.icon.width, this.config.MapGenerals.icon.height);
    }
    
    this.map = new OpenLayers.Map(
                {
                    controls : [],
                    theme : this.options.css,
                    displayProjection : new OpenLayers.Projection(this.config.MapGenerals.projection),
                    units : this.config.MapGenerals.units,
                    panMethod : OpenLayers.Easing.Quad.easeInOut,
                    panDuration : 75,
                });

    switch (this.options.layer) {
    case 'LHH+Region':// @TODO: make it generic
        this.layer = new OpenLayers.Layer.WMS("Hannover Stadt",
                "http://admin.hannover.de/geoserver/Hannover/wms",
                    {
                        layers : "Hannover:Hannover Stadt",
                        format : "image/png",
                        transparent:true,
                        tiled : true
                    },
                    {
                        isBaseLayer : true,
                        buffer : 1

                    });
        this.layer.addOptions(
                {
                    maxExtent : new OpenLayers.Bounds(538000, 5794000, 562001, 5813000),
                    scales : [ 5000, 1000, 2000, 4000, 10000, 15000, 20000 ]
                });
        this.zoom = 1;
        break;
    default:
         this.layer = new OpenLayers.Layer.OSM('OSM_LAYER', "http://admin.hannover.de/osm-tiles/${z}/${x}/${y}.png");
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
    this.map.setCenter(this.lonLat, this.zoom);

    if(typeof(this.config[this.options.view].Controls)!='undefined')
    {
        for(var i in this.config[this.options.view].Controls)
        {
            this.map.addControl(new OpenLayers.Control[this.config[this.options.view].Controls[i]]());
        }
    }else
    {
        this.map.addControl(new OpenLayers.Control.Navigation());
        this.map.addControl(new OpenLayers.Control.PanPanel());
        this.map.addControl(new OpenLayers.Control.ZoomPanel());
    }
    
    // should we render the default Map?
    if (this.options.render == 'true') {
        this.map.render(element);
    }
}// end XROWMap init
var config;
$(document).ready(function() {
    jQuery.ez('xrowGIS_page::getConfig', {}, function(result){
        config = result.content.config;
        $('.XROWMap').each(function(index) {
            switch ($(this)[0].dataset.maptype) {
            case 'RSSMap':
                var map = new RSSMap();
                break;
            case 'POIMap':
                var map = new POIMap();
                break;
            default:
                var map = new XROWMap();
                $(this)[0].dataset.render = true;// render the default Map
            }
            map.start($(this)[0]);
        });
        });
   

});
