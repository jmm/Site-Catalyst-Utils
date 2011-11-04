

<script type="text/javascript" src="site_catalyst_settings.js"></script>

<script type="text/javascript" src="site_catalyst.js"></script>

<script type="text/javascript">

<?php

    if ( $page_code[ 'variables' ] ) {

      echo <<<DOCHERE

{$page_code[ 'variables' ]}


/************* DO NOT ALTER ANYTHING BELOW THIS LINE ! **************/
var s_code=s.t();if(s_code)document.write(s_code)

DOCHERE;

    }
    // if


    if ( $page_code[ 'metadata' ] ) {

      echo <<<DOCHERE


var site_catalyst_meta = {$page_code[ 'metadata' ]};

DOCHERE;

    }
    // if

?>


</script>

