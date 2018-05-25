<!-- Webba Booking backend appearance page template --> 
<div class="wrap">
	<h2 class="wbk_panel_title"><?php  echo __( 'Appearance', 'wbk' ); ?></h2>
	<div class="row">
		<?php
		//	$slf= new SoloFramework( 'wbk_settings_data' );
			$slf->renderSectionSetControls( 'wbk_extended_appearance_options' );
		?>
	</div>
	<div class="row">
		<div class="slf-col-3">	
			<?php 
			 
			?>
		</div>
		<div class="slf-col-9">	
			<?php 
				$slf->renderSectionSet( 'wbk_extended_appearance_options' );
			?>
		</div>
	</div>
 </div>