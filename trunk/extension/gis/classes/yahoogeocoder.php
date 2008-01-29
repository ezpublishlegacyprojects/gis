<?php

include_once( "extension/gis/classes/geocoder.php" );

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
				$url=$gisini->variable( "Yahoo", "Url" );
				
				$requestUrl= $url."?appid=".$appid;
				
				if ( strlen ( $this->street ) > 1 )
				{
					$street_exp = explode(" ", $this->street );
					$street_komp="";
					foreach ($street_exp as $street_bit) 
					{
						if( $street_komp == "")
							$street_komp = "&street=".$street_bit;
						else 
							$street_komp = $street_komp."+".$street_bit;
					}
					$requestUrl .= $street_komp;
				}
					
				if ( strlen( $this->city ) > 1 )
				{
					$city_exp = explode(" ", $this->city );
					$city_komp="";
					foreach ($city_exp as $city_bit) 
					{
						if( strlen ($city_komp) == 0)
						{
							$city_komp = "&city=".$city_bit;
						}
							
						else 
							$city_komp = $city_komp."+".$city_bit;
					}
					$requestUrl .= $city_komp;
				}
				 
				if ( strlen( $this->zip ) > 1 )
				{
					$requestUrl .= "&zip=" . $this->zip;
				}
				if ( strlen( $this->state ) > 1 )
				{
					$state_exp = explode(" ", $this->state );
					$state_komp="";
					foreach ($state_exp as $state_bit) 
					{
						if( strlen ($state_komp) == 0)
						{
							$state_komp = "&state=".$state_bit;
						}
							
						else 
							$state_komp = $state_komp."+".$state_bit;
					}
					$requestUrl .= $state_komp;
				}
				
				
				if (!empty( $this->city ) OR !empty( $this->state ) )
				{
				    eZDebug::writeDebug( $requestUrl, 'Yahoo GeoCoder Request');
					$xml = file ( $requestUrl );
					if ( !empty($xml[1]) )
					{
					
						$xmldomxml = new eZXML();
						eZDebug::writeDebug( $xml[1], 'Yahoo GeoCoder Response');
						$xmldom =  $xmldomxml->domTree($xml[1]);
						$Result = $xmldom->elementsByName( "Result" );
		        		$this->accuracy = $Result[0]->attributeValue( "precision" );
                        if ( $this->accuracy != GeoCoder::ACCURACY_ZIP and $this->accuracy != GeoCoder::ACCURACY_STREET and $this->accuracy != GeoCoder::ACCURACY_CITY )
                        {
                            return false;
                        }
						$dom_long = $xmldom->elementsByName( "Longitude" );
		        		$this->longitude = $dom_long[0]->textContent();
		        		
		        		$dom_lat = $xmldom->elementsByName( "Latitude" );
		        		$this->latitude = $dom_lat[0]->textContent();
						
						$dom_street = $xmldom->elementsByName( "Address" );
		        		$this->street = $dom_street[0]->textContent();
		        		
		        		$dom_city = $xmldom->elementsByName( "City" );
		        		$this->city = $dom_city[0]->textContent();
		        		
		        		$dom_zip = $xmldom->elementsByName( "Zip" );
		        		$this->zip = $dom_zip[0]->textContent();
		        		
		        		$dom_state =& $xmldom->elementsByName( "State" );
		        		$this->state = $dom_state[0]->textContent();
		        		
		        		$dom_country =& $xmldom->elementsByName( "Country" );
		        		$this->country = $dom_country[0]->textContent();

					    return true;
					}
				}
				return false;
	}
}
?>