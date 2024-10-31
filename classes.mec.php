<?php
defined('MEC_MYEVENTCALENDAR_PLUGIN') or die('Forbidden');
class MyEventCalendar{
	function mec_init(){
		
        add_action( 'admin_enqueue_scripts', array('MyEventCalendar', 'mec_load_admin_resources'));        
        add_filter( 'plugin_action_links_'.MEC_PLUGIN_NAME, array('MyEventCalendar', 'mec_settings_link'));
        add_action('wp_enqueue_scripts',array('MyEventCalendar', 'mec_load_client_resources'));  
        add_action("wp_ajax_delete_data", array('MyEventCalendar','mec_do_something'));
        add_action("wp_ajax_post_data", array('MyEventCalendar','mec_do_something'));
        add_action("wp_ajax_update_data", array('MyEventCalendar','mec_do_something'));
        add_action("wp_ajax_get_data", array('MyEventCalendar','mec_do_something'));
        add_shortcode('myeventcalendar', array('MyEventCalendar','mec_loadCalendar'));
        add_filter('change_array_keys_for_calendar_events', array('MyEventCalendar','mec_update_keys_for_calendar_events'));
        //add_filter('the_content',  array('MyEventCalendar','loadCalendar'));
        
		
    }
    function mec_update_keys_for_calendar_events(array $array_values){
        $keyToReplace = array(
            "id"=>"id",
            "publisher"=>"publisher",
            "title"=>"title",
            "backgroundcolor"=>"backgroundColor",
            "endrecur"=>"endRecur",
            "start"=>"start",
            "end"=>"end",
            "daysofweek"=>"daysOfWeek",
            "location"=>"location"
        );
        foreach($array_values as $old){
            array_combine(array_merge($old, $keyToReplace), $old);
        }
        return $array_values;
    }
    //filter
  
