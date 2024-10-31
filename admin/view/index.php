
<?php 
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        if(!empty($_POST['theme']) && isset($_POST['theme'])){
            $themes = array('panther', 'santa', 'easterCrunch', 'lucky', 'halloween', 'greyJoy','default');
            $selectedTheme =  sanitize_text_field($_POST['theme'] );
            if(in_array($selectedTheme, $themes)){
                if(FALSE === get_option('mec_settings') ){
                    do_action( 'load_default_setting');
                }
               $options = ["theme"=>$selectedTheme] ;

               update_option('mec_settings', $options);
                echo '<div class="alert alert-success mt-1" role="alert"> Settings saved</div>'; 
            } 
            else{
                echo '<div class="alert alert-danger mt-1" role="alert">Invalid theme</div>' ;
            }    
                
           
        }
        else{
            echo '<div class="alert alert-danger mt-1" role="alert">Error saving settings</div>' ;
        }
    }

?>
<div class="wrap" >
    <h4>My Event Calendar Plugin</h4>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Settings</a>
        </li>
        <!-- <li class="nav-item">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#add-events" role="tab" aria-controls="add-events" aria-selected="false">Add Events</a>
        </li> -->
        <li class="nav-item">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">About</a>
        </li>
        
    </ul>
    <div class="tab-content" id="myTabContent">
    <?php 
        $options_r = get_option('mec_settings');
        $currentTheme = esc_html($options_r['theme']);
    
    ?>
        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
            <form method="POST">
                <div class="col-12">
                
                            <fieldset>
                                        <label><strong>Select Theme</strong></label>
                                        <select name="theme" id="theme">
                                            <?php 
                                            $themes = array('panther', 'santa', 'easterCrunch', 'lucky', 'halloween', 'greyJoy','default');
                                            foreach($themes as $theme):

                                            ?>
                                            <option value = "<?php echo esc_html($theme);?>" <?php if($currentTheme ===$theme){echo "selected";} else{echo "";}?>><?php echo esc_html($theme) ?></option>
                                            <?php endforeach;?>
                                        </select>
                            </fieldset>
                    
                </div>
                <hr>
                <div class="col-12 mt-2"><button class="btn-primary btn" type="submit">Save</button></div>
            </form>
        </div>
        <!-- <div class="tab-pane fade mt-4" id="add-events" role="tabpanel" aria-labelledby="add-events-tab">
            <form method="POST">
                <label>Start Date<label>
                <label>End Date<label>
                <label>Location<label>
                <label>Color<label>
                <label>Recurring?<label>
                <hr>
                <div class="col-12 mt-2"><button class="btn-primary btn" type="submit">Save</button></div>
            </form>
        </div> -->
        <div class="tab-pane fade mt-4" id="profile" role="tabpanel" aria-labelledby="profile-tab">Plugin developed by Machine Rally Developers<br> Version <?php esc_html_e( MEC_PLUGIN_VERSION, 'text_domain' ) ;?></div>
        
    </div>
</div>

