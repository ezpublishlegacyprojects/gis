<?php

class YahooGeocoder extends GeoCoder
{

    function YahooGeocoder()
    {
        parent::GeoCoder();
    }

    function request()
    {
        $gisini = eZINI::instance( "gis.ini" );
        $appid = $gisini->variable( "Yahoo", "ApplicationID" );
        $url = $gisini->variable( "Yahoo", "Url" );
        
        $requestUrl = $url . "?appid=" . $appid;
        
        if ( strlen( $this->street ) > 1 )
        {
            $street_exp = explode( " ", $this->street );
            $street_komp = "";
            foreach ( $street_exp as $street_bit )
            {
                if ( $street_komp == "" )
                    $street_komp = "&street=" . $street_bit;
                else
                    $street_komp = $street_komp . "+" . $street_bit;
            }
            $requestUrl .= $street_komp;
        }
        
        if ( strlen( $this->city ) > 1 )
        {
            $city_exp = explode( " ", $this->city );
            $city_komp = "";
            foreach ( $city_exp as $city_bit )
            {
                if ( strlen( $city_komp ) == 0 )
                {
                    $city_komp = "&city=" . $city_bit;
                }
                
                else
                    $city_komp = $city_komp . "+" . $city_bit;
            }
            $requestUrl .= $city_komp;
        }
        
        if ( strlen( $this->zip ) > 1 )
        {
            $requestUrl .= "&zip=" . $this->zip;
        }
        if ( strlen( $this->state ) > 1 )
        {
            $state_exp = explode( " ", $this->state );
            $state_komp = "";
            foreach ( $state_exp as $state_bit )
            {
                if ( strlen( $state_komp ) == 0 )
                {
                    $state_komp = "&state=" . $state_bit;
                }
                
                else
                    $state_komp = $state_komp . "+" . $state_bit;
            }
            $requestUrl .= $state_komp;
        }
        
        if ( ! empty( $this->city ) or ! empty( $this->state ) )
        {
            eZDebug::writeDebug( $requestUrl, 'Yahoo GeoCoder Request' );
            $xml = file( $requestUrl );
            if ( ! empty( $xml[1] ) )
            {
                
                $xmldom = new DOMDocument( '1.0', 'utf-8' );
                eZDebug::writeDebug( $xml[1], 'Yahoo GeoCoder Response' );
                $xmldom->loadXML( $xml[1] );
                $Result = $xmldom->getElementsByTagName( "Result" );
                $this->accuracy = $Result->item( 0 )->getAttribute( "precision" );
                if ( $this->accuracy != GeoCoder::ACCURACY_ZIP and $this->accuracy != GeoCoder::ACCURACY_STREET and $this->accuracy != GeoCoder::ACCURACY_CITY )
                {
                    return false;
                }
                $dom_long = $xmldom->getElementsByTagName( "Longitude" );
                $this->longitude = $dom_long->item( 0 )->nodeValue;
                
                $dom_lat = $xmldom->getElementsByTagName( "Latitude" );
                $this->latitude = $dom_lat->item( 0 )->nodeValue;
                
                $dom_street = $xmldom->getElementsByTagName( "Address" );
                $this->street = $dom_street->item( 0 )->nodeValue;
                
                $dom_city = $xmldom->getElementsByTagName( "City" );
                $this->city = $dom_city->item( 0 )->nodeValue;
                
                $dom_zip = $xmldom->getElementsByTagName( "Zip" );
                $this->zip = $dom_zip->item( 0 )->nodeValue;
                
                $dom_state = $xmldom->getElementsByTagName( "State" );
                $this->state = $dom_state->item( 0 )->nodeValue;
                
                $dom_country = $xmldom->getElementsByTagName( "Country" );
                $this->country = $dom_country->item( 0 )->nodeValue;
                
                return true;
            }
        }
        return false;
    }
}
?>