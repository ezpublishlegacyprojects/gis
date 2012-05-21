<?php

class xrowGEORSS
{
    public $nodeID;
    public $feed;

    function __construct( $nodeID )
    {
        $this->nodeID = $nodeID;
        self::generateGEORSSFeed();
    }

    function generateGEORSSFeed()
    {
        $treeNodes = self::fetchTreeNode();
        $parent = self::fetchParent();
        $this->feed = new ezcFeed();
        
        $this->feed->generator = eZSys::serverURL();
        $link = '/xrowgis/georssserver/' . $this->nodeID;
        $this->feed->id = self::transformURI( $link, true, 'full' );
        $this->feed->title = $parent->attribute( 'name' );
        $this->feed->link = eZSys::serverURL();
        $this->feed->description = 'Hannover.de GEORSS Feed Channel';
        $this->feed->language = eZLocale::currentLocaleCode();
        
        foreach ( $treeNodes as $node )
        {
            $item = $this->feed->add( 'item' );
            $item->title = $node->getName();
            $link = $node->attribute( 'url_alias' );
            $item->link = self::transformURI( $link, true, 'full' );
            $item->id = self::transformURI( $link, true, 'full' );
            
            $dm = $node->dataMap();
            
            foreach ( $dm as $attribute )
            {
                
                if ( $attribute->attribute( 'data_type_string' ) == 'ezxmltext' || $attribute->attribute( 'data_type_string' ) == 'eztext' )
                {
                    if ( empty( $item->description ) )
                    {
                        if ( $attribute->attribute( 'data_type_string' ) == 'ezxmltext' )
                        {
                            $outputHandler = new eZXHTMLXMLOutput( $attribute->attribute( 'data_text' ), false, $attribute );
                            $htmlContent = $outputHandler->outputText();
                            $item->description = htmlspecialchars( trim( $htmlContent ) );
                        }
                        else
                        {
                            $item->description = htmlspecialchars( $attribute->attribute( 'content' ) );
                        }
                    }
                    else
                    {
                        continue;
                    }
                }
                if ( $attribute->attribute( 'data_type_string' ) == xrowGIStype::DATATYPE_STRING && $attribute->attribute( 'has_content' ) && ( $attribute->attribute( 'content' )->latitude != 0 || $attribute->attribute( 'content' )->longitude != 0 ) )
                {
                    ezcFeed::registerModule( 'GeoRss', 'ezcFeedGeoModule', 'georss' );
                    $module = $item->addModule( 'GeoRss' );
                    $module->lat = $attribute->attribute( 'content' )->latitude;
                    $module->long = $attribute->attribute( 'content' )->longitude;
                }
            }
        }
        return $this->feed;
    }

    function fetchTreeNode()
    {
        $params = array();
        $params['ClassFilterType'] = 'include';
        $params['ClassFilterArray'] = self::getClasses();
        
        return eZContentObjectTreeNode::subTreeByNodeID( $params, $this->nodeID );
    }

    function fetchParent()
    {
        return eZContentObject::fetchByNodeID( $this->nodeID );
    }

    function getClasses()
    {
        $db = eZDB::instance();
        $sql = "SELECT DISTINCT I.contentclass_id, N.identifier FROM `ezcontentclass_attribute` AS I INNER JOIN `ezcontentclass` AS N On I.contentclass_id = N.id WHERE I.data_type_string ='" . xrowGIStype::DATATYPE_STRING . "'";
        
        $results = $db->arrayQuery( $sql );
        $retVal = array();
        
        foreach ( $results as $key => $result )
        {
            $retVal[] = $results[$key]['identifier'];
        }
        
        return $retVal;
    }

    function transformURI( $href, $ignoreIndexDir = false, $serverURL = null )
    {
        if ( $serverURL === null )
        {
            $serverURL = ezu::$transformURIMode;
        }
        
        if ( preg_match( "#^[a-zA-Z0-9]+:#", $href ) || substr( $href, 0, 2 ) == '//' )
            return false;
        
        if ( strlen( $href ) == 0 )
            $href = '/';
        else 
            if ( $href[0] == '#' )
            {
                $href = htmlspecialchars( $href );
                return true;
            }
            else 
                if ( $href[0] != '/' )
                {
                    $href = '/' . $href;
                }
        
        $sys = eZSys::instance();
        $dir = ! $ignoreIndexDir ? $sys->indexDir() : $sys->wwwDir();
        $serverURL = $serverURL === 'full' ? $sys->serverURL() : '';
        $href = $serverURL . $dir . $href;
        if ( ! $ignoreIndexDir )
        {
            $href = preg_replace( "#^(//)#", "/", $href );
            $href = preg_replace( "#(^.*)(/+)$#", "\$1", $href );
        }
        $href = str_replace( '&amp;amp;', '&amp;', htmlspecialchars( $href ) );
        
        if ( $href == "" )
            $href = "/";
        
        return $href;
    }

}
?>