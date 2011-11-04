<?php

/*

Omniture SiteCatalyst deployment utilities.  Preprocessing of SiteCatalyst variable values and generation of SiteCatalyst JavaScript code.

This subclass contains data and functionality unique to the site being deployed to.

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


require_once ( dirname( __FILE__ ) . "/jmm_site_catalyst.php" );


class Custom_Site_Catalyst extends JMM_Site_Catalyst {

  function Custom_Site_Catalyst() {

    parent::JMM_Site_Catalyst();


    $this->set_default_params( array(

      'variables' => array(

        'prop15' => "whatever"

      ),
      // array


      'metadata' => array(

        'charset' => 'UTF-8'

      )
      // array

    ) );


    $this->set_delimiters( array(

      // Actual SiteCatalyst variable names

      'pageName' => ":",

      'prop1' => ":",

      'prop2' => ":",

      'prop3' => ":",

      'hier1' => ",",

      'events' => ","

    ) );


    return;

  }
  // Custom_Site_Catalyst


  function get_page_code( $params = array() ) {

    // Restrict direct output meta to just certain items until parent::generate_page_specific_page_code() conversion of PHP to JSON is more robust

    $params[ '_meta' ][ 'direct-output' ] = (array) $params[ '_meta' ][ 'direct-output' ];

    $params[ '_meta' ][ 'direct-output' ] = array_values( array_intersect(

      $params[ '_meta' ][ 'direct-output' ],

      array( 'event-serial-ids', 'entries' )

    ) );


    $page_code = parent::get_page_code( $params );


    return $page_code;

  }
  // get_page_code

}
// Custom_Site_Catalyst

