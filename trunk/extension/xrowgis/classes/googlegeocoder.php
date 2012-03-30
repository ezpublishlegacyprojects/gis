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


class GoogleGeoCoder extends GeoCoder
{
    public $accuracy; // Google accuracy
    public $street;
    public $zip;
    public $city;
    public $state;
    public $country;
    public $longitude; // Dezimalgrad der geographischen Länge
    public $latitude; // Dezimalgrad der geographischen Breite
    private $phi; // Gogenma? der geographischen Länge
    private $theta; // Gogenma? der geographischen Breite

    
    function GoogleGeoCoder()
    {
        parent::GeoCoder();
    }

    /*!
      Uses the google API to update the GeoCoder Object with a given search request
      If no valid data is found returns false.

CONSTANT GGeoAddressAccuracy

There are no symbolic constants defined for this enumeration.

0     Unknown location. (Since 2.59)
1     Country level accuracy. (Since 2.59)
2     Region (state, province, prefecture, etc.) level accuracy. (Since 2.59)
3     Sub-region (county, municipality, etc.) level accuracy. (Since 2.59)
4     Town (city, village) level accuracy. (Since 2.59)
5     Post code (zip code) level accuracy. (Since 2.59)
6     Street level accuracy. (Since 2.59)
7     Intersection level accuracy. (Since 2.59)
8     Address level accuracy. (Since 2.59)

    */
    function request()
    {
        $searchstring = array();
        if ( $this->country )
            $searchstring[] = $this->country;
        if ( $this->state )
            $searchstring[] = $this->state;
        if ( $this->street )
            $searchstring[] = $this->street;
        if ( $this->zip and $this->city )
            $searchstring[] = $this->zip . ' ' . $this->city;
        elseif ( $this->zip )
            $searchstring[] = $this->zip;
        elseif ( $this->city )
            $searchstring[] = $this->city;
        
        $searchstring = implode( ' ', $searchstring );
        // ini values
        $gisini = eZINI::instance( "xrowgis.ini" );
        $key = $gisini->variable( "Google", "ApplicationID" );
        $url = $gisini->variable( "Google", "Url" );
        
        $requestUrl = $url . "?q=" . urlencode( $searchstring ) . "&key=$key&output=xml&sensor=false";
        
        eZDebug::writeDebug( $requestUrl, 'Google GeoCoder Request' );
        //request the google kml result
        $kml = file_get_contents( $requestUrl );
        
        if ( ! empty( $kml ) )
        {
            //eZDebug::writeDebug( $kml, 'Google GeoCoder Response');
            $xmldom = new DOMDocument( '1.0', 'utf-8' );
            $xmldom->loadXML( utf8_encode( $kml ) );
            
            //API Manual: http://www.google.com/apis/maps/documentation/reference.html#GGeoStatusCode
            $dom_statuscode = $xmldom->getElementsByTagName( "code" );
            if ( is_object( $dom_statuscode->item( 0 ) ) )
            {
                $dom_statuscode = $dom_statuscode->item( 0 )->nodeValue;
            }
            else
            {
                return false;
            }
            if ( $dom_statuscode == "200" )
            {
                //API Manual: http://www.google.com/apis/maps/documentation/reference.html#GGeoAddressAccuracy
                $dom_adressdetails = $xmldom->getElementsByTagName( "AddressDetails" );
                $dom_accuracy = $dom_adressdetails->item( 0 )->attributes->getNamedItem( "Accuracy" )->nodeValue;
                if ( in_array( $dom_accuracy, array( 
                    8 , 
                    7 , 
                    6 , 
                    5 
                ) ) )
                {
                    $this->accuracy = GeoCoder::ACCURACY_STREET;
                }
                elseif ( in_array( $dom_accuracy, array( 
                    4 
                ) ) )
                {
                    $this->accuracy = GeoCoder::ACCURACY_CITY;
                }
                else
                {
                    return false;
                }
                if ( $xmldom->getElementsByTagName( "ThoroughfareName" ) )
                {
                    $dom_street = $xmldom->getElementsByTagName( "ThoroughfareName" );
                    
                    if ( is_object( $dom_street->item( 0 ) ) )
                        $dom_street = $dom_street->item( 0 )->nodeValue;
                    else
                        $dom_street = "";
                }
                
                if ( $xmldom->getElementsByTagName( "PostalCodeNumber" ) )
                {
                    $dom_zip = $xmldom->getElementsByTagName( "PostalCodeNumber" );
                    
                    if ( is_object( $dom_zip->item( 0 ) ) )
                        $dom_zip = $dom_zip->item( 0 )->nodeValue;
                    else
                        $dom_zip = "";
                }
                
                if ( $xmldom->getElementsByTagName( "LocalityName" ) )
                {
                    $dom_city = $xmldom->getElementsByTagName( "LocalityName" );
                    
                    if ( is_object( $dom_city->item( 0 ) ) )
                        $dom_city = $dom_city->item( 0 )->nodeValue;
                    else
                        $dom_city = "";
                }
                
                if ( $xmldom->getElementsByTagName( "AdministrativeAreaName" ) )
                {
                    $dom_state = $xmldom->getElementsByTagName( "AdministrativeAreaName" );
                    
                    if ( is_object( $dom_state->item( 0 ) ) )
                        $dom_state = $dom_state->item( 0 )->nodeValue;
                    else
                        $dom_state = "";
                }
                if ( $xmldom->getElementsByTagName( "CountryNameCode" ) )
                {
                    $dom_country = $xmldom->getElementsByTagName( "CountryNameCode" );
                    
                    if ( is_object( $dom_country->item( 0 ) ) )
                        $dom_country = $dom_country->item( 0 )->nodeValue;
                    else
                        $dom_country = "";
                }
                
                $dom_point = $xmldom->getElementsByTagName( "coordinates" );
                $dom_point = $dom_point->item( 0 )->nodeValue;
                
                $dom_point = explode( ",", $dom_point );
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
                $this->phi = deg2rad( $dom_long );
                $this->theta = deg2rad( $dom_lat );
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
