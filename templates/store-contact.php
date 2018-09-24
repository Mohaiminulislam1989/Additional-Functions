<?php
/**
 * The Template for displaying all reviews.
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */

$vendor = dokan()->vendor->get( get_query_var( 'author' ) );
$vendor_info = $vendor->get_shop_info();

get_header( 'shop' );
?>

<?php do_action( 'woocommerce_before_main_content' ); ?>

<?php if ( dokan_get_option( 'enable_theme_store_sidebar', 'dokan_general', 'off' ) == 'off' ) { ?>
    <div id="dokan-secondary" class="dokan-clearfix dokan-w3 dokan-store-sidebar" role="complementary" style="margin-right:3%;">
        <div class="dokan-widget-area widget-collapse">
            <?php
            if ( ! dynamic_sidebar( 'sidebar-store' ) ) {

                $args = array(
                    'before_widget' => '<aside class="widget">',
                    'after_widget'  => '</aside>',
                    'before_title'  => '<h3 class="widget-title">',
                    'after_title'   => '</h3>',
                );

                if ( class_exists( 'Dokan_Store_Location' ) ) {
                    the_widget( 'Dokan_Store_Category_Menu', array( 'title' => __( 'Store Category', 'dokan-lite' ) ), $args );
                    if( dokan_get_option( 'store_map', 'dokan_general', 'on' ) == 'on' ) {
                        the_widget( 'Dokan_Store_Location', array( 'title' => __( 'Store Location', 'dokan-lite' ) ), $args );
                    }
                    if( dokan_get_option( 'contact_seller', 'dokan_general', 'on' ) == 'on' ) {
                        the_widget( 'Dokan_Store_Contact_Form', array( 'title' => __( 'Contact Vendor', 'dokan-lite' ) ), $args );
                    }
                }

            }
            ?>

            <?php do_action( 'dokan_sidebar_store_after', $vendor->data, $vendor_info ); ?>
        </div>
    </div><!-- #secondary .widget-area -->
<?php
} else {
    get_sidebar( 'store' );
}
?>

<div id="primary" class="content-area dokan-single-store dokan-w8">
    <div id="dokan-content" class="site-content store-review-wrap woocommerce" role="main">

        <?php dokan_get_template_part( 'store-header' ); ?>

        <div id="store-toc-wrapper">
            <div id="store-toc">
                <h2 class="headline"><?php _e( 'Contact', 'dokan-lite' ); ?><?php echo ' ' . $vendor->get_store_name(); ?></h2>
                <div>
                    <?php
                    /**
                     * Dokan Store Contact Form
                     */

                    $name = $email = '';

                    if ( is_user_logged_in() ) {
                        $user  = wp_get_current_user();
                        $name  = $user->display_name;
                        $email = $user->user_email;
                    }
                    ?>

                    <form id="dokan-form-contact-seller" action="" method="post" class="seller-form clearfix">
                        <div class="ajax-response"></div>
                        <div class="dokan-form-group">
                            <input type="text" name="name" value="<?php echo esc_attr( $name ); ?>" placeholder="<?php esc_attr_e( 'Your Name', 'dokan-lite' ); ?>" class="dokan-form-control" minlength="5" required="required">
                        </div>
                        <div class="dokan-form-group">
                            <input type="email" name="email" value="<?php echo esc_attr( $email ); ?>" placeholder="<?php esc_attr_e( 'you@example.com', 'dokan-lite' ); ?>" class="dokan-form-control" required="required">
                        </div>
                        <div class="dokan-form-group">
                            <textarea  name="message" maxlength="1000" cols="25" rows="6" value="" placeholder="<?php esc_attr_e( 'Type your messsage...', 'dokan-lite' ); ?>" class="dokan-form-control" required="required"></textarea>
                        </div>

                        <?php wp_nonce_field( 'dokan_contact_seller' ); ?>
                        <input type="hidden" name="seller_id" value="<?php echo $seller_id; ?>">
                        <input type="hidden" name="action" value="dokan_contact_seller">
                        <input type="submit" name="store_message_send" value="<?php esc_attr_e( 'Send Message', 'dokan-lite' ); ?>" class="dokan-right dokan-btn dokan-btn-theme">
                    </form>
                </div>
            </div><!-- #store-toc -->
        </div><!-- #store-toc-wrap -->

    </div><!-- #content .site-content -->
</div><!-- #primary .content-area -->

<div class="dokan-clearfix"></div>

<?php do_action( 'woocommerce_after_main_content' ); ?>

<?php get_footer(); ?>
