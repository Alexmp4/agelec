<?php
//WBK service categories table class
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
class WBK_Service_Categories_Table extends SLFTable {
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


            $field = new SLFField( array( 'title' => __( 'Services','wbk' ),     
                                         'name' => 'category_list',
                                         'format' => '%s',
                                         'component' => 'SLFTableServiceMultiSelect',                                         
                                         'render_cell' => true,
                                         'render_control' => true
                                          )

                                 );
            $this->field_set->append( $field );
 

            $this->table_name = 'wbk_service_categories';
 
            $filter = new SLFTableFilterAccess( '' , 'id' );
            $filter->setDefault();
            $this->filter_set['id'] = $filter;            
    }
   
}