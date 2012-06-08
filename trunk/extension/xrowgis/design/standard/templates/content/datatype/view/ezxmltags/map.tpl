{def $proxy = ezini("GISSettings","proxy","xrowgis.ini")
     $maptype = "RSSMap"
     $point = hash('width', 24, 'height', 32, 'xoffset', 0, 'yoffset', 0, 'image', 'extension/hannover/design/hannover/images/openlayers-custom/marker.png')
     $css="extension/hannover/design/hannover/stylesheets/openlayers-custom.css"
}
     
{def $url_array = $url|explode('://')}
{if $url_array.0|eq('eznode')}
    {set $url = concat('xrowgis/georss/', $url_array.1)|ezurl('no', 'full')}
{/if}
{if is_set($div)|not}
    {def $div = 'mapContainer'}
{/if}
<!-- map content: START -->
<div class="element mapContainer" class="custom_map">
    <div class="XROWMap" id="{$div}" style="width: 400px; height: 400px;"
    data-layer="{if is_set($layer)}{$layer}{else}OSM{/if}"
    data-maptype="{if is_set($maptype)}{$maptype}{/if}"
    data-div="{$div}"
    data-lat="{if is_set($lat)}{$lat}{else}{ezini("GISSettings","latitude","xrowgis.ini")}{/if}"
    data-lon="{if is_set($lon)}{$lon}{else}{ezini("GISSettings","longitude","xrowgis.ini")}{/if}"
    data-zoom="{if is_set($zoom)}{$zoom}{else}{ezini("GISSettings","zoom","xrowgis.ini")}{/if}"
    data-url="{if is_set($url)}{$url}{else}false{/if}"
    data-proxy="{if is_set($proxy)}{$proxy}{else}false{/if}"
    data-css="{if is_set($css)}{$css|ezroot(no, full)}{else}false{/if}"
    data-drag="{if is_set($drag)}{$drag}{else}false{/if}"
    data-width="{$point.width|wash()}"
    data-height="{$point.height|wash()}"
    data-xoffset="{$point.xoffset|wash()}"
    data-yoffset="{$point.yoffset|wash()}"
    data-image="{if is_set($point.image)}{$point.image|ezroot(no, full)}{else}false{/if}"
    ></div>
</div>
<!-- map content: END -->