<?php
//WBK service categories table class
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
class WBK_Coupons_Table extends SLFTable {
    public function __construct() {
            
            
            $this->field_set = new SLFFieldSet( true, true );

            $field = new SLFField( array( 'title' => __( 'Coupon','wbk' ),     
                                         'name' => 'name',
                                         'format' => '%s',
                                         'component' => 'SLFTableText',                                     
                                         'render_cell' => true,
                                         'render_control' => true,
                                         'validation' => array( array( 'SLFValidator', 'checkNameField' ), array(1,128)  )
                                          )

                                 );
            $this->field_set->append( $field );

            $field = new SLFField( array( 'title' => __( 'Usage limit','wbk' ),     
                                         'name' => 'maximum',
                                         'format' => '%d',
                                         'component' => 'SLFTableText',                                     
                                         'render_cell' => true,
                                         'render_control' => true,
                                         'validation' => array( array( 'SLFValidator', 'checkIntegerOrNull' ), array( 1, 100000000 )  )
                                          )

                                 );
            $this->field_set->append( $field );

            $field = new SLFField( array( 'title' => __( 'Available on','wbk' ),     
                                         'name' => 'date_range',
                                         'format' => '%s',
                                         'component' => 'SLFTableText',
                                         'render_cell' => true,
                                         'render_control' => true
                                         
                                        )
                                 );
            $this->field_set->append( $field );
 

            $field = new SLFField( array( 'title' => __( 'Services','wbk' ),     
                                         'name' => 'services',
                                         'format' => '%s',
                                         'component' => 'SLFTableServiceMultiSelect',                                         
                                         'render_cell' => true,
                                         'render_control' => true
                                          )

                                 );
            $this->field_set->append( $field );

            $field = new SLFField( array( 'title' => __( 'Discount (percentage)','wbk' ),     
                                         'name' => 'amount_percentage',
                                         'format' => '%d',
                                         'component' => 'SLFTableText',                                     
                                         'render_cell' => true,
                                         'render_control' => true,
                                         'validation' => array( array( 'SLFValidator', 'checkIntegerOrNull' ), array( 0, 100 )  )
                                          )

                                 );
            $this->field_set->append( $field );


   			$field = new SLFField( array( 'title' => __( 'Discount (fixed)','wbk' ),     
                                         'name' => 'amount_fixed',
                                         'format' => '%d',
                                         'component' => 'SLFTableText',                                     
                                         'render_cell' => true,
                                         'render_control' => true,
                                         'validation' => array( array( 'SLFValidator', 'checkIntegerOrNull' ), array( 0, 10000000 )  )
                                          )

                                 );


            $this->field_set->append( $field );


            $this->table_name = 'wbk_coupons';
 
            $filter = new SLFTableFilterAccess( '' , 'id' );
            $filter->setDefault();
            $this->filter_set['id'] = $filter;            
    }
   
}