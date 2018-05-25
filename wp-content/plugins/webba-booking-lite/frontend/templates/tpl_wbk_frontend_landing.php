<?php
    // check if accessed directly
    if ( ! defined( 'ABSPATH' ) ) exit;
    date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );    
 	if( isset( $_GET['paypal_status'] ) ){
?>
		<div class="wbk-outer-container">
			<div class="wbk-inner-container">
				<div class="wbk-frontend-row">
					<div class="wbk-col-12-12"> 
						<div class="wbk-details-sub-title">
						<?php  
							global $wbk_wording;
							$payment_title =  get_option( 'wbk_payment_result_title', '' );
							if( $payment_title  == '' ){
								$payment_title = sanitize_text_field( $wbk_wording['payment_title']	);
							}
							echo $payment_title;
						?></div>
					</div>
					<div class="wbk-col-12-12"> 
						<?php
							if( $_GET['paypal_status'] == 1 ){
							?>
								<div class="wbk-input-label wbk_payment_success"><?php 
								global $wbk_wording;
								$payment_complete_label  =  get_option( 'wbk_payment_success_message', '' ); 
								if( $payment_complete_label == ''){
									$payment_complete_label = sanitize_text_field( $wbk_wording['payment_complete'] );
								}
								echo $payment_complete_label;
								?></div>
						<?php
						    }
						?>
						<?php
							if( $_GET['paypal_status'] == 5 ){
							?>
								<div class="wbk-input-label wbk_payment_cancel"><?php
	 							global $wbk_wording;
								$payment_canceled_label  =  get_option( 'wbk_payment_cancel_message', '' ); 
								if( $payment_canceled_label == ''){
									$payment_canceled_label = sanitize_text_field( $wbk_wording['payment_complete'] );
								}
								echo $payment_canceled_label;
								?></div>
						<?php
						    }
						?>
						<?php
							if( $_GET['paypal_status'] == 2 ){
							?>
								<div class="wbk-input-label wbk_payment_error">Error 102</div>
						<?php
						    }
						?>
						<?php
							if( $_GET['paypal_status'] == 3 ){
							?>
								<div class="wbk-input-label wbk_payment_error">Error 103</div>
						<?php
						    }
						?>
						<?php
							if( $_GET['paypal_status'] == 4 ){
							?>
								<div class="wbk-input-label wbk_payment_error">Error 104</div>
						<?php
						    }
						?>
					</div>
				</div>
			</div>
		</div>

<?php
		date_default_timezone_set( 'UTC' );  
		return;
	}
?>
<?php
	if( get_option( 'wbk_allow_manage_by_link', 'no' ) == 'yes' ){		
 		if( isset( $_GET['admin_cancel'] ) ){
	 		$admin_cancel =  $_GET['admin_cancel'];
			$admin_cancel = str_replace('"', '', $admin_cancel );
			$admin_cancel = str_replace('<', '', $admin_cancel );
			$admin_cancel = str_replace('\'', '', $admin_cancel );
			$admin_cancel = str_replace('>', '', $admin_cancel );
			$admin_cancel = str_replace('/', '', $admin_cancel );
			$admin_cancel = str_replace('\\',  '', $admin_cancel );
			$valid = true;
	 		$appointment_id = WBK_Db_Utils::getAppointmentIdByAdminToken( $admin_cancel );
	 		if( $appointment_id === false ){
	 			$valid = false;
	 		} else {
 				$appointment = new WBK_Appointment();
				if ( !$appointment->setId( $appointment_id ) ) {
					$valid = false;
				}
				if ( !$appointment->load() ) {
					$valid = false;
				}
				WBK_Db_Utils::deleteAppointmentDataAtGGCelendar( $appointment_id );
		        $service_id = WBK_Db_Utils::getServiceIdByAppointmentId( $appointment_id );
        		$noifications = new WBK_Email_Notifications( $service_id, $appointment_id );
        		$noifications->prepareOnCancelCustomer();

				if( $appointment->delete() === false ){
					$value = false;
				} else {
					$noifications->sendOnCancelCustomer();
				}

			}
			if( $valid ){
				?>
					<div class="wbk-outer-container">
						<div class="wbk-inner-container">
							<div class="wbk-frontend-row">
								<div class="wbk-col-12-12">
									<div class="wbk-input-label">
									 	<?php echo __( 'Appointment cancelled', 'wbk' ); ?>
									</div>
								</div>
							</div>
							<div class="wbk-frontend-row" id="wbk-payment">
							</div>
						</div>
					</div> 
				<?php
					date_default_timezone_set( 'UTC' ); 
					return;
			}
 		}
 	}
