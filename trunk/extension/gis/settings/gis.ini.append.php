<?php /* #?ini charset="utf-8"?

[GISSettings]
#Choose Yahoo or Google
#Yahoo is easier to debug on the local intranet.
Interface=Yahoo
#GeocoderURL=http://www.maporama.com
#GeocoderURL=http://brainoff.com/geocoder/

PublicURL=http://www.example.com:81

[Yahoo]
ApplicationID=150fEujV34Ew4dTws3VmzZrQcWFbhUFtTO3KNJDOczcY9slBTMiOXtcUEIWgkRNZhiTjQg--
Url=http://local.yahooapis.com/MapsService/V1/geocode
# ZOOM choose level 1 - 16
#2: street level
#4: city level
#8: state level
DefaultZoom=8

[Google]
Url=http://maps.google.com/maps/geo
Proxy=
# enable proxy to load remote feeds
# urls must be under the same domain
# otherwise you will get JS permission errors
# E.g. in firefox
#Proxy=http://example.com/projects/map/proxy.pl?
ApplicationID=ABQIAAAAPF7DvntGNCZxs6lLUT8lhRS7IaKw0QFjSoA-ElwZla6ORVM4YBSiRKIy0ivhS1xQIc0oW7hQnNR1ng
# ZOOM choose level 1 - 16
DefaultZoom=16

# Special icons for special classes
# [Icon]
# using the class_id you can overwrite the BaseIcons for each class individual
# Important: if you choose another icon, please take care that each width and height is set
# folder[]
# folder[path]=/share/icons/crystal-admin/32x32/filesystems/folder.png
# folder[height]=16
# folder[width]=16
*/ ?>