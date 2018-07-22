<?php
/*
Plugin Name: Additional Functions
Plugin URI: https://example.com/
Description: Additional functions for this site is in this plugin
Version: 0.1
Author: Author
Author URI: https://example.com/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: af
Domain Path: /languages
*/

/**
 * Copyright (c) YEAR Author (email: Email). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

// don't call the file directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Additional_Functions class
 *
 * @class Additional_Functions The class that holds the entire Additional_Functions plugin
 */
class Additional_Functions {

    /**
     * Plugin version
     *
     * @var string
     */
    public $version = '0.1.0';

    /**
     * Constructor for the Additional_Functions class
     *
     * Sets up all the appropriate hooks and actions
     * within our plugin.
     *
     * @uses register_activation_hook()
     * @uses register_deactivation_hook()
     * @uses is_admin()
     * @uses add_action()
     */
    public function __construct() {

        $this->define_constants();

        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

        $this->includes();
        $this->init_hooks();
    }

    /**
     * Define the constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'AF_VERSION', $this->version );
        define( 'AF_FILE', __FILE__ );
        define( 'AF_PATH', dirname( AF_FILE ) );
        define( 'AF_INCLUDES', AF_PATH . '/includes' );
        define( 'AF_URL', plugins_url( '', AF_FILE ) );
        define( 'AF_ASSETS', AF_URL . '/assets' );
        define( 'AF_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
    }

    /**
     * Initializes the Additional_Functions() class
     *
     * Checks for an existing Additional_Functions() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( ! $instance ) {
            $instance = new Additional_Functions();
        }

        return $instance;
    }

    /**
     * Placeholder for activation function
     *
     * Nothing being called here yet.
     */
    public function activate() {

        update_option( 'af_version', AF_VERSION );
    }

    /**
     * Placeholder for deactivation function
     *
     * Nothing being called here yet.
     */
    public function deactivate() {

    }

    /**
     * Include the required files
     *
     * @return void
     */
    public function includes() {

    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks() {
        // Localize our plugin
        add_action( 'init', array( $this, 'localization_setup' ) );

        // Loads frontend scripts and styles
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        add_filter( 'dokan_get_dashboard_nav', array( $this, 'add_service_page' ), 11, 1 );
        add_action( 'dokan_load_custom_template', array( $this, 'load_template_from_plugin' ) );
        add_filter( 'dokan_query_var_filter', array( $this, 'register_service_queryvar' ) );
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'af', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Enqueue admin scripts
     *
     * Allows plugin assets to be loaded.
     *
     * @uses wp_enqueue_script()
     * @uses wp_localize_script()
     * @uses wp_enqueue_style
     */
    public function enqueue_scripts() {

        /**
         * All styles goes here
         */
        wp_enqueue_style( 'af-styles', plugins_url( 'assets/css/style.css', __FILE__ ), false, date( 'Ymd' ) );

        /**
         * All scripts goes here
         */
        wp_enqueue_script( 'af-scripts', plugins_url( 'assets/js/script.js', __FILE__ ), array( 'jquery' ), false, true );


        /**
         * Example for setting up text strings from Javascript files for localization
         *
         * Uncomment line below and replace with proper localization variables.
         */
        // $translation_array = array( 'some_string' => __( 'Some string to translate', 'af' ), 'a_value' => '10' );
        // wp_localize_script( 'base-plugin-scripts', 'af', $translation_array ) );

    }


    /**
     * Add menu on seller dashboard
     * @since 1.0
     * @param array $urls
     * @return array $urls
     */
    function add_service_page( $urls ) {

        $urls['service'] = array(
            'title' => __( 'Service', 'dokan' ),
            'icon'  => '<i class="fa fa-car"></i>',
            'url'   => dokan_get_navigation_url( 'service' ),
            'pos'   => 35,
        );

        return $urls;
    }

    /**
     * Register page templates
     *
     * @since 1.0
     *
     * @param array $query_vars
     *
     * @return array $query_vars
     */
    function load_template_from_plugin( $query_vars ) {
// var_dump($query_vars);
        if ( isset( $query_vars['service'] ) ) {
            add_filter( 'dokan_set_template_path', array( $this, 'af_set_template_path' ), 10 );
            dokan_get_template_part( 'service/service', '', array( 'pro'=>true ) );
            return;
        }
    }

    /**
     * Load template path
     *
     * @since 2.4
     *
     */
    public function af_set_template_path($path='') {
        return AF_PLUGIN_PATH . '/templates';
    }

    /**
     * Register dokan query vars
     *
     * @since 1.0
     *
     * @param array $vars
     *
     * @return array new $vars
     */
    function register_service_queryvar( $vars ) {
        $vars[] = 'service';
        return $vars;
    }

} // Additional_Functions

$af = Additional_Functions::init();