?>	
<?php
	if( get_option( 'wbk_allow_manage_by_link', 'no' ) == 'yes' ){		
 		if( isset( $_GET['admin_approve'] ) ){
	 		$admin_approve =  $_GET['admin_approve'];
			$admin_approve = str_replace('"', '', $admin_approve );
			$admin_approve = str_replace('<', '', $admin_approve );
			$admin_approve = str_replace('\'', '', $admin_approve );
			$admin_approve = str_replace('>', '', $admin_approve );
			$admin_approve = str_replace('/', '', $admin_approve );
			$admin_approve = str_replace('\\',  '', $admin_approve );
			$valid = true;

	 		$appointment_id = WBK_Db_Utils::getAppointmentIdByAdminToken( $admin_approve );

	 		 
	 		if( $appointment_id === false ){
	 			$valid = false;
	 		} else {							 
	 			$status = WBK_Db_Utils::getAppointmentStatus( $appointment_id );
	 			if( $status == 'pending' || $status == 'paid' ){
	 				if( $status == 'pending' ){
	 					WBK_Db_Utils::setAppointmentStatus( $appointment_id, 'approved' );
	 				}
	 				if( $status == 'paid' ){
	 					WBK_Db_Utils::setAppointmentStatus( $appointment_id, 'paid_approved' );
	 				}
	 			} else {
	 				$valid = false;
	 			}
			}
			if( $valid ){
				?>
					<div class="wbk-outer-container">
						<div class="wbk-inner-container">
							<div class="wbk-frontend-row">
								<div class="wbk-col-12-12">
									<div class="wbk-input-label">
									 	<?php 
											$service_id = WBK_Db_Utils::getServiceIdByAppointmentId( $appointment_id );
							                $noifications = new WBK_Email_Notifications( $service_id, $appointment_id );
							                $noifications->sendOnApprove();
							                if( get_option( 'wbk_email_customer_send_invoice', 'disabled' ) == 'onapproval' ){
							                    $noifications->sendSingleInvoice();
							                }   
							                $expiration_mode = get_option( 'wbk_appointments_delete_not_paid_mode', 'disabled' );
							                if( $expiration_mode == 'on_approve' ){
							                    WBK_Db_Utils::setAppointmentsExpiration( $appointment_id );
							                }
									 		echo __( 'Appointment approved', 'wbk' );
									 	?>
									</div>
								</div>
							</div>
							<div class="wbk-frontend-row" id="wbk-payment">
							</div>
						</div>
					</div> 
				<?php
					date_default_timezone_set( 'UTC' ); 
					return;
			}
 		}
 	}
?>
<?php
 	if( isset( $_GET['order_payment'] ) ){
 		$order_payment =  $_GET['order_payment'];

		$order_payment = str_replace('"', '', $order_payment );
		$order_payment = str_replace('<', '', $order_payment );
		$order_payment = str_replace('\'', '', $order_payment );
		$order_payment = str_replace('>', '', $order_payment );
		$order_payment = str_replace('/', '', $order_payment );
		$order_payment = str_replace('\\',  '', $order_payment );

 		$appointment_ids = WBK_Db_Utils::getAppointmentIdsByGroupToken ( $order_payment );

 		if( count( $appointment_ids ) == 0 ){
 			$valid = false;
 		} else {
 				$title = '';
 				$found_valid_appointments = 0;
 				foreach( $appointment_ids as $appointment_id ){
	 				$service_id = WBK_Db_Utils::getServiceIdByAppointmentId( $appointment_id );
	 				if( $service_id == FALSE ){
	 					continue;
	 				}
	 				$valid = true;
	 				$appointment = new WBK_Appointment();
					if ( !$appointment->setId( $appointment_id ) ) {
						continue;
					}
					if ( !$appointment->load() ) {
						continue;
					}
					$service = new WBK_Service();
					if ( !$service->setId( $service_id ) ) {
						continue;
					}
					if ( !$service->load() ) {
						continue;
					}
					$appointment_status = WBK_Db_Utils::getStatusByAppointmentId( $appointment_id );
					if(  $appointment_status != 'paid' && $appointment_status != 'paid_approved' && $appointment_status != 'woocommerce' ){			
						global $wbk_wording;
						$title_this = get_option( 'wbk_appointment_information', '' );
						if( $title_this == '' ){
							$title_this = $wbk_wording['appointment_info'];
						}					 
						$title_this = WBK_Db_Utils::landing_appointment_data_processing( $title_this, $appointment, $service ) . '<br>'; 
						$title .= $title_this;
						$found_valid_appointments++;
						
					}  
				}
				if( $found_valid_appointments == 0 ){
					global $wbk_wording;
					$title = get_option( 'wbk_nothing_to_pay_message', '' );
					if( $title == ''){
						$title = $wbk_wording['nothing_to_pay'];
					}				
				} else {
					$title .= WBK_PayPal::renderPaymentMethods( $service_id, $appointment_ids );
					$title .= WBK_Stripe::renderPaymentMethods( $service_id, $appointment_ids );					
				}

			}

			if( $valid == true ){
				?>
					<div class="wbk-outer-container">
						<div class="wbk-inner-container">
							<div class="wbk-frontend-row">
								<div class="wbk-col-12-12">
									<div class="wbk-input-label">
										<input class="wbk-input" style="display:none;">
									 	<?php echo $title; ?>
									</div>
								</div>
							</div>
							<div class="wbk-frontend-row" id="wbk-payment">
							</div>
						</div>
					</div> 
					<?php
					date_default_timezone_set( 'UTC' ); 
					return;
			}
 		
?>
<?php
	}					
