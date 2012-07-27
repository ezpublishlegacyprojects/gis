XROWMap = function() {
}
XROWMap.prototype.start = function(element) {
    this.init(element);
}
XROWMap.prototype.init = function(element) {
    var map, options={}, layersettings={}, tmp, featureLayers=[], params_new, x=0;
    this.map, this.layer, this.styledPoint, this.lonLat, this.markers, this.params={}, this.layerOptions={};
    this.options = element.dataset;
    this.config = $('.'+this.options.config);
    this.mapOptions=this.config.data('mapoptions');
    this.projection = $(this.config).find('.baseLayer').data().projection;
    Proj4js.defs["EPSG:25832"] = "+proj=utm +zone=32 +ellps=GRS80 +units=m +no_defs";//@TODO: Why the Hell is the def file not used?
    this.zoom = this.mapOptions.mapview.zoom;

    OpenLayers.Request.DEFAULT_CONFIG.url = location.host;// change the url from window.location.href to location .host
    
    
    
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
            featureLayers[x] = 
            {
                    'featureType' : tmp.featureType,
                    'layerName' : value.dataset.layername,
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

    //set center
    this.map.setCenter(this.lonLat, this.zoom);
    //this.map.zoomToMaxExtent();
    // add controls
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
    //render the default Map
    if (this.options.render == 'true') {
        this.map.render(element);
    }
}// end XROWMap init

//all this stuff underneath here comes to MapUtils.js...later.

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
    $("button.current-position").click(function()
            {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(getLL, error, {timeout:7000});
                }else {
                    error('not supported');
                }
            });
    $(".click-list li").click(function()
        {
            if($(this)[0].layer.visibility===true && $(this)[0].layer.isBaseLayer ===false)
            {
                $(this)[0].layer.setVisibility(false);
            }else
            {
                $(this)[0].layer.setVisibility(true);
            }
        });
});