    // add custom settings link
    function mec_settings_link($links){
        $settings_link = '<a href="admin.php?page=myeventcalendar_plugin">Settings</a>';
        array_push($links, $settings_link);
        return $links;
    }
	function mec_activate(){
       
        do_action( 'load_default_setting');
        do_action('create_plugin_calendar_table');
		flush_rewrite_rules();
	}
	function mec_deactivate(){
		flush_rewrite_rules();
    }
    function mec_create_plugin_calendar_table(){
        if(get_option('mec_tables_created')===FALSE) {

            global $wpdb;
            $charset_collate = $wpdb->get_charset_collate();
            $ptbd_table_name = $wpdb->prefix . 'calendar_events';

            if ($wpdb->get_var("SHOW TABLES LIKE '". $ptbd_table_name ."'"  ) != $ptbd_table_name ) {

                $sql  = 'CREATE TABLE '.$ptbd_table_name.' (
                id INT(20) AUTO_INCREMENT,
                publisher VARCHAR(255) NOT NULL,
                title VARCHAR(50) NOT NULL,
                start datetime NOT NULL,
                end datetime NOT NULL,
                backgroundcolor VARCHAR(20) NOT NULL,
                daysofweek VARCHAR(255),
                endrecur datetime,
                location VARCHAR(255),
                PRIMARY KEY  (id)
                ) '.$charset_collate.';';

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
                update_option('mec_tables_created', true);
            }
        }
    }
    function mec_set_default_settings(){
        if(get_option('mec_settings')===FALSE){
            $options = ["theme"=>"default"];
            add_option( 'mec_settings', $options);
        }
     
    }
 
	function mec_load_admin_resources(){		
        
        //-3.4.1.min        
        wp_enqueue_script( 'bootstrap_js', plugins_url("/admin/assets/js/bootstrap.js",__FILE__), array('jquery'), null,'all');
        wp_enqueue_script( 'jqueryui_js', plugins_url("/admin/assets/js/jquery-ui.min.js",__FILE__), array('jquery'), null,'all');
        wp_enqueue_style( 'bootstrap_css', plugins_url("/admin/assets/css/bootstrap.css",__FILE__), array(), null,'all');
        wp_enqueue_style( 'jqueryui_css', plugins_url("/admin/assets/css/jquery-ui.min.css",__FILE__), array(), null,'all');
        wp_enqueue_style( 'custom_css', plugins_url("/admin/assets/css/custom.css",__FILE__), array(), null,'all');
        wp_enqueue_style( 'jqueryui_structure_css', plugins_url("/admin/assets/css/jquery-ui.structure.min.css",__FILE__));
	}
	function mec_load_client_resources(){
        wp_enqueue_script( 'myeventcalendar', plugins_url("/frontend/assets/js/myeventcalendar.js",__FILE__), array(), null,'all');
        wp_enqueue_script( 'load-calendar', plugins_url("/frontend/assets/js/load-calendar.js",__FILE__), array('jquery'), null,true);
        //get array of calendar events
         global $wpdb;
        $ptbd_table_name = $wpdb->prefix . 'calendar_events';
        $results = $wpdb->get_results("SELECT * FROM $ptbd_table_name");
        //$results = apply_filters( 'change_array_keys_for_calendar_events', $results );
        $results = json_encode($results);
        //localize script must be the same name as the script enqueued
        wp_localize_script( 'load-calendar', 'frontend_ajax_object',array('ajaxurl' =>admin_url( 'admin-ajax.php' ), 'calendar_data'=>"$results", 'isAdministrator'=>is_admin()));       
   
        wp_enqueue_style( 'bootstrap_css', plugins_url("/admin/assets/css/bootstrap.css",__FILE__));
    }
    function mec_loadCalendar($content){
       
        $option = get_option('mec_settings');
        $theme = $option['theme'];
        return '<div data-theme='.$theme.' id="calendar-wrap"><div id="myeventcalendar" ></div><div>';
    }
    function mec_do_something(){
        
        if ( is_user_logged_in() )
        {
            $action = sanitize_text_field($_POST['action']);
            if($action =="delete_data" ||$action =="post_data" ){
                global $current_user;
                $user_role = $current_user->roles[0];
                
                if($user_role == 'administrator')
                { 
                    //check if its a post data
                    if($action =="post_data"){
                        global $wpdb;
                        $ptbd_table_name = $wpdb->prefix . 'calendar_events';
                        $publisher = sanitize_text_field($_POST["publisher"]);
                        $start = sanitize_text_field($_POST['start']);
                        $end = sanitize_text_field($_POST['end']);
                        $location = sanitize_text_field($_POST['location']);
                        $endRecur= sanitize_text_field($_POST['endRecur']);
                        $backgroundColor = sanitize_text_field($_POST['backgroundColor']);
                        $daysOfWeek = sanitize_text_field($_POST['daysOfWeek']);
                        $title = sanitize_text_field($_POST['title']);
                        if(!empty($publisher)&&!empty($start)&&!empty($end) && !empty($title)){
                            //create an array for the event info
                                $dataArray = array();
                                $dataArray['publisher']=$publisher ;
                                $dataArray['start']=$start;
                                $dataArray['end']=$end;
                                $dataArray['title'] =$title;
                                if(!empty($location))$dataArray['location']=$location;
                                if(!empty($endRecur))$dataArray['endrecur']=$endRecur;
                                if(!empty($backgroundColor))$dataArray['backgroundcolor']=$backgroundColor;
                                if(!empty($daysOfWeek))$dataArray['daysofweek']=$daysOfWeek.explode(",");
                                
                                echo $wpdb->insert($ptbd_table_name, $dataArray);
                                //$results = apply_filters( 'change_array_keys_for_calendar_events', $results );
                                //$results = $wpdb->get_results("SELECT id,location, start, end, daysofweek as daysOfWeek, publisher, title, endrecur as endRecur, backgroundcolor as backgroundColor  FROM $ptbd_table_name"));
                                wp_die();
                        }
                        else{
                            
                            wp_die();
                        }
                    }
                    else if($action =="delete_data"){
                        if(!empty($_POST['id'])){
                            $id = sanitize_title($_POST['id']);
                            global $wpdb;
                            $ptbd_table_name = $wpdb->prefix . 'calendar_events';
                            $wpdb->delete($ptbd_table_name, array('id'=>$id));
                            $results=$wpdb->get_results("SELECT id,location, start, end, daysofweek as daysOfWeek, publisher, title, endrecur as endRecur, backgroundcolor as backgroundColor  FROM $ptbd_table_name");
                            //$results = apply_filters( 'change_array_keys_for_calendar_events', $results );
                            $results = json_encode($results);
                            echo $results;
                            wp_die();

                        }
                        else{
                            wp_die();
                        }
                        
                    }
                }
                else{
                    wp_die();
                }
            }
            else{
                global $wpdb;
                $ptbd_table_name = $wpdb->prefix . 'calendar_events';
                $results = $wpdb->get_results("SELECT id,location, start, end, daysofweek as daysOfWeek, publisher, title, endrecur as endRecur, backgroundcolor as backgroundColor FROM $ptbd_table_name",ARRAY_A );
                //$results = apply_filters( 'change_array_keys_for_calendar_events', $results );
                $results = json_encode($results);
                echo $results;
                wp_die();
            }
            
        }
        else{
            global $wpdb;
            $ptbd_table_name = $wpdb->prefix . 'calendar_events';
            $results = $wpdb->get_results("SELECT id,location, start, end, daysofweek as daysOfWeek, publisher, title, endrecur as endRecur, backgroundcolor as backgroundColor * FROM $ptbd_table_name",ARRAY_A);
            //$results = apply_filters( 'change_array_keys_for_calendar_events', $results );
            $results = json_encode($results);
            echo $results;
            wp_die();
        }
        
    }
}
?>