?>
<?php
 	if( isset( $_GET['cancelation'] ) ){	 	
 	 		$cancelation =  $_GET['cancelation'];
			$cancelation = WBK_Db_Utils::wbk_sanitize( $cancelation );
			$appointment_ids = WBK_Db_Utils::getAppointmentIdsByGroupToken( $cancelation );
	 		if( count( $appointment_ids ) == 0  ){
				$valid = false; 
				?>
				<div class="wbk-outer-container">
						<div class="wbk-inner-container">
							<div class="wbk-frontend-row">
								<div class="wbk-col-12-12">
									<div class="wbk-input-label">
									 	<?php echo __( 'appointment(s) not found', 'wbk' ) ?>
									</div>						
								</div>
							</div>
							<div class="wbk-frontend-row" id="wbk-cancel-result">
							</div>
						</div>
					</div> 
				<?php	
				exit;		
	 		} else {
 				$service_id = WBK_Db_Utils::getServiceIdByAppointmentId( $appointment_ids[0] );
 				$valid = false;
				$service = new WBK_Service();
				if ( !$service->setId( $service_id ) ) {
				}
				if ( !$service->load() ) {
				}			
				$title_all = '';
				$valid_items = 0;
				$token_result = array();
				foreach( $appointment_ids as $appointment_id ){ 				
	 				$appointment = new WBK_Appointment();
					if ( !$appointment->setId( $appointment_id ) ) {
						$contine;
					}
					if ( !$appointment->load() ) {
						$contine;
					}
					$valid = true;
					global $wbk_wording;
					$title = get_option( 'wbk_appointment_information', '' );
					if( $title == '' ){
						$title = $wbk_wording['appointment_info'];
					}
					$title = WBK_Db_Utils::landing_appointment_data_processing( $title, $appointment, $service );					
			 		$appointment_status = WBK_Db_Utils::getStatusByAppointmentId( $appointment_id );
					if( $appointment_status == 'paid' || $appointment_status == 'paid_approved' ){	
						if( get_option( 'wbk_appointments_allow_cancel_paid', 'disallow' ) == 'disallow' ){
							global $wbk_wording;
							$paid_error_message = get_option( 'wbk_booking_couldnt_be_canceled',  '' );
							if( $paid_error_message == '' ){
								$paid_error_message = sanitize_text_field( $wbk_wording['paid_booking_cancel'] );
							}
							$title .= ' - ' . $paid_error_message;
							$title_all .= $title . '<br>';
							continue;	
						}						 
					} 
					// check buffer
					$buffer = get_option( 'wbk_cancellation_buffer', '' );
					if( $buffer != '' ){
						if( intval( $buffer ) > 0 ){
							$buffer_point = ( intval( $appointment->getTime() - intval( $buffer ) * 86400 ) );
							if( time() >  $buffer_point ){
								$cancel_error_message = get_option( 'wbk_booking_couldnt_be_canceled2', '' );
								if( $cancel_error_message == ''){
									global $wbk_wording;
									$cancel_error_message = $wbk_wording['paid_booking_cancel2'];
								} 														
								$title .= ' - ' . $cancel_error_message;
								$title_all .= $title . '<br>';
								continue;
							}
						}
					}
					// end check buffer
					$valid_items++;	
					$title_all .= $title . '<br>';	
					$token_result[] = WBK_Db_Utils::getTokenByAppointmentId( $appointment_id );

				}
				$title = $title_all;
				if( $valid_items > 0 ){
					global $wbk_wording;
					$email_cancel_label = get_option( 'wbk_booking_cancel_email_label', '' );
					if( $email_cancel_label == '' ){
						$email_cancel_label =  sanitize_text_field( $wbk_wording['cancelation_email'] );
					}
					$content = '<label class="wbk-input-label" for="wbk-customer_email">'. $email_cancel_label .'</label>';	
					$content .= '<input name="wbk-email" class="wbk-input wbk-width-100 wbk-mb-10" id="wbk-customer_email" type="text">';
					$cancel_label =  get_option( 'wbk_cancel_button_text', '' );
					if( $cancel_label == '' ){
						$cancel_label = sanitize_text_field( $wbk_wording['cancel_label'] );	
					}
					$content .= '<input class="wbk-button wbk-width-100 wbk-mt-10-mb-10" id="wbk-cancel_booked_appointment" data-appointment="'. implode( '-', $token_result ) .'" value="' . $cancel_label . '" type="button">';
				} else {
					$content = '';
				}							  		
			}  
 				if( $valid == true ){
			?>
					<div class="wbk-outer-container">
						<div class="wbk-inner-container">
							<div class="wbk-frontend-row">
								<div class="wbk-col-12-12">
									<div class="wbk-input-label">
									 	<?php echo $title . $content; ?>
									</div>
								</div>
							</div>
							<div class="wbk-frontend-row" id="wbk-cancel-result">
							</div>
						</div>
					</div> 
					<?php
					date_default_timezone_set( 'UTC' ); 
					return;
				}
 	}
 	date_default_timezone_set( 'UTC' );  
?>