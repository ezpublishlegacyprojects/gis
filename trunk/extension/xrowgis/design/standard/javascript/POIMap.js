POIMap = function() {

}
POIMap.prototype = new XROWMap();
POIMap.prototype.constructor = POIMap;

POIMap.prototype.start = function(element) {
    this.init(element);//init parent Map
    this.parentMap = this.map;//we want to render the parent Map, if there is no return value from gml
    this.markerLayer;
    this.popup;
    this.layerURL=[];

    if (this.options.url != "false" || typeof(this.map.featureLayers) != 'undefined') {//if we have no url, render the default map
        
        this.markers.destroy();//destroy Parent Marker
        
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
                                
                                if (typeof this.popup != "undefined" && this.popup != null) {
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
                                this.popup.events.register("click", this, popupDestroy);
                            }
                        });
                this.map.addControl(this.popupControl);
                this.popupControl.activate();
              break;
            case 'Shape':
                    if(typeof(this.layerURL[this.map.featureLayers[i].layer.url])!= 'object')
                    {
                        this.layerURL[this.map.featureLayers[i].layer.url] = new Array();
                    }
                    this.layerURL[this.map.featureLayers[i].layer.url][i] = this.map.featureLayers[i].layerName;
              break;
            }
        }
    }
    for(var x in this.layerURL)
    {
        var tmp, map;
        tmp = this.layerURL[x].shift();
        map = this.map;
        tmp = this.layerURL[x];
        map.events.register('click', map, function(e) {
            params_new =
                {
                    REQUEST : "GetFeatureInfo",
                    EXCEPTIONS : "application/vnd.ogc.se_xml",
                    BBOX : map.getExtent().toBBOX(),
                    SERVICE : "WMS",
                    INFO_FORMAT : 'text/plain',
                    QUERY_LAYERS : tmp.join(', '),
                    FEATURE_COUNT : 100,
                    Layers : tmp.join(', '),
                    WIDTH : map.size.w,
                    HEIGHT : map.size.h,
                    format : 'image/png',
//                    styles : map.layers[0].params.STYLES,
                    srs : map.layers[0].params.SRS
                };
                params_new.version = "1.1.1";
                params_new.xy = e.xy;
                params_new.x = parseInt(e.xy.x);
                params_new.y = parseInt(e.xy.y);
            OpenLayers.loadURL(
                    ""+x+"",
                    params_new, this, setHTML);
            OpenLayers.Event.stop(e);
        });
    }
    this.map.render(element);
}

//all this stuff underneath here comes to MapUtils.js...later.

function setHTML(response) {
    var cat="", src="", leg="", linkinfo="", lines, vals, popup_info;
    
    if (response.responseText.indexOf('no features were found') == -1) {
        lines = response.responseText.split('\n');
        console.log(lines);
        for (lcv = 0; lcv < (lines.length); lcv++) {
            vals = lines[lcv].replace(/^\s*/,'').replace(/\s*$/,'').replace(/ = /,"=").replace(/'/g,'').split('=');
            if (vals[1] == "") {
                vals[1] = "";
            }
            if (vals[0].indexOf('Name') != -1 ) {
                cat = vals[1];
            } else if (vals[0].indexOf('SOURCE') != -1 ) {
                src = vals[1];
            } else if (vals[0].indexOf('INFO') != -1 ) {
                leg = vals[1];
            } else if (vals[0].indexOf('HREF') != -1 ) {
                linkinfo = vals[1];
            }
        }
        popup_info = "<font size=2><b>" + cat +
                     "</b><br />" + leg +
                     "<br /><a href='" + linkinfo + "' target='_blank'>Mehr</a>" +
                     "</font></font>";
        
        this.featureLonLat = this.getLonLatFromPixel(params_new.xy);
        this.setCenter(this.featureLonLat, 16);
        if (typeof this.popup != "undefined" && this.popup != null) {
            this.removePopup(this.popup);
        }
        this.popup = new OpenLayers.Popup.FramedCloud("popup",
                this.featureLonLat,
                new OpenLayers.Size(200, 200), 
                popup_info,
                null, 
                true);
        this.popup.calculateRelativePosition = function () {
            return 'br';
        }
        this.addPopup(this.popup);
        this.popup.events.register("click", this, popupDestroy);
    }
}

function popupDestroy(e) {
    if(this.popup != null)
    {
        this.popup.destroy();
        this.popup = null;
    }
    OpenLayers.Util.safeStopPropagation(e);
}

function getLL(position)
{
    var currentPosition = [];
    currentPosition['lon'] = position.coords.longitude;
    currentPosition['lat'] = position.coords.latitude;
    return currentPosition;
}

function error(msg) {
    
    }

