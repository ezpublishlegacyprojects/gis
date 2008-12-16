<?php
//
// Definition of GoogleGeoCoder Methods
//
// GoogleGeoCoder Methods
//
// Created on: <08-Sep-2007 00:08:00 Norman Leutner>
// Last Updated: <08-Sep-2007 00:14:58 Norman Leutner>
// Version: 0.0.1
//
// Copyright (C) 2001-2007 all2e GmbH. All rights reserved.
//
// This source file is part of an extension for the eZ publish (tm)
// Open Source Content Management System.
//
// This file may be distributed and/or modified under the terms of the
// "GNU General Public License" version 2 (or greater) as published by
// the Free Software Foundation and appearing in the file LICENSE
// included in the packaging of this file.
//
// This file is provided AS IS with NO WARRANTY OF ANY KIND, INCLUDING
// THE WARRANTY OF DESIGN, MERCHANTABILITY AND FITNESS FOR A PARTICULAR
// PURPOSE.
//
// The "GNU General Public License" (GPL) is available at
// http://www.gnu.org/copyleft/gpl.html
//
// Contact info@all2e.com if any conditions
// of this licencing isn't clear to you.


include_once( "lib/ezxml/classes/ezxml.php" );

class GoogleGeoCoder extends GeoCoder 
{
    var $accuracy; // Google accuracy
    var $street;
    var $zip;
    var $city;
    var $state;
    var $country;
    var $longitude; // Dezimalgrad der geographischen Länge
    var $latitude; // Dezimalgrad der geographischen Breite
    var $phi; // Gogenma? der geographischen Länge
    var $theta; // Gogenma? der geographischen Breite
    
    function GoogleGeoCoder() 
    {
        parent::GeoCoder();
    }
    
    /*!
      Uses the google API to update the GeoCoder Object with a given search request
      If no valid data is found returns false.

CONSTANT GGeoAddressAccuracy

There are no symbolic constants defined for this enumeration.

0 	Unknown location. (Since 2.59)
1 	Country level accuracy. (Since 2.59)
2 	Region (state, province, prefecture, etc.) level accuracy. (Since 2.59)
3 	Sub-region (county, municipality, etc.) level accuracy. (Since 2.59)
4 	Town (city, village) level accuracy. (Since 2.59)
5 	Post code (zip code) level accuracy. (Since 2.59)
6 	Street level accuracy. (Since 2.59)
7 	Intersection level accuracy. (Since 2.59)
8 	Address level accuracy. (Since 2.59)

    */    
    function request()
    {
        $searchstring = array();
        if ( $this->street )
            $searchstring[] = $this->street;
        if ( $this->zip and $this->city)
            $searchstring[] = $this->zip . ' ' . $this->city;
        elseif ( $this->zip )
            $searchstring[] = $this->zip;
        elseif ( $this->city )
            $searchstring[] = $this->city;
        if ( $this->state )
            $searchstring[] = $this->state;
        if ( $this->country )
            $searchstring[] = $this->country;

        $searchstring = implode( ', ', $searchstring );
        // ini values
                $gisini = eZINI::instance( "gis.ini" );                          
                $key = $gisini->variable( "Google", "ApplicationID" );
                $url = $gisini->variable( "Google", "Url" );
               
                $requestUrl= $url."?q=".urlencode($searchstring)."&key=$key&output=xml";                

                eZDebug::writeDebug( $requestUrl, 'Google GeoCoder Request');
                //request the google kml result
                $kml = file ( $requestUrl );

                if ( !empty($kml[0]) )
                {
                    eZDebug::writeDebug( $kml[0], 'Google GeoCoder Response');
                    $xmldomxml = new eZXML();
                    $xmldom = $xmldomxml->domTree($kml[0]);

                    //API Manual: http://www.google.com/apis/maps/documentation/reference.html#GGeoStatusCode
                    $dom_statuscode = $xmldom->elementsByName( "code" );
                    $dom_statuscode = $dom_statuscode[0]->textContent();         

                    if ( $dom_statuscode=="200" ) 
                    {

                        //API Manual: http://www.google.com/apis/maps/documentation/reference.html#GGeoAddressAccuracy
                        $dom_adressdetails = $xmldom->elementsByName( "AddressDetails" ); 
                        $dom_accuracy = $dom_adressdetails[0]->get_attribute( "Accuracy" );
                        if ( in_array( $dom_accuracy, array( 8,7,6,5 ) ) )
                        {
                            $this->accuracy = 'GeoCoder::ACCURACY_STREET';
                        }
                        elseif ( in_array( $dom_accuracy, array( 4 ) ) )
                        {
                            $this->accuracy = 'GeoCoder::ACCURACY_CITY';
                        }
                        else
                        {
                            return false;
                        }
                        if ($xmldom->elementsByName( "ThoroughfareName" ))
                        {
                            $dom_street= $xmldom->elementsByName( "ThoroughfareName" );
                            $dom_street = $dom_street[0]->textContent();                                                
                        }

                        if ($xmldom->elementsByName( "PostalCodeNumber" ))
                        {
                            $dom_zip= $xmldom->elementsByName( "PostalCodeNumber" );
                            $dom_zip = $dom_zip[0]->textContent();                                                
                        }
                        
                        if ($xmldom->elementsByName( "LocalityName" ))
                        {
                            $dom_city = $xmldom->elementsByName( "LocalityName" );
                            $dom_city = $dom_city[0]->textContent();                                                
                        }

                        if ($xmldom->elementsByName( "AdministrativeAreaName" ))
                        {
                            $dom_state = $xmldom->elementsByName( "AdministrativeAreaName" );
                            $dom_state = $dom_state[0]->textContent();                                                
                        }
                        if ($xmldom->elementsByName( "CountryNameCode" ))
                        {
                            $dom_country = $xmldom->elementsByName( "CountryNameCode" );
                            $dom_country = $dom_country[0]->textContent();
                        }
                        

                        $dom_point = $xmldom->elementsByName( "coordinates" );
                        $dom_point = $dom_point[0]->textContent();

                        $dom_point = explode(",", $dom_point);
                        $dom_long = $dom_point[0];
                        $dom_lat = $dom_point[1];

                        // Map values to object
                        $this->accuracy = $dom_accuracy;
                        $this->street = $dom_street;
                        $this->zip = $dom_zip;
                        $this->city = $dom_city;
                        $this->state = $dom_state;
                        $this->country = $dom_country;
                        $this->longitude = $dom_long;
                        $this->latitude = $dom_lat;
                        $this->phi = deg2rad($dom_long);
                        $this->theta = deg2rad($dom_lat);   
                        return true;                     
                    }
                }
                else
                {
                    return false;
                }                 
    }
}
?>