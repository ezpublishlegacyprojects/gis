POIMap = function() {

}
POIMap.prototype = new XROWMap();
POIMap.prototype.constructor = POIMap;

POIMap.prototype.start = function(element) {
    
    this.init(element);//init parent Map
    
    this.markerLayer;
    this.popupControl;
    this.popup;
    
    if (this.options.url != "false" ) {
        if (this.options.proxy != "false") {
            OpenLayers.ProxyHost = this.options.proxy;
        }

        this.styledPoint = new OpenLayers.StyleMap({
            "default" : new OpenLayers.Style({
                graphicWidth : this.size.w,
                graphicHeight : this.size.h,
//                graphicXOffset : this.xoffset,
//                graphicYOffset : this.yoffset,
                externalGraphic : this.options.image,
                pointRadius : "13",
                cursor : 'pointer'
            })
        });
        
        this.markers.destroy();//remove parent Marker
        this.markerLayer = new OpenLayers.Layer.GML('GeoRSS', this.options.url,
                {
                    format : OpenLayers.Format.GeoRSS,
                    styleMap : this.styledPoint
                });

        this.map.addLayer(this.markerLayer);

        this.popupControl = new OpenLayers.Control.SelectFeature(
                this.markerLayer,
                {
                    onSelect : function(feature) {
                        this.pos = feature.geometry;
                        this.featureLonLat = new OpenLayers.LonLat(this.pos.x, this.pos.y);
                        this.map.setCenter(this.featureLonLat, 14);//@TODO:get the zomm factor from the default ini
                        
                        if (typeof this.popup != "undefined") {
                            this.map.removePopup(this.popup);
                        }
                        this.popup = new OpenLayers.Popup.FramedCloud("popup",
                                this.featureLonLat,
                                new OpenLayers.Size(200, 200), 
                                "<h3>" + feature.attributes.title + "</h3>" + feature.attributes.description,
                                null, 
                                true);
                        this.popup.calculateRelativePosition = function () {
                            return 'br';
                        }
                        this.map.addPopup(this.popup);
                    }
                });
        this.map.addControl(this.popupControl);
        this.popupControl.activate();
    }
    this.map.render(element);//if we have a url we are going to render the GML, otherwise Parent should be rendered
}

/*
// support GetFeatureInfo
var map = this.map;
this.map.events.register('click', map, function(e) {
    console.log(map);
    var params_new =
        {
            REQUEST : "GetFeatureInfo",
            EXCEPTIONS : "application/vnd.ogc.se_xml",
            BBOX : map.getExtent().toBBOX(),
            SERVICE : "WMS",
            INFO_FORMAT : 'text/plain',
            QUERY_LAYERS : map.layers[0].params.LAYERS,
            FEATURE_COUNT : 50,
            Layers : 'Hannover:Museen',
            WIDTH : map.size.w,
            HEIGHT : map.size.h,
            format : 'image/png',
            styles : map.layers[0].params.STYLES,
            srs : map.layers[0].params.SRS
        };

    // handle the wms 1.3 vs wms 1.1 madness
    if (map.layers[0].params.VERSION == "1.3.0") {
        params_new.version = "1.3.0";
        params_new.j = parseInt(e.xy.x);
        params_new.i = parseInt(e.xy.y);
    } else {
        params_new.version = "1.1.1";
        params_new.x = parseInt(e.xy.x);
        params_new.y = parseInt(e.xy.y);
    }

    OpenLayers.loadURL(
            "http://admin.hannover.de/geoserver/Hannover/wms",
            params_new, this, setHTML, setHTML);
    OpenLayers.Event.stop(e);
});

// sets the HTML provided into the nodelist element
function setHTML(response) {
    console.log(response.responseText);
}
*/

