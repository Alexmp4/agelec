<?php
//WBK email templates table class
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
class WBK_Email_Templates_Table extends SLFTable {
	public function __construct() {
			
            
			$this->field_set = new SLFFieldSet( true, true );

            $field = new SLFField( array( 'title' => __( 'Name','wbk' ),     
                                         'name' => 'name',
                                         'format' => '%s',
                                         'component' => 'SLFTableText',                                     
                                         'render_cell' => true,
                                         'render_control' => true,
                                         'validation' => array( array( 'SLFValidator', 'checkNameField' ), array(1,128)  )
                                          )

                                 );
            $this->field_set->append( $field );


            $field = new SLFField( array( 'title' => __( 'Template','wbk' ),     
                                         'name' => 'template',
                                         'format' => '%s',
                                         'component' => 'SLFTableHtmlEditor',                                         
                                         'render_cell' => false,
                                         'render_control' => true,
                                          'validation' => array( array( 'SLFValidator', 'checkEmailTemplateField' ), array(0,20000)  )
                                          )

                                 );
            $this->field_set->append( $field );
 

            $this->table_name = 'wbk_email_templates';
 
            $filter = new SLFTableFilterAccess( '' , 'id' );
            $filter->setDefault();
            $this->filter_set['id'] = $filter;            
	}
   
}