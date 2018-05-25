<?php
// Solo Framework table text component
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFTableWpUser extends SLFTableComponent {
	public function __construct( $title, $name, $value, $data_source  ) {
		parent::__construct( $title, $name, $value, null );
	}
    public function renderCell(){
        if( $this->value != 0 ){
            $user_info = get_userdata( $this->value );
            return $user_info->user_login;
        } else {
            return '';
        }
    }
    public function renderControl(){
    	$html = '';
        $html = '<label class="slf_table_component_label" >' . __( 'Give access to this calendar to', 'wbk' ) . '</label>';
        $html .= '<select class="slf_table_component_select slf_table_component_input" name="' . $this->name . '" data-type="select"  >';
        $arr_users = get_users( array( 'role__not_in' => array( 'administrator' ), 'fields' => 'all' ) );
        $html .= '<option ' . $selected . '  value="0" >' . __( 'not selected', 'wbl' ) . '</option>';
        foreach ( $arr_users as $user ) {
            if( $user->id == $this->value ){
                $selected = ' selected ';
            } else {
                $selected = '';
            }
            $html .= '<option ' . $selected . '  value="' . $user->ID . '" >' . $user->user_login . '</option>';
        }
        $html .= '</select>';
        return $html;
    }


}
