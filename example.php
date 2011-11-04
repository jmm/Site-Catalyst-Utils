<?php

header( "Content-Type: text/plain; charset=UTF-8" );


require_once "custom_site_catalyst.php";


$sc_handler = new Custom_Site_Catalyst();

$sc_data = array();

// In the real world, article data might come from a CMS, database, etc.

$article = array(

  'id' => 123,

  'title' => strtolower( preg_replace( "/[^a-z0-9_ -]/i", "", "Stuff about something" ) )

);


$sc_data[ 'channel' ] = 'articles';

$sc_data[ 'pageName' ] = array( $sc_data[ 'channel' ], $article[ 'title' ] );


$sc_data = $sc_handler->delimit_vars( $sc_data );


$sc_data = array_merge( $sc_data, array(

  'prop1' => $sc_data[ 'channel' ],

  'prop2' => $sc_data[ 'channel' ],

  'prop3' => $sc_data[ 'channel' ],

  'prop4' => 'article',

  'prop5' => $article[ 'title' ],

  'prop6' => $article[ 'id' ],

  'hier1' => $sc_data[ 'channel' ],


  '_meta' => array(

    'event-serial-ids' => array(

      "event16-like" => $sc_handler->get_event_serial_id( array( 'description' => "event16-like" ) )

    ),
    // array


    'direct-output' => array( 'event-serial-ids' )

  )
  // array

) );


echo $sc_handler->get_page_code( $sc_data );

