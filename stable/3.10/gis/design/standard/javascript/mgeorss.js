// MGeoRSS: GMaps API extension
// copyright 2006 Mikel Maron (email: mikel_maron yahoo com)
// http://brainoff.com/gmaps/mgeorss.html
// This work is public domain

function MGeoRSS(){}
MGeoRSS.prototype.initialize=function(map) {
    	this.map = map;
    	this.rssurl = false;
    	this.request = false;

	this.divhook = this.map.ownerDocument.createElement('div');
	this.divhook.id = "MGeoRSSHook";
	this.divhook.m = this;
	this.map.div.parentNode.appendChild(this.divhook);
}
MGeoRSS.prototype.load=function(url,proxyurl) {

	if (this.request != false) { return; }
 	this.rssurl = url;
        this.request = GXmlHttp.create();
	if (proxyurl != undefined) {
      		this.request.open("GET",proxyurl + this.rssurl,true);
	} else {
		this.request.open("GET",this.rssurl, true);
	}
	this.request.onreadystatechange = mgeorss_cb;
	this.request.send(null);
}
function mgeorss_cb() {
	e = document.getElementById("MGeoRSSHook");
	e.m.callback();
}
MGeoRSS.prototype.callback = function() {
	if (this.request.readyState == 4) {
		if (this.request.status == "200") {
			var xmlDoc = this.request.responseXML;
			var items = xmlDoc.documentElement.getElementsByTagName("item");
			for (var i = 0; i < items.length; i++) {
				try {
					var marker = this.createMarker(items[i]);
					this.map.addOverlay(marker);
				} catch (e) {
				}
			}
		}
		this.request = false;
	}
}
MGeoRSS.prototype.createMarker = function(item) {
	var title = item.getElementsByTagName("title")[0].childNodes[0].nodeValue;
	var description = item.getElementsByTagName("description")[0].childNodes[0].nodeValue;
	var link = item.getElementsByTagName("link")[0].childNodes[0].nodeValue;

	/* namespaces are handled by spec in moz, not in ie */
	if (navigator.userAgent.toLowerCase().indexOf("msie") < 0) {
		var lat = item.getElementsByTagNameNS("http://www.w3.org/2003/01/geo/wgs84_pos#","lat")[0].childNodes[0].nodeValue;
		var lng = item.getElementsByTagNameNS("http://www.w3.org/2003/01/geo/wgs84_pos#","long")[0].childNodes[0].nodeValue;
	} else {
		var lat = item.getElementsByTagName("geo:lat")[0].childNodes[0].nodeValue;
		var lng = item.getElementsByTagName("geo:long")[0].childNodes[0].nodeValue;
	}

	var point = new GPoint(parseFloat(lng), parseFloat(lat));
	var marker = new GMarker(point);
	var html = "<a href=\"" + link + "\">" + title + "</a><p/>" + description;
	GEvent.addListener(marker, "click", function() {
		marker.openInfoWindowHtml(html);
	});
	return marker;
}
GMap.prototype.addMGeoRSS=function(a) {
    a.initialize(this);
}

/* GeoRSS Simple */

function MGeoRSSSimple(){}
MGeoRSSSimple.prototype.initialize=function(map) {
    	this.map = map;
    	this.rssurl = false;
    	this.request = false;

	this.divhook = this.map.ownerDocument.createElement('div');
	this.divhook.id = "MGeoRSSSimpleHook";
	this.divhook.m = this;
	this.map.div.parentNode.appendChild(this.divhook);
}
MGeoRSSSimple.prototype.load=function(url,proxyurl) {
	if (this.request != false) { return; }
 	this.rssurl = url;
        this.request = GXmlHttp.create();
	if (proxyurl != undefined) {
      		this.request.open("GET",proxyurl + this.rssurl,true);
	} else {
		this.request.open("GET",this.rssurl, true);
	}
	this.request.onreadystatechange = mgeorsssimple_cb;
	this.request.send(null);
}
function mgeorsssimple_cb() {
	e = document.getElementById("MGeoRSSSimpleHook");
	e.m.callback();
}

MGeoRSSSimple.prototype.callback = function() {
	if (this.request.readyState == 4) {
		if (this.request.status == "200") {
			var xmlDoc = this.request.responseXML;
			var items = xmlDoc.documentElement.getElementsByTagName("entry");
			for (var i = 0; i < items.length; i++) {
				try {
					var marker = this.createMarker(items[i]);
					this.map.addOverlay(marker);
				} catch (e) {
				}
			}
		}
		this.request = false;
	}
}
MGeoRSSSimple.prototype.createMarker = function(item) {
	var title = item.getElementsByTagName("title")[0].childNodes[0].nodeValue;
	var description = item.getElementsByTagName("summary")[0].childNodes[0].nodeValue;
//	var link = item.getElementsByTagName("link")[0].childNodes[0].nodeValue;
	var link = "";
	/* namespaces are handled by spec in moz, not in ie */
	if (navigator.userAgent.toLowerCase().indexOf("msie") < 0) {
		var latlng = item.getElementsByTagNameNS("http://www.georss.org/georss","point")[0].childNodes[0].nodeValue;
	} else {
		var latlng = item.getElementsByTagName("georss:point")[0].childNodes[0].nodeValue;
	}
	alert(latlng );
	latlng = latlng.split(" ");
  var lat = latlng[0];
  var lng = latlng[1];
	var point = new GPoint(parseFloat(lng), parseFloat(lat));
	var marker = new GMarker(point);
	var html = "<a href=\"" + link + "\">" + title + "</a><p/>" + description;
	GEvent.addListener(marker, "click", function() {
		marker.openInfoWindowHtml(html);
	});
	return marker;
}
GMap.prototype.addMGeoRSSSimple=function(a) {
    a.initialize(this);
}

