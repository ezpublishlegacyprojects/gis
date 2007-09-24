<?php
$Module = array( "name" => "GIS",
                 "variable_params" => true,
                 "function" => array(
                     "script" => "map.php",
                     "params" => array( ) ) );

$ViewList = array();
$ViewList["map"] = array(
    "script" => "map.php",
    'params' => array( 'NodeID' ) );
$ViewList["georssserver"] = array(
    "script" => "georssserver.php",
    'params' => array( 'RSSFeed', 'NodeID' ) );

$FunctionList['georssserver'] = array( );

?>
