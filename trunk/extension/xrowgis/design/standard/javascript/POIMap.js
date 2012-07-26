POIMap = function() {

}
POIMap.prototype = new XROWMap();
POIMap.prototype.constructor = POIMap;

POIMap.prototype.start = function(element) {
    this.init(element);//init parent Map
    this.parentMap = this.map;//we want to render the parent Map, if there is no return value from gml
    this.markerLayer;
    this.popup;

    if (this.options.url != "false" || typeof(this.map.featureLayers) != 'undefined') {//if we have no url, render the default map
        
        this.markers.destroy();
        //try { /* direkt objekt benutzen */ } catch (e) {}
        for(var i in this.map.featureLayers)
        {
            switch(this.map.featureLayers[i].featureType)
            {
            case 'GeoRSS':
                this.styledPoint = new OpenLayers.StyleMap({
                    "default" : new OpenLayers.Style({
                        graphicWidth : this.size.w,
                        graphicHeight : this.size.h,
                        externalGraphic : this.mapOptions.icon.src,
                        pointRadius : "13",
                        cursor : 'pointer'
                    })
                });
                this.map.featureLayers[i].layer.addOptions({
                    format : OpenLayers.Format.GeoRSS,
                    styleMap : this.styledPoint
                });

                this.popupControl = new OpenLayers.Control.SelectFeature(
                        this.map.featureLayers[i].layer,
                        {
                            onSelect : function(feature) {
                                this.pos = feature.geometry;
                                this.featureLonLat = new OpenLayers.LonLat(this.pos.x, this.pos.y);
                                this.map.setCenter(this.featureLonLat, 16);
                                
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
              break;
            case 'Shape':
              break;
            }
        }
    }
    this.map.render(element);
}