{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{def $dragable = true()}
{if is_set($attribute.data_int)}
    {def $relatedObject = fetch('content', 'object', hash(
                                                          'object_id', $attribute.data_int))}
{/if}
<div class="block">
<div class="ajaxupdate">
<div class="element">
{if and(is_set($attribute.data_int), is_set($relatedObject))}
{set $dragable = false()}
<!--    <p style="font-weight:bold;">{'Geographic location'|i18n( 'extension/xrowgis' )}</p>-->
<br />
    <table>
{if is_set($relatedObject)}
        <tr>
            <td><label>{'Related Object'|i18n( 'extension/xrowgis' )}:</label></td>
            <td><a href="{$relatedObject.main_node.url_alias|ezurl( 'no', 'full')}">{$relatedObject.name}</a></td>
        </tr>
{/if}
        <tr>
            <td><label>{'Longitude'|i18n( 'extension/xrowgis' )}:</label></td>
            <td>{$attribute.content.longitude}</td>
        </tr>
        <tr>
            <td><label>{'Latitude'|i18n( 'extension/xrowgis' )}:</label></td>
            <td>{$attribute.content.latitude}</td>
        </tr>
        <tr>
            <td><label>{'Street'|i18n( 'extension/xrowgis' )}:</label></td>
            <td>{$attribute.content.street}</td>
        </tr>
        <tr>
            <td><label>{'ZIP'|i18n( 'extension/xrowgis' )}:</label></td>
            <td>{$attribute.content.zip}</td>
        </tr>
        <tr>
            <td><label>{'District'|i18n( 'extension/xrowgis' )}:</label></td>
            <td>{$attribute.content.district}</td>
        </tr>
        <tr>
            <td><label>{'City'|i18n( 'extension/xrowgis' )}:</label></td>
            <td>{$attribute.content.city}</td>
        </tr>
        <tr>
            <td><label>{'State'|i18n( 'extension/xrowgis' )}:</label></td>
            <td>{$attribute.content.state}</td>
        </tr>
        <tr>
            <td><label>{'Country'|i18n( 'extension/xrowgis' )}:</label></td>
            <td>{$attribute.content.country}</td>
        </tr>
    </table>
    <input type="hidden" name="ContentObjectAttribute_xrowgis_longitude_{$attribute.id}" value="{$attribute.content.longitude}" />
    <input type="hidden" name="ContentObjectAttribute_xrowgis_latitude_{$attribute.id}" value="{$attribute.content.latitude}" />
    <input type="hidden" name="ContentObjectAttribute_xrowgis_street_{$attribute.id}" value="{$attribute.content.street}" />
    <input type="hidden" name="ContentObjectAttribute_xrowgis_zip_{$attribute.id}" value="{$attribute.content.zip}" />
    <input type="hidden" name="ContentObjectAttribute_xrowgis_district_{$attribute.id}" value="{$attribute.content.district}" />
    <input type="hidden" name="ContentObjectAttribute_xrowgis_city_{$attribute.id}" value="{$attribute.content.city}" />
    <input type="hidden" name="ContentObjectAttribute_xrowgis_state_{$attribute.id}" value="{$attribute.content.state}" />
    <input type="hidden" name="ContentObjectAttribute_xrowgis_country_{$attribute.id}" value="{$attribute.content.country}" />
    <input type="hidden" name="ContentObjectAttribute_xrowgis_data_object_relation_id_{$attribute.id}" value="{$attribute.data_int}" id="xrowGIS-rel" />
    <br />
    <input onclick="jQuery().servemap( 'releaseRelation', {literal}{{/literal}'attributeID':{$attribute.id},'version':{$attribute.version},'relObjectID':{$attribute.data_int}{literal}}{/literal});" class="button" name="ReleaseRelationButton" value="{'Remove Relation'|i18n( 'extension/xrowgis' )}" title="{'Removes the GISObject Relation'|i18n( 'extension/xrowgis' )}" type="button">
</div>
        <div class="element mapContainer" style="float: right;">
            <div id="mapContainer" style="width: 200px; height: 200px;"></div>
        </div>
</div><!-- END AjaxUpdate -->
{else}
<fieldset>
<legend>{'Geographic location'|i18n( 'extension/xrowgis' )}</legend>
<br />
    <label>{'Longitude'|i18n( 'extension/xrowgis' )}:</label>
        <input onchange="jQuery('#editform').servemap( 'updateMap', {literal}{{/literal}'attr_id':{$attribute.id}, 'reverse':true, 'zoom':14{literal}}{/literal} );" id="xrowGIS-lon" class="box" size="32" type="text" name="ContentObjectAttribute_xrowgis_longitude_{$attribute.id}" size="12" value="{if is_set($attribute.content)}{$attribute.content.longitude}{/if}" />
    <label>{'Latitude'|i18n( 'extension/xrowgis' )}:</label>
        <input onchange="jQuery('#editform').servemap( 'updateMap', {literal}{{/literal}'attr_id':{$attribute.id}, 'reverse':true, 'zoom':14{literal}}{/literal} );" id="xrowGIS-lat" class="box" size="32" type="text" name="ContentObjectAttribute_xrowgis_latitude_{$attribute.id}" size="12" value="{if is_set($attribute.content)}{$attribute.content.latitude}{/if}" />
</fieldset>
<br />
<fieldset>
<legend>{'Address'|i18n( 'extension/xrowgis' )}</legend>
<br />
    <label>{'Street'|i18n( 'extension/xrowgis' )}:</label>
        <input onchange="jQuery('#editform').servemap( 'updateMap', {$attribute.id} );" id="xrowGIS-street-input" class="box" size="32"  type="text" name="ContentObjectAttribute_xrowgis_street_{$attribute.id}" size="12" value="{if is_set($attribute.content)}{$attribute.content.street}{/if}" />
    <label>{'ZIP'|i18n( 'extension/xrowgis' )}:</label>
        <input onchange="jQuery('#editform').servemap( 'updateMap', {$attribute.id} );" id="xrowGIS-zip-input" class="box" size="32" type="text" name="ContentObjectAttribute_xrowgis_zip_{$attribute.id}" size="12" value="{if is_set($attribute.content)}{$attribute.content.zip}{/if}" />
    <label>{'District'|i18n( 'extension/xrowgis' )}:</label>
        <input  class="box" size="32" id="xrowGIS-district-input" type="text" name="ContentObjectAttribute_xrowgis_district_{$attribute.id}" size="12" value="{if is_set($attribute.content)}{$attribute.content.district}{/if}" />
    <label>{'City'|i18n( 'extension/xrowgis' )}:</label>
        <input onchange="jQuery('#editform').servemap( 'updateMap', {$attribute.id} );" id="xrowGIS-city-input" class="box" size="32" type="text" name="ContentObjectAttribute_xrowgis_city_{$attribute.id}" size="12" value="{if is_set($attribute.content)}{$attribute.content.city}{/if}" />
    <label>{'State'|i18n( 'extension/xrowgis' )}:</label>
        <input onchange="jQuery('#editform').servemap( 'updateMap', {$attribute.id} );" id="xrowGIS-state-input" class="box" size="32" type="text" name="ContentObjectAttribute_xrowgis_state_{$attribute.id}" size="12" value="{if is_set($attribute.content)}{$attribute.content.state}{/if}" />
    <label>{'Country'|i18n( 'extension/xrowgis' )}:</label>
    {def $countries=fetch( 'content', 'country_list' )
         $class_content= $attribute.class_content
         $country = ''}
    {if is_set($attribute.content.country)}
        {set $country = $attribute.content.country}
    {/if}
    <select id="xrowGIS-country-input" onchange="jQuery('#editform').servemap( 'updateMap', {$attribute.id} );" name="ContentObjectAttribute_xrowgis_country_{$attribute.id}">
        <option value="">----</option>
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
{undef $countries
       $class_content
       $country}
</fieldset>
<br />
<input class="button uploadImage" type="button" name="ContentObjectAttribute_xrowgis[{$attribute.id}][object]" id="xrowgis_{$attribute.contentobject_id}_{$attribute.version}_objects_{$attribute.id}" value="{'Add Relation'|i18n( 'extension/xrowgis' )}" />
<input type="hidden" id="xrowgis_{$attribute.contentobject_id}_{$attribute.version}_objects_{$attribute.id}_url" value={concat( 'xrowgis/upload/', $attribute.contentobject_id, '/', $attribute.version, '/objects' )|ezurl()} />
<input onclick="jQuery().servemap( 'resetForm' );" class="button" type="button" name="ContentObjectAttribute_xrowgis[{$attribute.id}]"  value="{'Reset Form'|i18n( 'extension/xrowgis' )}" />
<input type="hidden" value="noRel" id="xrowGIS-rel" />
</div>
    <div class="element mapContainer" style="float: right;">
        <div id="mapContainer" style="width: 400px; height: 400px;"></div>
    </div>
    <div class="element recomContainer" style="float: left;">
        <div id="recomContainer" style="min-width: 200px; height: 150px; display:none;">
        <fieldset>
        <legend>{'Address proposal'|i18n( 'extension/xrowgis' )}</legend>
            <table>
                <tr>
                    <td><label>{'Street'|i18n( 'extension/xrowgis' )}:</label></td>
                    <td id="xrowGIS-street"></td>
                </tr>
                <tr>
                    <td><label>{'ZIP'|i18n( 'extension/xrowgis' )}:</label></td>
                    <td id="xrowGIS-zip"></td>
                </tr>
                <tr>
                    <td><label>{'District'|i18n( 'extension/xrowgis' )}:</label></td>
                    <td id="xrowGIS-district"></td>
                </tr>
                <tr>
                    <td><label>{'City'|i18n( 'extension/xrowgis' )}:</label></td>
                    <td id="xrowGIS-city"></td>
                </tr>
                <tr>
                    <td><label>{'State'|i18n( 'extension/xrowgis' )}:</label></td>
                    <td id="xrowGIS-state"></td>
                </tr>
            </table>
            </fieldset>
            <br />
            <input onclick="jQuery().servemap( 'takeOverAdress', {literal}{{/literal}'attributeID':{$attribute.id}{literal}}{/literal});" class="button" type="button" name="takeOver" value="{'Take-over Adress'|i18n( 'extension/xrowgis' )}" />
        </div>
    </div>
</div><!-- END AjaxUpdate -->
</div>
{/if}{*END if there is an relation to an Object which contains valid GIS Data*}
{def $latitude = $attribute.content.latitude}
{def $longitude = $attribute.content.longitude}

<script type="text/javascript" src="http://maps.google.com/maps/api/js?v=3.5&amp;sensor=false"></script>
<script type="text/javascript" src="http://openlayers.org/api/OpenLayers.js"></script>
<script type="text/javascript">
{literal}
    var options = {
        name:'{/literal}{ezini("GISSettings","Interface","gis.ini")}{literal}',
        lat:'{/literal}{$latitude}{literal}',
        lon: '{/literal}{$longitude}{literal}',
        zoom:'{/literal}{ezini(ezini("GISSettings","Interface","gis.ini"),"DefaultZoom","gis.ini")}{literal}',
        drag : '{/literal}{$dragable}{literal}'
        };
    jQuery(document).ready(jQuery().servemap( 'createMap', options ));
{/literal}
</script>