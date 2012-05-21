{def $url_array = $url|explode('://')}
{if $url_array.0|eq('eznode')}
{set $url = concat('xrowgis/georss/', $url_array.1)|ezurl('no', 'full')}
{/if}
<div class="element mapContainer" style="float: right;">
    <div id="mapContainer" style="width: 400px; height: 400px;"></div>
</div>
<script type="text/javascript"
    src="http://maps.google.com/maps/api/js?v=3.6&amp;sensor=false"></script>
<script type="text/javascript">
{literal}
    var options = {
        name:'OpenLayers',
        lat:'{/literal}{ezini("GISSettings","latitude","xrowgis.ini")}{literal}',
        lon:'{/literal}{ezini("GISSettings","longitude","xrowgis.ini")}{literal}',
        zoom:'{/literal}{ezini("GISSettings","zoom","xrowgis.ini")}{literal}',
        proxy:'{/literal}{ezini("GISSettings","proxy","xrowgis.ini")}{literal}',
        url:'{/literal}{$url}{literal}',
        drag : false
        };
    jQuery(document).ready(jQuery().servemap( 'createRSSMap', options ));
{/literal}
</script>