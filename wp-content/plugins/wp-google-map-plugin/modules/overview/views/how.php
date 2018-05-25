<?php
/**
 * Plugin Overviews.
 * @package Maps
 * @author Flipper Code <flippercode>
 **/

?>
<?php 
        $form  = new WPGMP_Template();
        echo $form->show_header();
   ?>

<div class="flippercode-ui">
<div class="fc-main">
<div class="fc-container">
 <div class="fc-divider">

 <div class="fc-back fc-docs ">
 <div class="fc-12">
            <h4 class="fc-title-blue"> How to Create your First Map? </h4>
              <div class="wpgmp-overview">
                <ol>

                    <li><?php
                    $url = admin_url( 'admin.php?page=wpgmp_manage_settings' ); ?>
                   First create a  <a href="http://bit.ly/29Rlmfc" target="_blank">  Google Map API Key</a>. Then go to <a href="<?php echo $url ?>"> Settings </a> page and insert your google maps API Key and save.
                    </li>
                    
                    <li><?php
                    $url = admin_url( 'admin.php?page=wpgmp_form_location' ); ?>
                    Create a location by using<a href="<?php echo $url; ?>" target="_blank">  Add Location</a> page. To import multiple locations on a single click, Go to  "Import Location" page and browse your csv file and import it. </a>.
                    </li>
                    
                    <li><?php
                    $url = admin_url( 'admin.php?page=wpgmp_form_map' ); ?>
                    Go to <a href="<?php echo $url; ?>" target="_blank">  Add Map</a> page and insert details as per your requirement. Assign locations to map and save your map.
                    </li>
                                                                                
                </ol>
            </div>
            
            <h4 class="fc-title-blue"> How to Display Map in Frontend? </h4>
              <div class="wpgmp-overview">
                        
                    <p><?php
                    $url = admin_url( 'admin.php?page=wpgmp_manage_map' ); ?>
                    Go to <a href="<?php echo $url; ?>" target="_blank"> Manage Map</a> and copy the shortcode then paste it to any page/post where you want to display map.
                    </p>
                    
              </div>
              
            
            
        
           

           
          

        <h4 class="fc-title-blue"> How to Create Marker Category? </h4>
                <div class="wpgmp-overview">
                        
                    <p><?php
                    $url = admin_url( 'admin.php?page=wpgmp_form_group_map' ); ?>
                    Go to <a href="<?php echo $url;?>" target="_blank"> Add Marker Category</a> and choose parent category if any , category title and choose icon. These categories can be assigned to the location on "Add Locations" page.
                   </p>
                </div> 


        <h4 class="fc-title-blue"> Google Map API Troubleshooting </h4>
        <div class="wpgmp-overview">
        <p>If your google maps is not working. Make sure you have checked following things.</p>
        <ul>
        <li> 1. Make sure you have assigned locations to your map.</li>
        <li> 2. You must have google maps api key.</li>
        <li> 3. Check HTTP referrers. It must be *.yourwebsite.com/* 
        </li>
        </ul>
        <p><img src="<?php echo WPGMP_IMAGES; ?>referrer.png"> </p>
        <p>If still any issue, Create your <a target="_blank" href="http://www.flippercode.com/forums">support ticket</a> and we'd be happy to help you asap. </p>
        </div>          
    </div>
</div></div>
</div>
</div></div>