<?php
// Solo Framework table html editor
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFTableHtmlEditor extends SLFTableComponent {


	public function __construct( $title, $name, $value, $data_source  ) {
		parent::__construct( $title, $name, $value, null );
	}
	
    public function renderCell(){
		 	

    }
    public function renderControl(){
        $id = uniqid();
    	$editor_contents = '<textarea  class="slf_table_component_input slf_table_component_editor" name="' . $this->name . '"  style="height:300px;" id="'. $id .'">' . htmlspecialchars_decode( stripslashes( $this->value ) ) . '</textarea>';

        $controls = '<div id="wp-'.$id.'-editor-tools" class="wp-editor-tools hide-if-no-js"><div id="wp-'.$id.'-media-buttons" class="wp-media-buttons"><button type="button" id="insert-media-button" class="button insert-media add_media" data-editor="'.$id.'"><span class="wp-media-buttons-icon"></span> Add Media</button></div>           
            </div>';
		return  '<div class="slf-editor-wrap">' . $controls . $editor_contents . '</div>';
    }


}
