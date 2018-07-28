<div class="dokan-dashboard-wrap">

<?php
    $current_page = get_query_var( 'service' );
    /**
     *  dokan_dashboard_content_before hook
     *  dokan_dashboard_support_content_before
     *
     *  @hooked get_dashboard_side_navigation
     *
     *  @since 2.4
     */
    do_action( 'dokan_dashboard_content_before' );
    do_action( 'dokan_dashboard_support_content_before' );
    ?>

    <div class="dokan-dashboard-content dokan-booking-wrapper dokan-product-edit">
        <?php

        $service_url = dokan_get_navigation_url( 'service' );
        $title       = apply_filters( 'dokan_service_menu_title', $current_page );

        $template_args = array(
            'is_booking'  => true,
            'title'       => $title,
            'service_url' => $service_url
        );

        switch ( $current_page ) {
            case 'new-product':
                include ( AF_PLUGIN_PATH . '/templates/service/new-product.php' );
                break;

            case 'edit':
                include ( AF_PLUGIN_PATH . '/templates/service/new-product.php' );
                break;

            case '':
                include ( AF_PLUGIN_PATH . '/templates/service/product-list.php' );
                break;

            default:
                do_action( 'dokan_service_load_menu_template', $current_page, $template_args );
                break;
        }
        ?>
    </div><!-- .dokan-dashboard-content -->

    <?php

    /**
     *  dokan_dashboard_content_after hook
     *  dokan_dashboard_support_content_after hook
     *
     *  @since 2.4
     */
    do_action( 'dokan_dashboard_content_after' );
    do_action( 'dokan_dashboard_support_content_after' );
    ?>

</div><!-- .dokan-dashboard-wrap -->
