<?php 
defined( 'ABSPATH' ) or die( '' ); // Prevents direct file access
/*
Plugin Name: HCS Alert
Description: System for backend alerts on the website (snow days, etc)
Version:     1.0
Author:      Brett Goodrich
Author URI:  brettgoodrich.com
*/

function hcs_alert() {
	return get_option('hcs_alert_data');
}

class HCSAlert
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        add_submenu_page(
			'edit.php', // Goes under Posts
            'HCS Alert', // Title that goes in h2 on settings page
            'HCS Alert', // Title that goes into the WordPress nav menu
            'manage_options', // User permissions required to see, access, and edit this settings page
            'hcs_alert_menu', // The slug to refer to this menu by
            array( $this, 'create_admin_page' ) // Callback function that creates the settings page. $this makes it look in this class for the function.
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'hcs_alert_data' );
        ?>
        <div class="wrap">
            <h2>HCS Alert</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'hcs_alert_data_group' );   
                do_settings_sections( 'hcs_alert_menu' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'hcs_alert_data_group', // Option group
            'hcs_alert_data', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'hcs_alert_data_section', // ID
            'Alert Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'hcs_alert_menu' // Page
        );  

        add_settings_field(
            'alert_title', // ID
            'Title', // Title 
            array( $this, 'alert_title_callback' ), // Callback
            'hcs_alert_menu', // Page
            'hcs_alert_data_section' // Section           
        );      

        add_settings_field(
            'alert_desc', 
            'Description', 
            array( $this, 'alert_desc_callback' ), 
            'hcs_alert_menu', 
            'hcs_alert_data_section'
        ); 
		
        add_settings_field(
            'alert_enbl', 
            'Enabled', 
            array( $this, 'alert_enbl_callback' ), 
            'hcs_alert_menu', 
            'hcs_alert_data_section'
        ); 
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['alert_title'] ) )
            $new_input['alert_title'] = sanitize_text_field( $input['alert_title'] );

        if( isset( $input['alert_desc'] ) )
            $new_input['alert_desc'] = sanitize_text_field( $input['alert_desc'] );

        if( isset( $input['alert_enbl'] ) )
            $new_input['alert_enbl'] = absint( $input['alert_enbl'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'When enabled, this will put a red box with this information on the homepage. Useful for snow days, delays, etc.';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function alert_title_callback()
    {
        printf(
            '<input type="text" id="alert_title" name="hcs_alert_data[alert_title]" value="%s" />',
            isset( $this->options['alert_title'] ) ? esc_attr( $this->options['alert_title']) : ''
        );
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function alert_desc_callback()
    {
        printf(
            '<input type="text" id="alert_desc" name="hcs_alert_data[alert_desc]" value="%s" style="width:90%%;" />',
            isset( $this->options['alert_desc'] ) ? esc_attr( $this->options['alert_desc']) : ''
        );
    }
	
    public function alert_enbl_callback()
    {
		echo '<input type="checkbox" id="alert_enbl" name="hcs_alert_data[alert_enbl]" value="1"';
//		print_r($this->options['alert_enbl']);
		if($this->options['alert_enbl']) echo ' checked="checked"';
		echo '/> Enabled if checked';
    }
	
}

if( is_admin() )
    $my_settings_page = new HCSAlert();