<!-- Webba Booking service categories page template --> 
<?php
    // check if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap">
 	<h2 class="wbk_panel_title"><?php  echo __( 'Service categories', 'wbk' ); ?>
    </h2>
        <?php        
            $table = new WBK_Service_Categories_Table();
            $html = $table->render();
            echo $html;
        ?>                                            
</div>
