<?php

/*

Omniture SiteCatalyst deployment utilities.  Preprocessing of SiteCatalyst variable values and generation of SiteCatalyst JavaScript code.  Version 1.0.

Copyright 2009 Jesse McCarthy <http://jessemccarthy.net/>.

This file is part of JMM SiteCatalyst Utils (the "Software").

The Software is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

The Software is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with the Software.  If not, see <http://www.gnu.org/licenses/>.

*/


class JMM_Site_Catalyst {

  var $method_data;

  var $delimiters;

  var $default_params;

  var $js_var = 's';


  function JMM_Site_Catalyst() {

    $this->method_data = array();

    $this->default_params = array( 'variables' => array(), 'metadata' => array() );

    $this->delimiters = array();


    return;

  }
  // JMM_Site_Catalyst


  function get_delimiters() {

    return $this->delimiters;

  }
  // get_delimiters


  function set_delimiters( $delimiters ) {

    $this->delimiters = $delimiters;


    return;

  }
  // set_delimiters


  function set_default_params( $params ) {

    foreach ( $params as $key => $value ) {

      $this->default_params[ $key ] = $value;

    }
    // foreach


    return;

  }
  // set_default_params


  function delimit_var( $variable, $value ) {

    $delimiters = $this->get_delimiters();

    if ( ( $delimiter = $delimiters[ $variable ] ) && is_array( $value ) ) {

      foreach ( $value as $v_key => $segment ) {

        $value[ $v_key ] = str_replace( $delimiter, "", $segment );

      }
      // foreach

      $value = join( $delimiter, $value );

    }
    // if


    return $value;

  }
  // delimit_var


  function delimit_vars( $variables ) {

    foreach ( $variables as $variable => $value ) {

      $variables[ $variable ] = $this->delimit_var( $variable, $value );

    }
    // foreach


    return $variables;

  }
  // delimit_vars


  function get_event_serial_id( $params = array() ) {

    $max_chars = 20;

    $micro_time = microtime();

    $time = explode( " ", $micro_time );

    $time = $time[1];

    $alpha_num = strtoupper( str_shuffle( md5( "{$micro_time}{$_SERVER[ 'REQUEST_URI' ]}{$_SERVER[ 'REMOTE_ADDR' ]}{$_SERVER[ 'HTTP_USER_AGENT' ]}{$params[ 'description' ]}" ) ) );

    $remaining_chars = ( $max_chars - strlen( $time ) );

    $offset = mt_rand( 0, ( strlen( $alpha_num ) - $remaining_chars ) );

    $serial_id = ( $time . substr( $alpha_num, $offset, $remaining_chars ) );


    return $serial_id;

  }
  // get_event_serial_id


  function prepare_pagename_segment( $data ) {

    $delimiters = $this->get_delimiters();

    $delimiter = $delimiters[ 'pageName' ];

    $data = strtolower( $data );

    $data = preg_replace(

      array( "/ +/", "@[{$delimiter}]@" ),

      array( " ", "" ),

      $data

    );
    // preg_replace


    return $data;

  }
  // prepare_pagename_segment


  function set_prepare_page_code_variables_data( $data ) {

    $data_for = 'prepare_page_code_variables';

    if ( $this->method_data[ $data_for ] ) {

      return;

    }
    // if


    /* start search_replace_chars */

    $data_item = 'search_replace_chars';

    // Groups of characters that must be removed from SiteCatalyst variables
    $search_replace_char_groups = array();


    $search_replace_char_groups[ 'sc_illegal' ] = array(

      'search' => array( "\t", "\r", "\n" ),

      'replace' => array( "", "", "" )

    );
    // array


    // HTML and JS significant characters

    $search_replace_char_groups[ 'html_js' ] = array(

      'search' => array( "&", "<", ">", '"' ),

      'replace' => array( "\\x26", "\\x3C", "\\x3E", "\\x22" )

    );


    $chars = array(

      "\x82" => "'",

      "\x84" => '"',

      "\x85" => "...",

      "\x88" => "^",

      "\x8B" => "<",

      "\x91" => "'",

      "\x92" => "'",

      "\x93" => '"',

      "\x94" => '"',

      "\x96" => "--",

      "\x97" => "--",

      "\x98" => "~",

      "\x9B" => ">"

    );
    // array

    $search_replace_char_groups[ 'cp1252' ] = array( 'search' => array_keys( $chars ), 'replace' => array_values( $chars ) );


    $search_replace_chars = array( 'search' => array(), 'replace' => array() );

    // Order is significant here

    foreach ( array( 'cp1252', 'html_js', 'sc_illegal' ) as $char_group ) {

      $char_group = $search_replace_char_groups[ $char_group ];

      foreach ( array_keys( $search_replace_chars ) as $src_key ) {

        $search_replace_chars[ $src_key ] = array_merge( $search_replace_chars[ $src_key ], $char_group[ $src_key ] );

      }
      // foreach

    }
    // foreach


    $this->method_data[ $data_for ][ 'search_replace_chars' ] = $search_replace_chars;


    unset( $chars, $search_replace_char_groups );




    /* start regex_search_replace */

    $data_item = 'regex_search_replace';

    $regex_search_replace = array( 'search' => array(), 'replace' => array() );

    if ( strtoupper( $data[ $data_item ][ 'charset' ] ) == 'ISO-8859-1' ) {

      $regex_search_replace[ 'search' ][] = "<[^\x20-\xFF]>";

      $regex_search_replace[ 'replace' ][] = "";

    }
    // if


    $this->method_data[ $data_for ][ $data_item ] = $regex_search_replace;


    return;

  }
  // set_prepare_page_code_variables_data


