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
    this.markers

    if (this.options.css == 'false') {
        this.options.css = 'extension/xrowgis/design/standard/javascript/OpenLayers/theme/default/style.css';
    }
    if (this.options.image == 'false') {
        this.options.image = "extension/xrowgis/design/standard/javascript/OpenLayers/img/marker.png";
        this.size = new OpenLayers.Size(21,25);
    }
    this.map = new OpenLayers.Map({
        controls : [],
        theme : this.options.css,
        displayProjection : new OpenLayers.Projection("EPSG:4326"),
        units : "m",
        maxResolution : 156543.0339,
        maxExtent : new OpenLayers.Bounds(-20037508, -20037508, 20037508,
                20037508.34)
    });
    
    switch (this.options.layer) {
    default:
        this.layer = new OpenLayers.Layer.OSM()
        break;
    }
/*
    // create Vector layer
    this.markers = new OpenLayers.Layer.Vector("Markers", {
        displayInLayerSwitcher : false,
        styleMap : this.styledPoint
    });
*/
    this.markers = new OpenLayers.Layer.Markers( "Markers" );
    
    this.map.addLayers([ this.layer, this.markers ]);

    this.lonLat = new OpenLayers.LonLat(this.options.lon, this.options.lat).transform(
            new OpenLayers.Projection(this.map.displayProjection), this.map
                    .getProjectionObject());
/*
    this.controls = {
        drag : new OpenLayers.Control.DragFeature(this.markers, {
            'onComplete' : this.onCompleteMove
        })
    }
    this.map.addControl(this.controls['drag']);

    if (this.options.drag == true) {
        this.controls['drag'].activate();
    }


    this.params = {
        map : this.map,
        lonLat : this.lonLat,
        layer : this.markers
    }
    this.drawFeatures(this.params);
    -(this.size.w/2)+(this.options.xoffset), -(this.size.h+((this.options.yoffset)))
*/
//add simple Marker
    this.size = new OpenLayers.Size(this.options.width,this.options.height);
    this.xoffset = (this.size.w/2)+(Number(this.options.xoffset));
    this.yoffset = (this.size.h)+(Number(this.options.yoffset));
    this.offset = new OpenLayers.Pixel(-this.xoffset, -this.yoffset);
    this.icon = new OpenLayers.Icon(this.options.image, this.size, this.offset);
    this.markers.addMarker(new OpenLayers.Marker(this.lonLat,this.icon));

//add controls
    this.map.setCenter(this.lonLat, this.options.zoom);
    this.map.addControl(new OpenLayers.Control.Navigation());
    this.map.addControl(new OpenLayers.Control.PanPanel());
    this.map.addControl(new OpenLayers.Control.ZoomPanel());
    
//should we render the default Map?
    if(this.options.render == 'true'){
        this.map.render(this.options.div);
    }
}// end XROWMap init

$(document).ready(function() {

    $('.XROWMap').each(function(index) {
        
        switch ($(this)[0].dataset.maptype) {
        case 'RSSMap':
            var map = new RSSMap();
            break;
        case 'BackendMap':
            var map = new BackendMap();
            break;
        default:
            var map = new XROWMap();
            $(this)[0].dataset.render = true;//render the default Map
        }
        map.start($(this)[0]);
    });
});