XROWMap = function() {
}
XROWMap.prototype.start = function(element) {
    this.init(element);
}
XROWMap.prototype.init = function(element) {
    var map, options={}, layersettings={}, tmp, featureLayerName = [], featureLayers=[], x=0;
    this.map, this.layer, this.styledPoint, this.lonLat, this.markers, this.params={}, this.layerOptions={};
    this.options = element.dataset;
    this.config = $('#'+this.options.config);
    this.mapOptions=this.config.data('mapoptions');
    this.projection = $(this.config).find('.baseLayer').data().projection;
    Proj4js.defs["EPSG:25832"] = "+proj=utm +zone=32 +ellps=GRS80 +units=m +no_defs";//@TODO: Why the Hell is the def file not used?
    this.zoom = this.mapOptions.mapview.zoom;
    
    OpenLayers.IMAGE_RELOAD_ATTEMPTS = 5;//pink tiles avoiding
    OpenLayers.Request.DEFAULT_CONFIG.url = location.host;// change the url from window.location.href to location .host
    //OpenLayers.ProxyHost = "/cgi-bin/proxy.py?url=";
    
    //fix for elements which are not visibly at first, for e.g. like maps hidden in tabs
    if(typeof(this.mapOptions.mapview.height)=='undefined')
    {
        if ($(element).height() == 0) 
        {
            $(element).height($(element).width());
        }
    }
    else
    {
        $(element).height(this.mapOptions.mapview.height);
    }


    //initalize map Object
    this.map = new OpenLayers.Map(
                {
                    controls : [],
                    theme : this.mapOptions.theme,
                    projection: this.mapOptions.generals.projection,
                    maxResolution: 'auto',
                    units : this.mapOptions.generals.units,
                    panMethod : OpenLayers.Easing.Quad.easeInOut,
                    panDuration : 75
                });
    //set additional MapOptions
    if(typeof(this.mapOptions.mapoptions)!='undefined')
    {
        for(var i in this.mapOptions.mapoptions)
        {
            options[i] = eval(this.mapOptions.mapoptions[i]);
        }
        this.map.setOptions(options);
    }
    //create Layers
    map = this.map;//save the map over the following function
    $(this.config).find('li').each(function(index, value)
    {
        eval("this.layer = new OpenLayers.Layer." + value.dataset.service + "('"+ value.dataset.layername +"', '"+ value.dataset.url +"', "+ value.dataset.layerparams +", "+ value.dataset.layeroptions +");");

        if(typeof(value.dataset.layersettings)!='undefined')
        {
            tmp = $.parseJSON(value.dataset.layersettings);
            for(var i in tmp)
            {
                layersettings[i] = eval(tmp[i]);
            }
            this.layer.addOptions(layersettings);
        }
        //save all special feature Layers to this.map for next steps
        if(typeof(value.dataset.features) != 'undefined')
        {
            tmp = $.parseJSON(value.dataset.features);
            
            if(typeof(tmp.getFeatureInfo) != 'undefined' && tmp.getFeatureInfo == true)
            {
                map.events.register('click', map, function(e) {
                    var params_new =
                        {
                            REQUEST : "GetFeatureInfo",
                            EXCEPTIONS : "application/vnd.ogc.se_xml",
                            BBOX : map.getExtent().toBBOX(),
                            SERVICE : "WMS",
                            INFO_FORMAT : 'text/plain',
                            QUERY_LAYERS : value.dataset.layername,
                            FEATURE_COUNT : 100,
                            Layers : value.dataset.layername,
                            WIDTH : map.size.w,
                            HEIGHT : map.size.h,
                            format : 'image/png',
//                            styles : map.layers[0].params.STYLES,
                            srs : map.layers[0].params.SRS
                        };
                        params_new.version = "1.1.1";
                        params_new.x = parseInt(e.xy.x);
                        params_new.y = parseInt(e.xy.y);

                    OpenLayers.loadURL(
                            ""+value.dataset.url+"",
                            params_new, this, setHTML, setHTML);
                    OpenLayers.Event.stop(e);
                });

            }
            featureLayers[x] = 
            {
                    'featureType' : tmp.featureType,
                    'layer' : this.layer,
            }
            ++x;
        }
        map.addLayer(this.layer);
    });
    this.map.featureLayers = featureLayers;
    this.map = map;//@TODO: Why do we have to do it this way?!

    //defining Icon stuff for gml Layer and marker Layer
    this.size = new OpenLayers.Size(this.mapOptions.icon.width, this.mapOptions.icon.height);
    this.xoffset = (this.size.w / 2) + (Number(this.options.xoffset));
    this.yoffset = (this.size.h) + (Number(this.options.yoffset));
    this.offset = new OpenLayers.Pixel(-this.xoffset, -this.yoffset);
    this.icon = new OpenLayers.Icon(this.mapOptions.icon.src, this.size, this.offset);

    // add simple Marker and reproject the coords
    this.markers = new OpenLayers.Layer.Markers("Marker Layer");
    this.lonLat = new Proj4js.Point(this.options.lon, this.options.lat);
    Proj4js.transform(new Proj4js.Proj(this.projection.projection), new Proj4js.Proj(this.projection.displayProjection), this.lonLat);
    this.lonLat = new OpenLayers.LonLat(this.lonLat.x, this.lonLat.y);
    this.map.addLayer(this.markers);
    this.markers.addMarker(new OpenLayers.Marker(this.lonLat, this.icon));

    // add controls
    this.map.setCenter(this.lonLat, this.zoom);
//    this.map.zoomToMaxExtent();
    
    if(typeof(this.mapOptions.mapview.controls)!='undefined')
    {
        map = this.map;
        $.each(this.mapOptions.mapview.controls, function(index, value)
                {
                    map.addControl(new OpenLayers.Control[value]());
                });
        this.map = map;
    }else//default Controls
    {
        this.map.addControl(new OpenLayers.Control.Navigation());
        this.map.addControl(new OpenLayers.Control.PanPanel());
        this.map.addControl(new OpenLayers.Control.ZoomPanel());
    }

    //if (!this.map.getCenter()) this.map.zoomToMaxExtent();
    //render the default Map
    if (this.options.render == 'true') {
        this.map.render(element);
    }
}// end XROWMap init

//some Helper, should be later in for e.g. util.js
//function toLL(obj){return obj.transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326"));}
//function fromLL(obj){return obj.transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));}

// sets the HTML provided into the nodelist element
function setHTML(response){
    alert( response.responseText);
//    document.getElementById('nodelist').innerHTML = response.responseText;
};

$(document).ready(function() {
    $('.XROWMap').each(function(index) {
        switch ($(this)[0].dataset.maptype) {
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
