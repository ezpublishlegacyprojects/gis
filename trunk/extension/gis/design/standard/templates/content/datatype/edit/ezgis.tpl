{* DO NOT EDIT THIS FILE! Use an override template instead. *}


<div class="block">
    <div class="element">
{default attribute_base=ContentObjectAttribute}
<fieldset>
<legend>{'Geographic location'|i18n( 'extension/gis' )}<legend/>
<label>{'Latitude'|i18n( 'extension/gis' )}:</label>
<input class="box" size="32" type="text" name="{$attribute_base}_ezgis_latitude_{$attribute.id}" size="12" value="{section show=$attribute.content.is_valid}{$attribute.content.latitude}{/section}" />

<label>{'Longitude'|i18n( 'extension/gis' )}:</label>
<input class="box" size="32" type="text" name="{$attribute_base}_ezgis_longitude_{$attribute.id}" size="12" value="{section show=$attribute.content.is_valid}{$attribute.content.longitude}{/section}" />

<label><input name="{$attribute_base}_ezgis_update_{$attribute.id}" value="Update map" title="Resolve Information" type="checkbox"> {'Update Location from Address'|i18n( 'extension/gis' )}:</label>


</fieldset>
<fieldset>
<legend>{'Address'|i18n( 'extension/gis' )}<legend/>
<label>{'Street'|i18n( 'extension/gis' )}:</label>
<input class="box" size="32"  type="text" name="{$attribute_base}_ezgis_street_{$attribute.id}" size="12" value="{section show=$attribute.content.is_valid}{$attribute.content.street}{/section}" />
<label>{'ZIP'|i18n( 'extension/gis' )}:</label>
<input class="box" size="32" type="text" name="{$attribute_base}_ezgis_zip_{$attribute.id}" size="12" value="{section show=$attribute.content.is_valid}{$attribute.content.zip}{/section}" />
<label>{'City'|i18n( 'extension/gis' )}:</label>
<input class="box" size="32" type="text" name="{$attribute_base}_ezgis_city_{$attribute.id}" size="12" value="{section show=$attribute.content.is_valid}{$attribute.content.city}{/section}" />
<label>{'State'|i18n( 'extension/gis' )}:</label>
<input class="box" size="32" type="text" name="{$attribute_base}_ezgis_state_{$attribute.id}" size="12" value="{section show=$attribute.content.is_valid}{$attribute.content.state}{/section}" />
<label>{'Country'|i18n( 'extension/gis' )}:</label>

{let countries=fetch( 'content', 'country_list' )
     class_content=$attribute.class_content}

{def $country = $attribute.content.country}
<select name="{$attribute_base}_ezgis_country_{$attribute.id}">
{def $alpha_2 = ''}
{foreach $countries as $key => $current_country}
     {set $alpha_2 = $current_country.Alpha2}
     {if $country|ne( '' )}
        {if $country|is_array|not}
            {* Backwards compatability *}
            <option {if $country|eq( $current_country.Alpha2 )}selected="selected"{/if} value="{$alpha_2}">{$current_country.Name}</option>
        {else}
            <option {if is_set( $country.$alpha_2 )}selected="selected"{/if} value="{$alpha_2}">{$current_country.Name}</option>
        {/if}
     {else}
            <option value="{$alpha_2}">{$current_country.Name}</option>
     {/if}
{/foreach}
</select>

{/let}
</fieldset>

<input class="button" name="StoreButton" value="{'Update'|i18n( 'extension/gis' )}" title="{'Resolve information'|i18n( 'extension/gis' )}" type="submit">

{if ezini_hasvariable("GISSettings","GeocoderURL","gis.ini")}
<p>
<label>{'Use this link to find location based on an address'|i18n( 'extension/gis' )}:</label>
<a href="{ezini("GISSettings","GeocoderURL","gis.ini")}" target="_blank">{'Lookup'|i18n( 'extension/gis' )}</a>
</p>
{/if}

{/default}

    </div>
    <div class="element" style="float: right;">
        <div id="mapContainer" style="width: 300px; height: 300px;"></div>
    </div>
</div>

{* map attribute values or define default values for lat and long *}
{if and(not($attribute.content.latitude),not($attribute.content.longitude))}
    {def $latitude = 40.711695}
    {def $longitude = -74.01228}
{else}
    {def $latitude = $attribute.content.latitude}
    {def $longitude = $attribute.content.longitude}
{/if}



{if ezini("GISSettings","Interface","gis.ini")|eq('Yahoo')}
    <script type="text/javascript" src="http://api.maps.yahoo.com/ajaxymap?v=2.0&appid={ezini('Yahoo','ApplicationID','gis.ini')}"></script>
    {literal}
    <style type="text/css">
    #mapContainer {
        height: 300px;
        width: 300px;
    }
    </style>
    {/literal}
    {literal}

    <script type="text/javascript">
        // Capture the user mouse-click and expand the SmartWindow
        function onSmartWinEvent() {
             var words = "<b>{/literal}{'Current location'|i18n( 'extension/gis' )}{literal}</b>";      marker.openSmartWindow(words);
        }
          // Create a lat/lon object
          var myPoint = new YGeoPoint({/literal}{$latitude}{literal},{/literal}{$longitude}{literal});
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
          // Add a label to the marker
            marker.addLabel("C");
            // Call onSmartWinEvent when the user clicks on the marker
        YEvent.Capture(marker, EventsList.MouseClick, onSmartWinEvent);
        // Display the marker
        map.addOverlay(marker);

    </script>
    {/literal}
{/if}

{if ezini("GISSettings","Interface","gis.ini")|eq('Google')}
{literal}
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key={/literal}{ezini("Google","ApplicationID","gis.ini")}{literal}" type="text/javascript"></script>
<script type="text/javascript">

    function initialize() {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("mapContainer"));

        // Display the map centered on a latitude and longitude
        map.setCenter(new GLatLng({/literal}{$latitude}{literal}, {/literal}{$longitude}{literal}), 13);

        // set maps controls
        map.enableContinuousZoom();
        map.enableDoubleClickZoom();
        map.addControl(new GSmallMapControl());
        map.addControl(new GScaleControl());
        map.addControl(new GMapTypeControl());

        // Infowindow
        var infobox = {
            infowindow: 'custom',
            infowindowtext: '{/literal}<b>{'Current location'|i18n( 'extension/gis' )}</b>{literal}',
            isdefault: true
        };

        // Create our "tiny" marker icon
        var blueIcon = new GIcon(G_DEFAULT_ICON);
        blueIcon.image = "http://gmaps-samples.googlecode.com/svn/trunk/markers/blue/blank.png";

        // Set up our GMarkerOptions object
        markerOptions = { icon:blueIcon };

        // define marker on the current position
        var marker = new GMarker(new GLatLng({/literal}{$latitude}{literal}, {/literal}{$longitude}{literal}), markerOptions);

        // display the marker
        map.addOverlay(marker);

        // Add event listener on the marker icon
        GEvent.addListener(marker, 'click', function() {marker.openInfoWindowHtml(infobox.infowindowtext);});

      }
    }
    initialize();
</script>
{/literal}
{/if}

