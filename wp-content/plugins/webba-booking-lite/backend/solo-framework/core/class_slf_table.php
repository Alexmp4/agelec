<?php
// Solo Framework table class
if ( ! defined( 'ABSPATH' ) ) exit;
add_action( 'wp_ajax_slf_table_update',  'slfTableUpdate' );
add_action( 'wp_ajax_slf_table_prepare_row',  'slfPrepareRow' ); 
add_action( 'wp_ajax_slf_table_render_add_row',  'slfRenderAddRow' ); 
add_action( 'wp_ajax_slf_table_update_row',  'slfUpdateRow' ); 
add_action( 'wp_ajax_slf_table_add_row',  'slfAddRow' ); 
add_action( 'wp_ajax_slf_table_delete_row',  'slfDeleteRow' ); 
 

function getAllowedClassNames() {
	return array( 'WBK_Appointments_Table' , 'WBK_Email_Templates_Table', 'WBK_Service_Categories_Table', 'WBK_Service_Categories_Table', 'WBK_GG_Calendar_Table', 'WBK_Coupons_Table' );
}

function slfRenderAddRow(){
	date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
	$class_name = sanitize_text_field( $_POST['class_name'] );
	if ( !in_array( $class_name, getAllowedClassNames() ) ){
		echo 'ERROR: Undefined table.';
		date_default_timezone_set( 'UTC' ) ;
		die();
		return;
	}
	$table = new $class_name();
	if( !$table->checkAccess() ){
		date_default_timezone_set( 'UTC' ) ;
		die();
		return;
	} 
	$result = $table->renderAddRowForm();
	echo $result;
	date_default_timezone_set( 'UTC' ) ;
	die();
	return;
}
 
function slfDeleteRow(){
	$class_name = sanitize_text_field( $_POST['class_name'] );
	$row_id = sanitize_text_field ( $_POST['row_id'] );
	if ( !is_numeric( $row_id ) ){
		echo 'ERROR: row ' . $row_id.' not found.';
		die();
		return;
	}
	if ( !in_array( $class_name, getAllowedClassNames() ) ){
		echo 'ERROR: Undefined table.';
		die();
		return;
	}
	$table = new $class_name();
	if( !$table->checkAccess() ){
		die();
		return;
	} 
	$result = $table->deleteRow( $row_id );
	echo $result;
	die();
	return;
}

