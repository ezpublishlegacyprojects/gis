RSSMap = function() {

}
RSSMap.prototype = new XROWMap();
RSSMap.prototype.constructor = RSSMap;

RSSMap.prototype.start = function(element) {
	
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
                        this.map.setCenter(this.featureLonLat, 16);//@TODO:get the zomm factor from the default ini - comes from this.config
                        
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
