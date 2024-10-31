<?php
defined('MEC_MYEVENTCALENDAR_PLUGIN') or die('Forbidden');
class MyEventCalendarAdmin{

    function mec_add_admin_page(){

        add_menu_page('MEC Plugin', 'MECalendar', 'manage_options', 'myeventcalendar_plugin', array('MyEventCalendarAdmin', "mec_index_page"), plugins_url( "assets/images/calendar.png", __FILE__ ), 110);
              
    }
    
    function mec_index_page(){
        
        require_once plugin_dir_path( __FILE__ ).'view/index.php';
    }
    
}

?>