function slfPrepareRow(){
	date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
	$class_name = sanitize_text_field( $_POST['class_name'] );
	$row_id = sanitize_text_field ( $_POST['row_id'] );
	if ( !in_array( $class_name, getAllowedClassNames() ) ){
		echo 'ERROR: Undefined table class.';
		date_default_timezone_set( 'UTC' );
		die();
		return;
	} 
	if ( !is_numeric( $row_id ) ){
		echo 'ERROR: row not found.';
		date_default_timezone_set( 'UTC' );
		die();
		return;
	}
	$table = new $class_name();
	if( !$table->checkAccess() ){
		date_default_timezone_set( 'UTC' );
		die();
		return;
	}
	$result = $table->prepareRow( $row_id );
	echo $result;
	date_default_timezone_set( 'UTC' );
	die();
	return;
}
function slfUpdateRow(){
	global $wpdb;
	date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
	$class_name = sanitize_text_field( $_POST['class_name'] );
	$row_id = sanitize_text_field ( $_POST['row_id'] );
	if( !is_numeric( $row_id  ) ){
		echo 'ERROR: row not found.';
		date_default_timezone_set( 'UTC' );
		die();
		return;
	}
	$fields = $_POST['fields'];
	if ( !in_array( $class_name, getAllowedClassNames() ) ){
		echo 'ERROR: Undefined table.';
		date_default_timezone_set( 'UTC' );
		die();
		return;
	}
 	$table = new $class_name();
 	$error_fields = array();
 	$to_update = array();
 	$formats = array();
 	$result = array();
  	foreach ( $fields as $field ) {
	   $validation = $table->field_set->fields[ $field['name'] ]->validation;
	   if ( $validation == null ){
	   		if( $field['name'] == 'day' ){
	   			$field[ 'value' ] = strtotime( $field[ 'value' ]) ;
	   		}
	   		if( $field['name'] == 'category_list' || $field['name'] == 'services' ){
	   			$field[ 'value' ] = implode(',', $field[ 'value' ] ) ;
	   		}
	   		if( $field['name'] == 'extra' ){	   			
	   			 
	   			$field[ 'value' ] = stripslashes ( $field[ 'value' ] );
	   			 
	   		}
	   		$to_update[ $field['name'] ] = $field['value'];
			$formats[] = $table->field_set->fields[ $field['name'] ]->format;

	   } else {
	   		if( $field['name'] == 'day' ){
	   			$field[ 'value' ] = strtotime( $field[ 'value' ]) ;
	   		}
	   		if( $field['name'] == 'category_list' || $field['name'] == 'services' ){
	   			$field[ 'value' ] = implode(',', $field[ 'value' ] ) ;
	   		}
	   		$valid_class = $validation[0][0];
	   		$valid_function = $validation[0][1];
			$valid_condition = $validation[1];
			$environment = array( $row_id, $table->table_name );
			$valid_result =  $valid_class::$valid_function( $field['value'], $valid_condition, $environment );

	   		if ( $valid_result == true ){
	   			$to_update[ $field['name'] ] = $field['value'];
	   			$formats[] = $table->field_set->fields[ $field['name'] ]->format;
	   		} else {	   			 
	   			$error_fields[] =  $table->field_set->fields[ $field['name'] ]->title;;
	   			$result['status'] = 0;
	   		}
	   }	
   		 		 	
  	}
  	if( count( $error_fields ) == 0  ){	 	
  		$before_update = $table->onBeforeUpdate( $row_id );
  	 	$edited = $wpdb->update( 
						   $table->table_name, 
						   $to_update, 
						   array( 'id' => $row_id ), 
					       $formats,
						   array( '%d' ) 
					      );



  	 	if( $edited == 0 ){
  	 		$result['status'] = 2;
  	 		$result = json_encode($result);
  			echo $result;
  			date_default_timezone_set( 'UTC' );
			die();
			return;
  	 	} 
  	 	if( $edited == 1 ){
  	 		$table->onAfterUpdate( $before_update, $row_id );
  	 		$new_row =   $table->renderRow( $row_id );
  	 		$result['status'] = 1;
  	 		$result['data'] = $new_row;
  	 		$result = json_encode($result);
  			echo $result;
  			date_default_timezone_set( 'UTC' );
			die();
			return;
  	 	}
 
  	} else {
  		$result['data'] = __( 'Unable to save, please fix the following fields: ', 'wbk' ) . implode( ', ', $error_fields );
  	}
  	$result = json_encode($result);
  	echo $result;
  	date_default_timezone_set( 'UTC' );
	die();
	return;
}
function slfAddRow(){
	global $wpdb;
	date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
	$class_name = sanitize_text_field( $_POST['class_name'] );	 
	$fields = $_POST['fields'];
	if ( !in_array( $class_name, getAllowedClassNames() ) ){
		echo 'ERROR: Undefined table.';
		date_default_timezone_set( 'UTC' );
		die();
		return;
	}
 	$table = new $class_name();
 	$error_fields = array();
 	$to_update = array();
 	$formats = array();
 	$result = array();
  	foreach ( $fields as $field ) {
	   $validation = $table->field_set->fields[ $field['name'] ]->validation;
	   if ( $validation == null ){
			if( $field['name'] == 'day' ){
	   			$field[ 'value' ] = strtotime( $field[ 'value' ] ) ;
	   		}
			if( $field['name'] == 'category_list' || $field['name'] == 'services'){
	   			$field[ 'value' ] = implode(',', $field[ 'value' ] ) ;
	   		}

	   		$to_update[ $field['name'] ] = $field['value'];
			$formats[] = $table->field_set->fields[ $field['name'] ]->format;
	   } else {
	   		if( $field['name'] == 'day' ){
	   			$field[ 'value' ] = strtotime( $field[ 'value' ]) ;
	   		}
	   		if( $field['name'] == 'category_list' || $field['name'] == 'services' ){
	   			$field[ 'value' ] = implode(',', $field[ 'value' ] ) ;
	   		}
	   	 
	   		$valid_class = $validation[0][0];
	   		$valid_function = $validation[0][1];
			$valid_condition = $validation[1];
			$row_id = null;
			$environment = array( $row_id, $table->table_name );
			$valid_result =  $valid_class::$valid_function( $field['value'], $valid_condition, $environment );		 	 
	   		if ( $valid_result ){
	   			$to_update[ $field['name'] ] = $field['value'];
	   			$formats[] = $table->field_set->fields[ $field['name'] ]->format;
	   		} else {	   			 
	   			$error_fields[] =  $table->field_set->fields[ $field['name'] ]->title;;
	   			$result['status'] = 0;
	   			
	   		}
	   }	
   		 		 	
  	}
  	if( count( $error_fields ) == 0  ){
  	 	$edited = $wpdb->insert( 
						    $table->table_name, 
						    $to_update, 						    
					        $formats
						   	);
  	 	if( $edited == false ){
  	 		$result['status'] = 2;
  	 		$result['data'] = __( 'Internal error', 'wbk' );
  	 		$result = json_encode($result);
  			echo $result;
  			date_default_timezone_set( 'UTC' );
			die();
			return;
  	 	} 
  	 	if( $edited == 1 ){
  	 		$new_row =  '<tr id="slf-table-row-'. $wpdb->insert_id .'" >' . $table->renderRow( $wpdb->insert_id ) . '</tr>';
  	 		$result['status'] = 1;
  	 		$result['data'] = $new_row;
  	 		$result = json_encode($result);
  	 		$table->onAfterAdd( $wpdb->insert_id );
  	 		date_default_timezone_set( 'UTC' );
  			echo $result;
			die();
			return;
  	 	}
 	
  	} else {
  		$result['data'] = __( 'Unable to add, please fix the following fields: ', 'wbk' ) . implode( ',', $error_fields );
  	}
  	$result = json_encode($result);
  	date_default_timezone_set( 'UTC' );
  	echo $result;
	die();
	return;
}
function slfTableUpdate(){
	date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
	$class_name = sanitize_text_field( $_POST['class_name'] );
	$filters = $_POST['filters'];
	if ( !in_array( $class_name, getAllowedClassNames() ) ){
		echo 'ERROR: Undefined table.';
		date_default_timezone_set( 'UTC' );
		die();
		return;
	} 
	$table = new $class_name();
	foreach ( $filters as $filter ) {
		$filter_name = $filter['field'];
		$filrer_value = $filter['value'];
		if ( !$table->filter_set[$filter_name]->set($filrer_value) ){
			$table->filter_set[$filter_name]->setDefault();
		}
	}
  	$html = $table->renderTable();
	if ( $table->field_set->allow_add == true ){
			$html .= $table->renderAddPanel();
	}
 	echo $html;
	date_default_timezone_set( 'UTC' );
 	die();
	return;
}
class SLFTable extends stdClass {
	public function __construct( $param = array() ) {
	}
	public function render(){
		$this->renderFilters();
		$this->renderData();
		$this->renderFooter();
	}
	public function renderFilters(){
		if ( !isset( $this->filter_set ) || !is_array( $this->filter_set ) ) {
			echo '';
			return;
		}
		$html = '<div class="slf_row slf_overflow_visible">';
		foreach ( $this->filter_set as $filter ) {
			$html .=  '<div class="slf_col_12_6_4">';
			$html .=  $filter->render();
			$html .=  '</div>';
		}
		$html .= '<div style="clear:both;"></div>';
		$html .= '</div>';
		echo $html;
	}
	public function renderData(){
		$html = '<input type="hidden" id="slf_table_class_name" value ="'. get_class($this) .'">';	
		$html .= '<div class="slf_row">';
		$html .= '<div id="slf-table-container">';
		$html .= $this->renderTable();
		if ( $this->field_set->allow_add == true ){
			$html .= $this->renderAddPanel();
		}
		$html .= '</div>';
		$html .= '</div>';
		echo $html;
	}
	public function renderAddPanel(){
		$html  = '<div id="slf_table_add_panel">';
		$html .= '<input class="button slf_create_new_btn" type="button" value="' . __( 'Create new', 'wbk' ) . '" onclick="slf_table_render_add_form( \'' . get_class($this).'\');" >';
		$html .= '</div>';

		return $html;
	}
	public function renderFooter(){
		$html = $this->footerContent();
		if( $html != '' ){
			$html = '<div class="slf_row slf_overflow_visible">' . $html . '</div>';

		} 
		echo $html;
	}
	public function footerContent(){
		return '';
	}

