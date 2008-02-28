{if $module_result.view_parameters.zoom}
{def $zoom=$module_result.view_parameters.zoom}
{else}
{def $zoom=ezini("Yahoo","DefaultZoom","gis.ini")}
{/if}
<html>
<head>
<script type="text/javascript" src="http://api.maps.yahoo.com/ajaxymap?v=2.0&appid={ezini("Yahoo","ApplicationID","gis.ini")}"></script>
{literal}
<style type="text/css">
body
{
	margin: 0;
	border: 0;
	padding: 0;
}
#mapContainer {
    height: 500;
    width: 100%;
}
</style>
{/literal}
</head>
<body>

<div id="mapContainer"></div>

<script type="text/javascript">
    // Create a lat/lon object
{if and( $module_result.view_parameters.latitude, $module_result.view_parameters.longitude )}

var myPoint = new YGeoPoint({$module_result.view_parameters.latitude},{$module_result.view_parameters.longitude});
{else}

var myPoint = new YGeoPoint(0,0);

{/if}
  // Create a map object
  var map = new  YMap(document.getElementById('mapContainer'));
  // Add a pan control
  map.drawZoomAndCenter(myPoint, {$zoom});
  // Display the map centered on a latitude and longitude
  //map.setZoomLevel( {$zoom} );
  map.addPanControl();
  // Add a slider zoom control
  map.addZoomLong();

  // Overlay data from GeoRSS file
  map.addOverlay(new YGeoRSS('{concat( ezini("GISSettings","PublicURL","gis.ini"), '/gis/georssserver/', $module_result.view_parameters.rss )}'));

  

</script>

</body>
</html>