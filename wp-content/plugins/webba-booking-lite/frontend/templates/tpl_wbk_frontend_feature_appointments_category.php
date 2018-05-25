<?php
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) ); 

if( is_numeric( $data[0] ) && $data[0] > 0 ){
	$arr_appointments = WBK_Db_Utils::getFeatureAppointmentsByCategory( $data[0] );
	 
}
?>
<table data-tablesaw-sortable class="slf-table tablesaw tablesaw-stack" data-tablesaw-mode="stack">
<thead>
<tr>
	<th data-tablesaw-sortable-col data-tablesaw-sortable-default-col>
		<?php  echo __( 'Service', 'wbk' ) ?>
	</th>
	<th data-tablesaw-sortable-col data-tablesaw-sortable-default-col>
		<?php  echo __( 'ID', 'wbk' ) ?>
	</th>
	<th data-tablesaw-sortable-col data-tablesaw-sortable-default-col>
		<?php  echo __( 'Date', 'wbk' ) ?>
	</th>
	<th data-tablesaw-sortable-col data-tablesaw-sortable-default-col>
		<?php  echo __( 'Time', 'wbk' ) ?>
	</th>
	<th data-tablesaw-sortable-col data-tablesaw-sortable-default-col>
		<?php  echo __( 'Name', 'wbk' ) ?>
	</th>
	<th data-tablesaw-sortable-col data-tablesaw-sortable-default-col>
		<?php  echo __( 'Email', 'wbk' ) ?>
	</th>
	<th data-tablesaw-sortable-col data-tablesaw-sortable-default-col>
		<?php  echo __( 'Phone', 'wbk' ) ?>
	</th>
	<th data-tablesaw-sortable-col data-tablesaw-sortable-default-col>
		<?php  echo __( 'Comment', 'wbk' ) ?>
	</th>
	<th data-tablesaw-sortable-col data-tablesaw-sortable-default-col>
		<?php  echo __( 'Custom fields', 'wbk' ) ?>
	</th>	
</tr>
<tbody>
<?php
	$date_format = WBK_Date_Time_Utils::getDateFormat();	
	$time_format = WBK_Date_Time_Utils::getTimeFormat();

	foreach( $arr_appointments as $appointment_id ){
		$appointment = new WBK_Appointment();
        if ( !$appointment->setId( $appointment_id ) ) {
			continue;             
        }     
        if ( !$appointment->load() ) {
        	continue;
        }
        $service_id =  $appointment->getService();
		$service = new WBK_Service();
        if ( !$service->setId( $service_id ) ) {
			continue;             
        }     
        if ( !$service->load() ) {
        	continue;
        }
        $service_name = $service->getName();
        $name = $appointment->getName();
        $comment = $appointment->getDescription();
        $email = $appointment->getEmail();
        $phone = $appointment->getPhone();
        $time = $appointment->getTime();
        $quantity = $appointment->getQuantity();
        $extra = $appointment->getExtra();
        $extra = str_replace( '###', PHP_EOL, $extra );
		$date = date_i18n( $date_format, $time );
		$time = date_i18n( $time_format, $time );

		?>
			<tr>
				<td>
					<?php echo $service_name ?>
				</td>
				<td>
					<?php echo $appointment_id ?>
				</td>
				<td>
					<?php echo $date ?>
				</td>
				<td>
					<?php echo $time ?>
				</td>
				<td>
					<?php echo $name ?>
				</td>
				<td>
					<?php echo $email ?>
				</td>
				<td>
					<?php echo $phone ?>
				</td>
				<td>
					<?php echo $comment ?>
				</td>

				<td>
					<?php echo $extra ?>
				</td>
			</tr>		
		 
		<?php

	}
?>

	

</tbody>
</thead>
</table>
<?php
date_default_timezone_set( 'UTC' );
?>