	public function renderTable(){
		global $wpdb;
		$col_names = array();
		$html = '<div class="slf_overlay"></div>';	
		$html .= '<table data-tablesaw-sortable class="slf-table tablesaw tablesaw-stack" data-tablesaw-mode="stack">';
		$html .=	'<thead>';	 
		$html .=		'<tr><th data-tablesaw-sortable-col data-tablesaw-sortable-default-col>' . __( 'ID', 'wbk' ) . '</th>';
		foreach ( $this->field_set->fields as $field ) { 
			if ( !$field->render_cell ){
				continue;
			}

			if( $field->name == 'extra' ){
				$ids = trim( get_option( 'wbk_custom_fields_columns', '' ) );
				if( $ids != ''  ){
					$ids = explode( ',', $ids );
					foreach ( $ids as $current_custom_id ) {
						$html .=  '<th id="' . $field->name .$current_custom_id. '"  data-tablesaw-sortable-col>' . $current_custom_id . '</th>';
					}					
				} else {
					$html .=  '<th id="' . $field->name . '"  data-tablesaw-sortable-col>' . $field->title . '</th>';
				}
			} else {
				$html .=  '<th id="' . $field->name . '"  data-tablesaw-sortable-col>' . $field->title . '</th>';
				
			} 
			$col_names[] = $field->name;
		}
		$html .=		'</tr>';
		$html .=	'</thead>';
		$html .= '<tbody>';
		$sql = 'SELECT id,'. implode(',', $col_names ) . ' FROM ' . $this->table_name;
		$condition = '';
		if ( isset( $this->filter_set ) && is_array( $this->filter_set ) ) {
			foreach (	$this->filter_set as $filter ) {
				if ( $condition == '' ) {
					$condition .= ' WHERE ' . $filter->getSql();
				} else {
					$condition .= ' AND ' . $filter->getSql();
				}
			}
	 	} else {
	 		$condition .= ' WHERE id = -1';
	 	}
	 	$sql .= $condition . $this->getOrder();
		$rows = $wpdb->get_results( 
			$sql, ARRAY_A 		
		);
		$row_num = 0;	
		foreach ( $rows as $row ) {
			$row_num++;
			$html .= '<tr id="slf-table-row-' . $row['id'] . '">';

				$i = 0;
				foreach ( $row  as  $key => $field ) {		
				 	 
					if ( $key == 'id' ){
					    if ( $this->field_set->editable == true ){
					    	$html_control = ''; 
						    $html_control .= '<a class="slf-table-row-edit slf-table-icon" data-row-id="' . $field . '" data-app-id="' . $field . '" href="javascript:slf_table_prepare_row( ' . $field . ', \'' . 
						    get_class($this).'\');"><span class="dashicons dashicons-welcome-write-blog"></span></a>';

							$html_control .= '<a class="slf-table-row-edit slf-table-icon" data-row-id="' . $field . '" data-app-id="' . $field . '" href="javascript:slf_table_prepare_delete_row( ' . $field . ', \'' . 
						    get_class($this).'\');"><span class="dashicons dashicons-trash"></span></a>';
 

						} else {
							$html_control = '';
						}
						$html .= '<td>' . $field . $html_control . '</td>';
					} else {
						if ( !$this->field_set->fields[ $key ]->render_cell ){
							continue;
						}
						$class_name =  $this->field_set->fields[ $key ]->component;

						$name = $this->field_set->fields[ $key ]->name;
						$value = $field;					 
						$title = $this->field_set->fields[ $key ]->title; 

						$data_source =  array( $this->field_set->fields[ $key ]->data_source, $row['id'] );
						$component = new $class_name( $title, $name, $value, $data_source );
						$splitted = false;
						if( $key == 'extra' ){
							$ids = get_option( 'wbk_custom_fields_columns', '' );
							if( $ids != ''  ){								 
                                $html .= $component->renderCell();
                                $splitted = true;
							}
						} 
						if( !$splitted ){
							$html .= '<td>' . $component->renderCell() . '</td>';						
						}
					}
					$i++;
				}
			$html .= '</tr>';		
		}
		$html .= '</tbody>';
		$html .= '</table>';
		return $html;
	}
	public function exportTableCSV(){
		return;
	}
	protected function getOrder(){
		return ' order by id ';
	} 
	public function prepareRow( $row_id ){
		global $wpdb;
		$html = '';
		foreach ( $this->field_set->fields as $field ) { 
			if ( !$field->render_control ){
				continue;
			}		 
			$sql =  $wpdb->prepare( 
							"
								SELECT $field->name
								FROM  $this->table_name
								WHERE id = %d
							", 
						    $row_id
						);
			$value = $wpdb->get_var( $sql );
			$data_source = array(  $field->data_source, $row_id );

			$component = new $field->component( $field->title , $field->name, $value, $data_source );
			$html .= $component->renderControl();		 
		};
	 	$html .= '<div class="slf_control_container">';
		$html .= '<input type="button" class="button-primary slf_table_row_save" value="' . __( 'Save', 'wbk' ) . '" />';	 
		$html .= '<input type="button" class="button-primary slf_table_row_cancel" value="' . __( 'Cancel', 'wbk' ) . '" />';	 
		$html .= '</div>';
	 	$html .= '<div class="slf_control_error_message">';
		$html .= '</div>';

		echo $html;
	}
	public function renderAddRowForm(){
		$html = '<h3>' . __( 'Fill in a form to add new element:', 'wbk' ) . '</h3>';
		foreach ( $this->field_set->fields as $field ) { 
			if ( !$field->render_control ){
				continue;
			}		 
			$value = '';
			$data_source = array( $field->data_source, -1 );

			$component = new $field->component( $field->title , $field->name, $value, $data_source );
			$html .= $component->renderControl();		 
		};
	 	$html .= '<div class="slf_control_container">';
		$html .= '<input type="button" class="button-primary slf_table_row_save" value="' . __( 'Save', 'wbk' ) . '" />';	 
		$html .= '<input type="button" class="button-primary slf_table_row_cancel" value="' . __( 'Cancel', 'wbk' ) . '" />';	 
		$html .= '</div>';
	 	$html .= '<div class="slf_control_error_message">';
		$html .= '</div>';

		echo $html;
	}
	public function deleteRow( $id ){
		global $wpdb;
		$data = $this->onBeforeDelete( $id );
		$sql = $wpdb->prepare( "DELETE from $this->table_name where id = %d", $id );
		$result = $wpdb->query( $sql );
		if( $result == 1 ){
			$this->onAfterDelete( $data, $id );
		}
	 	return $result;
	}
	public function renderRow( $id ){
		global $wpdb;
	 	
    	foreach ( $this->field_set->fields as $field ) { 
			$col_names[] = $field->name;
		}
		if ( !is_numeric( $id) ){
			return;
		}

		 
		$sql = 'SELECT id,'. implode(',', $col_names ) . ' FROM ' . $this->table_name . ' where id =' . $id;
		 
		$rows = $wpdb->get_results( 
			$sql, ARRAY_A 		
		);
		$row_num = 0;	
		$html = '';
		 
		foreach ( $rows as $row ) {
			$row_num++;
				$i = 0;
				foreach ( $row  as  $key => $field ) {				 
					if ( $key == 'id' ){
					    if ( $this->field_set->editable == true ){
						    $html_control = '<a class="slf-table-row-edit slf-table-icon" data-row-id="' . $field . '" data-app-id="' . $field . '" href="javascript:slf_table_prepare_row( ' . $field . ', \'' . 
						    get_class($this).'\');"><span class="dashicons dashicons-welcome-write-blog"></span></a>';
						    $html_control .= '<a class="slf-table-row-edit slf-table-icon" data-row-id="' . $field . '" data-app-id="' . $field . '" href="javascript:slf_table_prepare_delete_row( ' . $field . ', \'' . 
						    get_class($this).'\');"><span class="dashicons dashicons-trash"></span></a>';


						} else {
							$html_control = '';
						}
						$html .= '<td>' . $field . $html_control . '</td>';
					} else {
						if ( !$this->field_set->fields[ $key ]->render_cell ){
							continue;
						}
						$class_name =  $this->field_set->fields[ $key ]->component;

						$name = $this->field_set->fields[ $key ]->name;
						$value = $field;					 
						$title = $this->field_set->fields[ $key ]->title; 
						
						$data_source = array(  $this->field_set->fields[ $key ]->data_source, $row['id'] );
						
						$component = new $class_name( $title, $name, $value, $data_source );
						$html .= '<td>' . $component->renderCell() . '</td>';
					}
					$i++;
				}
		}
		return $html;
	}
	public function checkAccess(){
		if ( current_user_can('manage_options') ){
        	return TRUE;
        } else {
	 		return FALSE;
	    }
	}
	public function onAfterAdd( $id ){
	}
	public function onBeforeUpdate( $row_id ){
	}
	public function onAfterUpdate( $data, $row_id ){
	}
	public function onBeforeDelete( $row_id ){
	}
	public function onAfterDelete( $data, $row_id ){
	}


}
?>