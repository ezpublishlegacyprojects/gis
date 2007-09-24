[GISSettings]
ApplicationID=YahooDemo
GeocoderURL=http://www.maporama.com
#GeocoderURL=http://brainoff.com/geocoder/

PublicURL=http://www.example.com:81

[Yahoo]
ApplicationID=YahooDemo
Url=http://api.local.yahoo.com/MapsService/V1/geocode
# ZOOM choose level 1 - 16
#2: street level
#4: city level
#8: state level
DefaultZoom=1

[Google]
Proxy=
# enable proxy to load remote feeds
# urls must be under the same domain
# otherwise you will get JS permission errors
# E.g. in firefox
#Proxy=http://example.com/projects/map/proxy.pl?
ApplicationID=
# ZOOM choose level 1 - 16
DefaultZoom=1

# Special icons for special classes
# [Icon]
# using the class_id you can overwrite the BaseIcons for each class individual
# Important: if you choose another icon, please take care that each width and height is set
# folder[]
# folder[path]=/share/icons/crystal-admin/32x32/filesystems/folder.png
# folder[height]=16
# folder[width]=16
