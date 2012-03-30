<html>
<head>
<script src="http://maps.google.com/maps?file=api&v=1&key={ezini("Google", "ApplicationID", "xrowgis.ini")}" type="text/javascript"></script>
<script type="text/javascript" src={"javascript/mgeorss.js"|ezdesign}></script>
{literal}
<style type="text/css">
body
{
	margin: 0;
	border: 0;
	padding: 0;
}
#mapContainer {
    height: 400px;
    width: 500px;
}
</style>
{/literal}
</head>
<body>



<div id="mapContainer"></div>

<script type="text/javascript">
//<![CDATA[

map = new GMap(document.getElementById("mapContainer"));

// Atom only? just an other format loader
//georss = new MGeoRSSSimple();
//map.addMGeoRSSSimple(georss);

georss = new MGeoRSS();
map.addMGeoRSS(georss);

map.addControl(new GLargeMapControl());
map.addControl(new GMapTypeControl());
map.centerAndZoom(new GPoint(0, 0), {ezini("Yahoo","DefaultZoom","xrowgis.ini")});

{if ezini("Google","Proxy","xrowgis.ini")|not}

//, $module_result.view_parameters.rss
//georss.load('{concat( ezini("GISSettings","PublicURL","xrowgis.ini"), '/var/storage/GeoRSSSimple.xml' )}');

georss.load('{concat( ezini("GISSettings","PublicURL","xrowgis.ini"), '/xrowgis/georssserver/', $module_result.view_parameters.rss )}');

{else}

georss.load('{concat( ezini("GISSettings","PublicURL","xrowgis.ini"), '/xrowgis/georssserver/', $module_result.view_parameters.rss )}', '{ezini("Google","Proxy","xrowgis.ini")}');

{/if}

//georss.load('{concat( ezini("GISSettings","PublicURL","xrowgis.ini"), '/var/storage/eqs7day-M5.xml')}');

//]]>
</script>

</body>
</html>