  function prepare_page_code_variables( $variables, $metadata ) {

    $this->set_prepare_page_code_variables_data( array(

      'regex_search_replace' => array( 'charset' => $metadata[ 'charset' ] )

    ) );


    $search_replace_chars = $this->method_data[ __FUNCTION__ ][ 'search_replace_chars' ];

    $regex_search_replace = $this->method_data[ __FUNCTION__ ][ 'regex_search_replace' ];


    foreach ( $variables as $variable_name => $variable_value ) {

      if ( is_array( $variable_value ) ) {

        $variable_value = $this->prepare_page_code_variables( $variable_value );

      }
      // if


      else {

        $variable_value = html_entity_decode( strip_tags( $variable_value ), ENT_QUOTES, $metadata[ 'charset' ] );

        $variable_value = str_replace(

          $search_replace_chars[ 'search' ],

          $search_replace_chars[ 'replace' ],

          $variable_value

        );


        $variable_value = preg_replace( $regex_search_replace[ 'search' ], $regex_search_replace[ 'replace' ], $variable_value );

      }
      // else


      $variables[ $variable_name ] = $variable_value;

    }
    // foreach


    return $variables;

  }
  // prepare_page_code_variables


  function convert_to_json( $data ) {

    static $indent_level = 1;

    $indent_spaces = 2;

    $json = array();

    $indent_string = str_repeat( " ", ( $indent_level * $indent_spaces ) );

    foreach ( $data as $d_key => $d_value ) {

      if ( is_array( $d_value ) ) {

        $indent_level += 1;

        $d_value = $this->convert_to_json( $d_value );

        $indent_level -= 1;

      }
      // if

      else {

        $d_value = str_replace( '"', "\\x22", $d_value );

        $d_value = <<<DOCHERE
"{$d_value}"
DOCHERE;

      }
      // else


      $json[ $d_key ] = <<<DOCHERE
{$indent_string}"{$d_key}" : {$d_value}
DOCHERE;


    }
    // foreach


    $json = join( ",\n\n", $json );


    $indent_string = str_repeat( " ", ( ( $indent_level - 1 ) * $indent_spaces ) );

    if ( $json ) {

      $json = <<<DOCHERE
{

{$json}

{$indent_string}}
DOCHERE;

    }
    // if


    return $json;

  }
  // convert_to_json


  function get_page_code( $params = array() ) {

    $page_code = $this->get_unique_page_code( $params );


    ob_start();

    include ( dirname( __FILE__ ) . "/template.php" );

    $page_code = ob_get_clean();


    return $page_code;

  }
  //  get_page_code


  function get_unique_page_code( $params ) {

    $variables = array_merge( $this->default_params[ 'variables' ], $params );

    unset( $variables[ '_meta' ] );

    $metadata = array_merge( $this->default_params[ 'metadata' ], $params[ '_meta' ] );


    $page_code = array( 'variables' => NULL, 'metadata' => NULL );


    $meta_output = array();

    foreach ( (array) $metadata[ 'direct-output' ] as $metadata_item ) {

      $meta_output[ $metadata_item ] = $metadata[ $metadata_item ];

    }
    // foreach


    $meta_output = $this->convert_to_json( $meta_output );

    if ( $meta_output ) {

      $page_code[ 'metadata' ] = $meta_output;

    }
    // if


    if ( ! $metadata[ 'output-meta-only' ] ) {

      $variables = $this->prepare_page_code_variables( $variables, $metadata );


      foreach ( $variables as $variable_name => $variable_value ) {

        $variables[ $variable_name ] = <<<DOCHERE
{$this->js_var}.{$variable_name} = "{$variable_value}";
DOCHERE;

      }
      // foreach


      $page_code[ 'variables' ] = join( "\n\n", $variables );

    }
    // if


    return $page_code;

  }
  // get_unique_page_code

}
// JMM_Site_Catalyst


