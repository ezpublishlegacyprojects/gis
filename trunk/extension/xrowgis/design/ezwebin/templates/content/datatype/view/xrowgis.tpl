{set-block scope=global variable=cache_ttl}0{/set-block}
{if $node_id|not}
    {def $node_id=2}
{/if}
{if $icon|not}
        {def $icon='/share/icons/crystal-admin/32x32/mimetypes/readme.png'}
{/if}
{if $width|not}
    {def $width='100%'}
{/if}
{if $height|not}
    {def $height='100%'}
{/if}
{if $node_id|not}
    {def $node_id=2}
{/if}
{if $zoom|not}
    {def $zoom=ezini("Yahoo","DefaultZoom","xrowgis.ini")}
{/if}
{if $centerposition|not}
    {def $centerposition='Hanover Germany'}
{/if}
{if $classes|not}
    {def $classes='gis,poi'}
{/if}
<script type="text/javascript" src="http://api.maps.yahoo.com/ajaxymap?v=3.7&appid={ezini("Yahoo","ApplicationID","xrowgis.ini")}"></script>
{literal}
<style type="text/css">
#map{
{/literal}
{if $height|not}
    height: 100%;
{/if}
{if $width|not}
    width: 100%;
{/if}
{literal}
}
</style>
{/literal}
<div id="map"></div>

{literal}
<script type="text/javascript">
    // Create a Map that will be placed in the "map" div.
    var map = new YMap(document.getElementById('map'));

    function startMap(){
{/literal}
        var GeoPoint = new YGeoPoint( {$attribute.content.latitude}, {$attribute.content.longitude});
        // Add the ability to change between Sat, Hybrid, and Regular Maps
        map.addTypeControl();
        // Add the zoom control. Long specifies a Slider versus a "+" and "-" zoom control
        map.addZoomLong();
        // Add the Pan control to have North, South, East and West directional control
        map.addPanControl();
        // Specifying the Map starting location and zoom level
        map.drawZoomAndCenter( GeoPoint, {$zoom});



var markerMarkup = '<h3 class="map-headline">{$attribute.object.name|wash(javascript)}</h3>';
placeMarker(GeoPoint,markerMarkup);

{literal}
        function placeMarker(geoPoint,markerMarkup){

            var newMarker= new YMarker(geoPoint, createCustomMarkerImage());
            YEvent.Capture(newMarker, EventsList.MouseOver,
                function(){
                    newMarker.openSmartWindow(markerMarkup);
                });
            map.addOverlay(newMarker);
        }
        function createCustomMarkerImage(){
            var myImage = new YImage();
            myImage.src = '{/literal}{$icon}{literal}';
            myImage.size = new YSize(20,20);
            myImage.offsetSmartWindow = new YCoordPoint(0,0);
            return myImage;
        }


    }

window.onload = startMap;
</script>
{/literal}
