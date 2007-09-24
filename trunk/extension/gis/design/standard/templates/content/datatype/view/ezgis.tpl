{*?template charset=utf8?*}
{section show=$attribute.has_content}
<label>{'Latitude'|i18n( 'design/standard/content/datatype' )}:</label>
{section show=$attribute.content.is_valid}{$attribute.content.latitude}{/section}

<label>{'Longitude'|i18n( 'design/standard/content/datatype' )}:</label>
{section show=$attribute.content.is_valid}{$attribute.content.longitude}{/section}

<label>{'Street'|i18n( 'design/standard/content/datatype' )}:</label>
{section show=$attribute.content.is_valid}{$attribute.content.street}{/section}

<label>{'ZIP'|i18n( 'design/standard/content/datatype' )}:</label>
{section show=$attribute.content.is_valid}{$attribute.content.zip}{/section}

<label>{'City'|i18n( 'design/standard/content/datatype' )}:</label>
{section show=$attribute.content.is_valid}{$attribute.content.city}{/section}

<label>{'State'|i18n( 'design/standard/content/datatype' )}:</label>
{section show=$attribute.content.is_valid}{$attribute.content.state}{/section}

<label>{'Country'|i18n( 'design/standard/content/datatype' )}:</label>
{section show=$attribute.content.is_valid}{$attribute.content.country}{/section}


<script type="text/javascript" src="http://api.maps.yahoo.com/ajaxymap?v=2.0&appid={ezini('GISSettings','ApplicationID','gis.ini')}"></script>
{literal}
<style type="text/css">
#mapContainer {
    height: 300px;
    width: 300px;
}
</style>
<div id="mapContainer"></div>

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

</script>
{/literal}



{section-else}
No geo information avialable.
{/section}