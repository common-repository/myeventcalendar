<?php
/**
 * Uninstall plugin 
 * 
 */
defined('WP_UNINSTALL_PLUGIN') or die();

//delete options
delete_option('mec_settings');

//delete database table
 if(get_option('mec_tables_created')) {

            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            $ptbd_table_name = $wpdb->prefix . 'calendar_events';

            if ($wpdb->get_var("SHOW TABLES LIKE '". $ptbd_table_name ."'"  ) == $ptbd_table_name ) {

           
                $wpdb->query("DROP TABLE ".$ptbd_table_name);
                //delete option
                delete_option('mec_tables_created');
            }
}