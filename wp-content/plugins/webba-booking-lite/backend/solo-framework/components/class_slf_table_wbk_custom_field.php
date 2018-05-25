<?php
// Solo Framework table wbk custom field component
if ( ! defined( 'ABSPATH' ) ) exit;
class SLFTableWbkCustomField extends SLFTableComponent {
	public function __construct( $title, $name, $value, $data_source  ) {
		parent::__construct( $title, $name, $value, null );
	}
    public function renderCell(){
        $extra = json_decode( $this->value );
        $ids = get_option( 'wbk_custom_fields_columns', '' );
        if( $ids != ''  ){
            $ids = explode( ',', $ids );
            $result = array();
            foreach( $ids as $id ){
                $iter_value = '';
                foreach( $extra as $item ){
                    
                    if( count( $item ) <> 3 ){
                        continue;               
                    }
                    if( $item[0] == $id ){
                        $iter_value = $item[2];
                    }
                   
                }
                $result[] = $iter_value;
            }
            $html = '';
            foreach ( $result as $temp_result ) {
                $html .= '<td>' . $temp_result . '</td>';
            }
            return $html;
        }           	
    	$result = array();
    	foreach( $extra as $item ){
    		if( count( $item ) <> 3 ){
    			continue;    			
    		}
    		$result[] = $item[1] . ': ' . $item[2]; 
    	}    	
		return implode( ', ', $result );
    }
    public function renderControl(){
    	$extra = json_decode( $this->value );
    	$html = '';
		foreach( $extra as $item ){
    		if( count( $item ) <> 3 ){
    			continue;    			
    		}
			$html .= '<label class="slf_table_component_label" >' . $item[1]  . '</label>';
			$html .= '<input type="text"  data-id="' . $item[0] . '"  data-label="' . $item[1] . '" class="slf_table_component_text slf_table_custom_field_part"  value="' . $item[2]  . '"  />';

    	}  
		$html .= '<input style="width: 1000px" class="slf_table_component_input slf_hidden slf_table_component_custom_field" name="' . $this->name . '"   value="' . htmlentities( $this->value ) . '"  />';
		return $html;
    }
}
