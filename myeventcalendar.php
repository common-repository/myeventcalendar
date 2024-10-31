<?php
/**
 * @package MyEventCalendar
 * @copyright Machine-Rally-developers
 * 
* Plugin Name: MyEventCalendar
* Plugin URI: https://machine-rally-developers.com/index.php/products/my-event-calendar/about-my-event-calendar
* Version: 1.0.0
* Requires at least: 5.3
* Requires PHP:      7.2.1
* Author: Machine Rally Developers
* Author URI: https://machine-rally-developers.com
* Licence: GPLv2 or later
* Description: Do you want to display your events on your site, then MyEventCalendar may be what you want. Add [myeventcalendar] to an empty page
*/

if(!function_exists('add_action')){
	die;
}

define('MEC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MEC_PLUGIN_NAME', plugin_basename( __FILE__ ));
define('MEC_PLUGIN_VERSION', "1.0.0");
define('MEC_MYEVENTCALENDAR_PLUGIN', true);
require_once(MEC_PLUGIN_DIR.'classes.mec.php');
add_action( 'init', array('MyEventCalendar', 'mec_init') );

add_action('load_default_setting', array('MyEventCalendar', 'mec_set_default_settings'));
add_action('create_plugin_calendar_table', array('MyEventCalendar', 'mec_create_plugin_calendar_table'));
register_activation_hook(__FILE__, array('MyEventCalendar', 'mec_activate'));
register_deactivation_hook(__FILE__, array('MyEventCalendar', 'mec_deactivate'));
if(is_admin()){
    require_once(MEC_PLUGIN_DIR.'admin/mec-admin.php');
    add_action('admin_menu', array('MyEventCalendarAdmin', "mec_add_admin_page"));    
    
}
else{
    
   
}
?>
