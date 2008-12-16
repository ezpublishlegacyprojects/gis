{*?template charset=utf8?*}
<div>
{if $attribute.has_content}
    <div style="float: left;">
    <label>{'Latitude'|i18n( 'design/standard/content/datatype' )}:</label>
    {$attribute.content.latitude}

    <label>{'Longitude'|i18n( 'design/standard/content/datatype' )}:</label>
    {$attribute.content.longitude}

    <label>{'Street'|i18n( 'design/standard/content/datatype' )}:</label>
    {$attribute.content.street}

    <label>{'ZIP'|i18n( 'design/standard/content/datatype' )}:</label>
    {$attribute.content.zip}

    <label>{'City'|i18n( 'design/standard/content/datatype' )}:</label>
    {$attribute.content.city}

    <label>{'State'|i18n( 'design/standard/content/datatype' )}:</label>
    {$attribute.content.state}

    <label>{'Country'|i18n( 'design/standard/content/datatype' )}:</label>
    {$attribute.content.country}
    
    </div>
    <div style="float: right;">
        <div id="mapContainer" style="width: 300px; height: 300px;"></div>
    </div>
    <div style="clear: both"></div>
    {if ezini("GISSettings","Interface","gis.ini")|eq('Yahoo')} 
        <script type="text/javascript" src="http://api.maps.yahoo.com/ajaxymap?v=2.0&appid={ezini('Yahoo','ApplicationID','gis.ini')}"></script>
        {literal}
        <script type="text/javascript">

            // Create a lat/lon object
            var myPoint = new YGeoPoint({/literal}{$attribute.content.latitude}{literal},{/literal}{$attribute.content.longitude}{literal});
            // Create a map object
            var map = new  YMap(document.getElementById('mapContainer'));
            // Add a pan control
            map.addPanControl();
            // Add a slider zoom control
            map.addZoomLong();
            // Display the map centered on a latitude and longitude
            map.drawZoomAndCenter(myPoint, 3);
            // Create a marker positioned at a lat/lon
            var marker = new YMarker(myPoint);        
            // Display the marker
            map.addOverlay(marker);

        </script>
        {/literal}
    {/if}


    {if ezini("GISSettings","Interface","gis.ini")|eq('Google')} 
        <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={ezini("Google","ApplicationID","gis.ini")}" type="text/javascript"></script>        
        <script type="text/javascript">
        {literal}
            function initialize() {
              if (GBrowserIsCompatible()) {
                var map = new GMap2(document.getElementById("mapContainer"));

                // Display the map centered on a latitude and longitude
                map.setCenter(new GLatLng({/literal}{$attribute.content.latitude}{literal}, {/literal}{$attribute.content.longitude}{literal}), 13);
                // set maps controls
                map.enableContinuousZoom();
                map.enableDoubleClickZoom();
                map.addControl(new GSmallMapControl());
                map.addControl(new GScaleControl());
                map.addControl(new GMapTypeControl());
                // Create our "tiny" marker icon
                var blueIcon = new GIcon(G_DEFAULT_ICON);
                blueIcon.image = "http://gmaps-samples.googlecode.com/svn/trunk/markers/blue/blank.png";
                // Set up our GMarkerOptions object
                markerOptions = { icon:blueIcon };
                // define marker on the current position
                var marker = new GMarker(new GLatLng({/literal}{$attribute.content.latitude}{literal}, {/literal}{$attribute.content.longitude}{literal}), markerOptions);
                // display the marker
                map.addOverlay(marker);

              }
            }
            initialize();
        {/literal}
        </script>
    {/if}



{else}
    No geo information avialable.
{/if}
</div>