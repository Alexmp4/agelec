<?php

class WordPress_Store_Locator_Delete
{
    private $plugin_name;
    private $version;

    /**
     * Construct Store Locator Admin Class
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @param   string                         $plugin_name
     * @param   string                         $version    
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->notice = "";
    }

    public function init()
    {
        add_action( 'admin_notices', array($this, 'notice' ));

        if ( ! is_admin()) {
            $this->notice .= "You are not an admin";
            return FALSE;
        }
        $stores = $this->get_stores();

        if(empty($stores)) {
            $this->notice .= "No Stores to Delete";
            return FALSE;
        }
        
        $this->delete($stores);
    }

    public function get_stores()
    {
        $args = array(
            'posts_per_page'   => -1,
            'post_type'        => 'stores',
            'post_status'      => 'any',
            'fields'           => 'ids',
        );
        $stores = get_posts( $args );
        return $stores;
    }

    public function delete($stores)
    {
        foreach ($stores as $store) {
            if(wp_delete_post($store)) {
                $this->notice .=  "Deleted: ". $store . "</br/>";
            } else {
                $this->notice .=  "Not Deleted: ". $store;
            }
        }
    }

    public function notice()
    {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo $this->notice ?></p>
        </div>
        <?php
    }
}