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

        add_filter( 'product_type_selector', array( $this, 'add_service_type' ) );

        add_filter( 'dokan_get_dashboard_nav', array( $this, 'add_service_page' ), 11, 1 );
        add_action( 'dokan_load_custom_template', array( $this, 'load_template_from_plugin' ) );

        add_filter( 'dokan_query_var_filter', array( $this, 'register_service_queryvar' ) );
        add_filter( 'dokan_product_listing_exclude_type', array( $this, 'filter_service_type_product' ) );

        add_filter( 'dokan_dashboard_nav_active', array( $this, 'set_service_menu_as_active' ) );

        add_filter( 'dokan_add_new_product_redirect', array( $this, 'set_redirect_url' ), 10, 2 );

        add_action( 'admin_footer', array( $this, 'show_service_admin_custom_js' ) );
        add_filter( 'product_type_options', array( $this, 'show_service_virtual' ) );

        add_action( 'dokan_settings_after_banner', array( $this, 'dokan_settings_fields_after_banner' ), 10, 2 );
        add_action( 'dokan_store_profile_saved',  array( $this, 'dokan_store_profile_fields_saved' ), 10, 2 );

        add_filter( 'dokan_query_var_filter', array( $this, 'load_custom_query_var' ) );
        add_filter( 'template_include', array( $this,  'store_custom_template' ), 100 );

        add_action( 'dokan_rewrite_rules_loaded', array( $this, 'dokan_custom_rewrite_rules_loaded' ), 11 );
        add_filter( 'dokan_store_tabs',  array( $this, 'dokan_store_additional_tabs' ), 10, 2 );

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

    public function add_service_type ( $type ) {
        // Key should be exactly the same as in the class product_type
        $type[ 'service' ] = __( 'Service' );
        return $type;
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
        if ( isset( $query_vars['service'] ) ) {
            include ( AF_PLUGIN_PATH . '/templates/service/service.php' );
        }
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

    /**
     * Register dokan query vars
     *
     * @since 1.0
     *
     * @param array $vars
     *
     * @return array new $vars
     */
    function filter_service_type_product( $type ) {
        $type[] = 'service';
        return $type;
    }

    /**
     * Highlight Service menu as active on Dokan Dashboard
     *
     * @since 1.0
     *
     * @param string $active_menu
     *
     * @return string
     */
    function set_service_menu_as_active( $active_menu ) {
        if ( 'service/new-product' == $active_menu || 'service/edit' == $active_menu ) {
            return 'service';
        }
        return $active_menu;
    }

    /**
     * Filter Redirect url after new service product added
     *
     * @since 1.0
     *
     * @param string $url
     *
     * @param int $product_id
     *
     * @return $url
     */
    function set_redirect_url( $url, $product_id ) {

        $product_type = isset( $_POST['product_type'] ) ? $_POST['product_type'] : '';
        $tab          = isset( $_GET['tab'] ) ? $_GET['tab'] : '';

        if ( 'service' == $product_type ) {
            $url = add_query_arg( array( 'product_id' => $product_id ), dokan_get_navigation_url( 'service' ) . 'edit/' );
            return $url;
        }

        if ( 'service' == $tab ) {
            $url = add_query_arg( array(), dokan_get_navigation_url( 'service' ) );
            return $url;
        }

        return $url;
    }

    public function show_service_admin_custom_js() {

        if ('product' != get_post_type()) :
            return;
        endif;
        ?>
        <script type='text/javascript'>
            jQuery(document).ready(function () {
                //for Price tab
                jQuery('.product_data_tabs .general_tab').addClass('show_if_service').show();
                jQuery('#general_product_data .pricing').addClass('show_if_service').show();
                //for Inventory tab
                jQuery('.inventory_options').addClass('show_if_service').show();
                jQuery('#inventory_product_data ._manage_stock_field').addClass('show_if_service').show();
                jQuery('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_service').show();
                jQuery('#inventory_product_data ._sold_individually_field').addClass('show_if_service').show();
            });
        </script>
        <?php

    }

    public function show_service_virtual( $options ) {
        $options['virtual']['wrapper_class'] .= ' show_if_service';
        return $options;
    }

    /**
     * Add store settings fields
     *
     * @since 1.0
     *
     */
    function dokan_settings_fields_after_banner( $current_user, $profile_info ) {

        $b_description   = isset( $profile_info['b_description'] ) ? $profile_info['b_description'] : '' ;
        $gallery_1       = isset( $profile_info['gallery_1'] ) ? absint( $profile_info['gallery_1'] ) : 0;
        $gallery_2       = isset( $profile_info['gallery_2'] ) ? absint( $profile_info['gallery_2'] ) : 0;
        $gallery_3       = isset( $profile_info['gallery_3'] ) ? absint( $profile_info['gallery_3'] ) : 0;
        // $gallery_4       = isset( $profile_info['gallery_4'] ) ? absint( $profile_info['gallery_4'] ) : 0;
        // $gallery_5       = isset( $profile_info['gallery_5'] ) ? absint( $profile_info['gallery_5'] ) : 0;
        ?>


        <div class="dokan-form-group" id="dokan_tnc_text">
            <label class="dokan-w3 dokan-control-label" for="dokan_b_description"><?php _e( 'Business Description', 'dokan-lite' ); ?></label>
            <div class="dokan-w8 dokan-text-left">
                <?php
                    $settings = array(
                        'editor_height' => 200,
                        'media_buttons' => false,
                        'teeny'         => true,
                        'quicktags'     => false
                    );
                    wp_editor( $b_description, 'dokan_b_description', $settings );
                ?>
            </div>
        </div>


        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="dokan_gallery_1"><?php _e( 'Gallery Photo 1', 'dokan-lite' ); ?></label>

            <div class="dokan-w5 dokan-gravatar">
                <div class="dokan-left gravatar-wrap<?php echo $gallery_1 ? '' : ' dokan-hide'; ?>">
                    <?php $gallery_1_url = $gallery_1 ? wp_get_attachment_url( $gallery_1 ) : ''; ?>
                    <input type="hidden" class="dokan-file-field" value="<?php echo $gallery_1; ?>" name="dokan_gallery_1">
                    <img class="dokan-gravatar-img" src="<?php echo esc_url( $gallery_1_url ); ?>">
                    <a class="dokan-close dokan-remove-gravatar-image">&times;</a>
                </div>
                <div class="gravatar-button-area<?php echo $gallery_1 ? ' dokan-hide' : ''; ?>">
                    <a href="#" class="dokan-pro-gravatar-drag dokan-btn dokan-btn-default"><i class="fa fa-cloud-upload"></i> <?php _e( 'Upload Photo', 'dokan-lite' ); ?></a>
                </div>
            </div>
        </div>

        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="dokan_gallery_2"><?php _e( 'Gallery Photo 2', 'dokan-lite' ); ?></label>
            <div class="dokan-w5 dokan-gravatar">
                <div class="dokan-left gravatar-wrap<?php echo $gallery_2 ? '' : ' dokan-hide'; ?>">
                    <?php $gallery_2_url = $gallery_2 ? wp_get_attachment_url( $gallery_2 ) : ''; ?>
                    <input type="hidden" class="dokan-file-field" value="<?php echo $gallery_2; ?>" name="dokan_gallery_2">
                    <img class="dokan-gravatar-img" src="<?php echo esc_url( $gallery_2_url ); ?>">
                    <a class="dokan-close dokan-remove-gravatar-image">&times;</a>
                </div>
                <div class="gravatar-button-area<?php echo $gallery_2 ? ' dokan-hide' : ''; ?>">
                    <a href="#" class="dokan-pro-gravatar-drag dokan-btn dokan-btn-default"><i class="fa fa-cloud-upload"></i> <?php _e( 'Upload Photo', 'dokan-lite' ); ?></a>
                </div>
            </div>
        </div>

        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="dokan_gallery_3"><?php _e( 'Gallery Photo 3', 'dokan-lite' ); ?></label>
            <div class="dokan-w5 dokan-gravatar">
                <div class="dokan-left gravatar-wrap<?php echo $gallery_3 ? '' : ' dokan-hide'; ?>">
                    <?php $gallery_3_url = $gallery_3 ? wp_get_attachment_url( $gallery_3 ) : ''; ?>
                    <input type="hidden" class="dokan-file-field" value="<?php echo $gallery_3; ?>" name="dokan_gallery_3">
                    <img class="dokan-gravatar-img" src="<?php echo esc_url( $gallery_3_url ); ?>">
                    <a class="dokan-close dokan-remove-gravatar-image">&times;</a>
                </div>
                <div class="gravatar-button-area<?php echo $gallery_3 ? ' dokan-hide' : ''; ?>">
                    <a href="#" class="dokan-pro-gravatar-drag dokan-btn dokan-btn-default"><i class="fa fa-cloud-upload"></i> <?php _e( 'Upload Photo', 'dokan-lite' ); ?></a>
                </div>
            </div>
        </div>

        <!-- <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="dokan_gallery_4"><?php _e( 'Gallery Photo 4', 'dokan-lite' ); ?></label>
            <div class="dokan-w5 dokan-gravatar">
                <div class="dokan-left gravatar-wrap<?php echo $gallery_4 ? '' : ' dokan-hide'; ?>">
                    <?php $gallery_4_url = $gallery_4 ? wp_get_attachment_url( $gallery_4 ) : ''; ?>
                    <input type="hidden" class="dokan-file-field" value="<?php echo $gallery_4; ?>" name="dokan_gallery_4">
                    <img class="dokan-gravatar-img" src="<?php echo esc_url( $gallery_4_url ); ?>">
                    <a class="dokan-close dokan-remove-gravatar-image">&times;</a>
                </div>
                <div class="gravatar-button-area<?php echo $gallery_4 ? ' dokan-hide' : ''; ?>">
                    <a href="#" class="dokan-pro-gravatar-drag dokan-btn dokan-btn-default"><i class="fa fa-cloud-upload"></i> <?php _e( 'Upload Photo', 'dokan-lite' ); ?></a>
                </div>
            </div>
        </div>

        <div class="dokan-form-group">
            <label class="dokan-w3 dokan-control-label" for="dokan_gallery_5"><?php _e( 'Gallery Photo 5', 'dokan-lite' ); ?></label>
            <div class="dokan-w5 dokan-gravatar">
                <div class="dokan-left gravatar-wrap<?php echo $gallery_5 ? '' : ' dokan-hide'; ?>">
                    <?php $gallery_5_url = $gallery_5 ? wp_get_attachment_url( $gallery_5 ) : ''; ?>
                    <input type="hidden" class="dokan-file-field" value="<?php echo $gallery_5; ?>" name="dokan_gallery_5">
                    <img class="dokan-gravatar-img" src="<?php echo esc_url( $gallery_5_url ); ?>">
                    <a class="dokan-close dokan-remove-gravatar-image">&times;</a>
                </div>
                <div class="gravatar-button-area<?php echo $gallery_5 ? ' dokan-hide' : ''; ?>">
                    <a href="#" class="dokan-pro-gravatar-drag dokan-btn dokan-btn-default"><i class="fa fa-cloud-upload"></i> <?php _e( 'Upload Photo', 'dokan-lite' ); ?></a>
                </div>
            </div>
        </div> -->
        <?php
    }

    public function dokan_store_profile_fields_saved ( $store_id, $dokan_settings ) {
        if ( ! $store_id ) {
            return;
        }

        $dokan_settings = get_user_meta( $store_id, 'dokan_profile_settings', true );

        $dokan_settings['b_description']   = isset( $_POST['dokan_b_description'] ) ? $_POST['dokan_b_description'] : '';
        $dokan_settings['gallery_1']      = isset( $_POST['dokan_gallery_1'] ) ? absint( $_POST['dokan_gallery_1'] ) : null;
        $dokan_settings['gallery_2']      = isset( $_POST['dokan_gallery_2'] ) ? absint( $_POST['dokan_gallery_2'] ) : null;
        $dokan_settings['gallery_3']      = isset( $_POST['dokan_gallery_3'] ) ? absint( $_POST['dokan_gallery_3'] ) : null;
        // $dokan_settings['gallery_4']      = isset( $_POST['dokan_gallery_4'] ) ? absint( $_POST['dokan_gallery_4'] ) : null;
        // $dokan_settings['gallery_5']      = isset( $_POST['dokan_gallery_5'] ) ? absint( $_POST['dokan_gallery_5'] ) : null;

        update_user_meta( $store_id, 'dokan_profile_settings', $dokan_settings );

    }

    public function dokan_custom_rewrite_rules_loaded( $custom_store_url ) {
        add_rewrite_rule( $custom_store_url.'/([^/]+)/about?$', 'index.php?'.$custom_store_url.'=$matches[1]&about=true', 'top' );
        add_rewrite_rule( $custom_store_url.'/([^/]+)/about/page/?([0-9]{1,})/?$', 'index.php?'.$custom_store_url.'=$matches[1]&paged=$matches[2]&about=true', 'top' );

        add_rewrite_rule( $custom_store_url.'/([^/]+)/services?$', 'index.php?'.$custom_store_url.'=$matches[1]&services=true', 'top' );
        add_rewrite_rule( $custom_store_url.'/([^/]+)/services/page/?([0-9]{1,})/?$', 'index.php?'.$custom_store_url.'=$matches[1]&paged=$matches[2]&services=true', 'top' );

        add_rewrite_rule( $custom_store_url.'/([^/]+)/contact?$', 'index.php?'.$custom_store_url.'=$matches[1]&contact=true', 'top' );
        add_rewrite_rule( $custom_store_url.'/([^/]+)/contact/page/?([0-9]{1,})/?$', 'index.php?'.$custom_store_url.'=$matches[1]&paged=$matches[2]&contact=true', 'top' );
    }

    /**
     * Load Pro rewrite query vars
     *
     * @since 2.4
     *
     * @param  array $query_vars
     *
     * @return array
     */
    public function load_custom_query_var( $query_vars ) {
        $query_vars[] = 'about';
        $query_vars[] = 'services';
        $query_vars[] = 'contact';

        return $query_vars;
    }

    /**
     * Returns the terms_and_conditions template
     *
     * @since 2.3
     *
     * @param string $template
     *
     * @return string
     */
    function store_custom_template( $template ) {

        if ( ! function_exists( 'WC' ) ) {
            return $template;
        }

        if ( get_query_var( 'about' ) ) {
            return AF_PLUGIN_PATH . '/templates/store-about.php';
        }
        if ( get_query_var( 'services' ) ) {
            return AF_PLUGIN_PATH . '/templates/store-about.php';
        }
        if ( get_query_var( 'contact' ) ) {
            return AF_PLUGIN_PATH . '/templates/store-about.php';
        }

        return $template;

    }

    /**
     * Get about page
     *
     * @since 1.0
     *
     * @param $store_id
     * @param $store_info
     *
     * @return string
     */
    function dokan_get_store_about_url( $store_id ) {
        $userstore = dokan_get_store_url( $store_id );
        return apply_filters( 'dokan_get_store_about_url', $userstore ."about" );
    }

    /**
     * Get services page
     *
     * @since 1.0
     *
     * @param $store_id
     * @param $store_info
     *
     * @return string
     */
    function dokan_get_store_services_url( $store_id ) {
        $userstore = dokan_get_store_url( $store_id );
        return apply_filters( 'dokan_get_store_services_url', $userstore ."services" );
    }

    /**
     * Get contact page
     *
     * @since 1.0
     *
     * @param $store_id
     * @param $store_info
     *
     * @return string
     */
    function dokan_get_store_contact_url( $store_id ) {
        $userstore = dokan_get_store_url( $store_id );
        return apply_filters( 'dokan_get_store_contact_url', $userstore ."contact" );
    }

    public function dokan_store_additional_tabs( $tabs, $store_id ) {
        $tabs['about'] = array(
            'title' => __( 'About', 'dokan-lite' ),
            'url'   => $this->dokan_get_store_about_url( $store_id )
        );
        $tabs = array_reverse($tabs);
        $tabs['services'] = array(
            'title' => __( 'Services', 'dokan-lite' ),
            'url'   => $this->dokan_get_store_services_url( $store_id )
        );
        $tabs['contact'] = array(
            'title' => __( 'Contact', 'dokan-lite' ),
            'url'   => $this->dokan_get_store_contact_url( $store_id )
        );
        return $tabs;
    }

} // Additional_Functions

$af = Additional_Functions::init();


////////////////////////////////////////////////////////////

add_action( 'plugins_loaded', 'sk_register_service_type' );
function sk_register_service_type () {
    class WC_Product_Service extends WC_Product {
        public function __construct( $product ) {
            $this->product_type = 'service'; // name of your custom product type
            parent::__construct( $product );
            // add additional functions here
        }
    }
}
function pre($a=array(),$b=array(),$c=array()){
    echo '<pre>';
    var_dump($a,$b,$c);
    echo '</pre>';
}
