<?php
global $post;

$post_id      = NULL;
$post_title   = '';
$post_content = '';
$post_excerpt = '';
$post_status  = 'pending';

if ( isset( $_GET['product_id'] ) ) {
    $post_id        = intval( $_GET['product_id'] );
    $post           = get_post( $post_id );
    $post_title     = $post->post_title;
    $post_content   = $post->post_content;
    $post_excerpt   = $post->post_excerpt;
    $post_status    = $post->post_status;
    $product        = wc_get_product( $post_id );

    $_visibility    = ( version_compare( WC_VERSION, '2.7', '>' ) ) ? $product->get_catalog_visibility() : get_post_meta( $post_id, '_visibility', true );
    $visibility_options = dokan_get_product_visibility_options();

    $_enable_reviews = $post->comment_status;
}

$_regular_price         = get_post_meta( $post_id, '_regular_price', true );
$_sale_price            = get_post_meta( $post_id, '_sale_price', true );
$is_discount            = !empty( $_sale_price ) ? true : false;
$_sale_price_dates_from = get_post_meta( $post_id, '_sale_price_dates_from', true );
$_sale_price_dates_to   = get_post_meta( $post_id, '_sale_price_dates_to', true );

$_sale_price_dates_from = !empty( $_sale_price_dates_from ) ? date_i18n( 'Y-m-d', $_sale_price_dates_from ) : '';
$_sale_price_dates_to   = !empty( $_sale_price_dates_to ) ? date_i18n( 'Y-m-d', $_sale_price_dates_to ) : '';
$show_schedule          = false;

if ( !empty( $_sale_price_dates_from ) && !empty( $_sale_price_dates_to ) ) {
$show_schedule          = true;
}

$_virtual     = get_post_meta( $post_id, '_virtual', true );
$is_virtual   = ( 'yes' == $_virtual ) ? true : false;
?>

<header class="dokan-dashboard-header dokan-clearfix">
    <h1 class="entry-title">
        <?php if ( !$post_id ): ?>
            <?php _e( 'Add Service product', 'dokan' ); ?>
        <?php else: ?>
            <?php _e( $title , 'dokan' ); ?>
            <span class="dokan-label <?php echo dokan_get_post_status_label_class( $post->post_status ); ?> dokan-product-status-label">
                <?php echo dokan_get_post_status( $post->post_status ); ?>
            </span>

            <?php if ( $post->post_status == 'publish' ) { ?>
                <span class="dokan-right">
                    <a class="view-product dokan-btn dokan-btn-sm" href="<?php echo get_permalink( $post->ID ); ?>" target="_blank"><?php _e( 'View Product', 'dokan' ); ?></a>
                </span>
            <?php } ?>

            <?php if ( $_visibility == 'hidden' ) { ?>
                <span class="dokan-right dokan-label dokan-label-default dokan-product-hidden-label"><i class="fa fa-eye-slash"></i> <?php _e( 'Hidden', 'dokan' ); ?></span>
            <?php } ?>

        <?php endif ?>
    </h1>
</header><!-- .entry-header -->

<div class="product-edit-new-container product-edit-container">
    <?php if ( Dokan_Template_Products::$errors ) { ?>
        <div class="dokan-alert dokan-alert-danger">
            <a class="dokan-close" data-dismiss="alert">&times;</a>

            <?php foreach ( Dokan_Template_Products::$errors as $error ) { ?>

                <strong><?php _e( 'Error!', 'dokan' ); ?></strong> <?php echo $error ?>.<br>

            <?php } ?>
        </div>
    <?php } ?>

    <?php if ( isset( $_GET['message'] ) && $_GET['message'] == 'success' ) { ?>
        <div class="dokan-message">
            <button type="button" class="dokan-close" data-dismiss="alert">&times;</button>
            <strong><?php _e( 'Success!', 'dokan' ); ?></strong> <?php _e( 'The product has been saved successfully.', 'dokan' ); ?>

            <?php if ( $post->post_status == 'publish' ) { ?>
                <a href="<?php echo get_permalink( $post_id ); ?>" target="_blank"><?php _e( 'View Product &rarr;', 'dokan' ); ?></a>
            <?php } ?>
        </div>
    <?php } ?>

    <?php
    $can_sell = apply_filters( 'dokan_can_post', true );

    if ( $can_sell ) {

        if ( dokan_is_seller_enabled( get_current_user_id() ) ) {
            ?>

            <form class="dokan-product-edit-form" role="form" method="post">

                <?php
                if ( $post_id ):
                    do_action( 'dokan_product_data_panel_tabs' );
                endif;

                do_action( 'dokan_product_edit_before_main' );
                ?>

                <div class="dokan-form-top-area">

                    <div class="content-half-part">

                        <div class="dokan-form-group">
                            <input type="hidden" name="dokan_product_id" value="<?php echo $post_id; ?>">

                            <label for="post_title" class="form-label"><?php _e( 'Title', 'dokan' ); ?></label>
                            <div class="dokan-product-title-alert dokan-hide dokan-alert dokan-alert-danger">
                                <?php _e( 'Please choose a Name !!!', 'dokan' ); ?>
                            </div>
                            <?php dokan_post_input_box( $post_id, 'post_title', array( 'placeholder' => __( 'Product name..', 'dokan' ), 'value' => $post_title ) ); ?>
                        </div>

                        <div class="dokan-clearfix">

                                    <div class="dokan-form-group dokan-clearfix dokan-price-container">

                                        <div class="content-half-part regular-price">
                                            <label for="_regular_price" class="form-label"><?php _e( 'Price', 'dokan-lite' ); ?>
                                                <span class="vendor-earning" data-commission="<?php echo dokan_get_seller_percentage( dokan_get_current_user_id(), $post_id ); ?>" data-commission_type="<?php echo dokan_get_commission_type( dokan_get_current_user_id(), $post_id ); ?>">( <?php _e( ' You Earn : ', 'dokan-lite' ) ?><?php echo get_woocommerce_currency_symbol() ?><span class="vendor-price">0.00</span> )</span>
                                            </label>
                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                                <?php dokan_post_input_box( $post_id, '_regular_price', array( 'class' => 'dokan-product-regular-price', 'placeholder' => __( '0.00', 'dokan-lite' ) ), 'number' ); ?>
                                            </div>
                                        </div>

                                        <div class="content-half-part sale-price">
                                            <label for="_sale_price" class="form-label">
                                                <?php _e( 'Discounted Price', 'dokan-lite' ); ?>
                                                <a href="#" class="sale_schedule <?php echo ($show_schedule ) ? 'dokan-hide' : ''; ?>"><?php _e( 'Schedule', 'dokan-lite' ); ?></a>
                                                <a href="#" class="cancel_sale_schedule <?php echo ( ! $show_schedule ) ? 'dokan-hide' : ''; ?>"><?php _e( 'Cancel', 'dokan-lite' ); ?></a>
                                            </label>

                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php echo get_woocommerce_currency_symbol(); ?></span>
                                                <?php dokan_post_input_box( $post_id, '_sale_price', array( 'class' => 'dokan-product-sales-price','placeholder' => __( '0.00', 'dokan-lite' ) ), 'number' ); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="dokan-form-group dokan-clearfix dokan-price-container">
                                        <div class="dokan-product-less-price-alert dokan-hide">
                                            <?php _e('Product price can\'t be less than the vendor fee!', 'dokan-lite' ); ?>
                                        </div>
                                    </div>

                                    <div class="sale_price_dates_fields dokan-clearfix dokan-form-group <?php echo ( ! $show_schedule ) ? 'dokan-hide' : ''; ?>">
                                        <div class="content-half-part from">
                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php _e( 'From', 'dokan-lite' ); ?></span>
                                                <input type="text" name="_sale_price_dates_from" class="dokan-form-control datepicker" value="<?php echo esc_attr( $_sale_price_dates_from ); ?>" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" placeholder="<?php _e( 'YYYY-MM-DD', 'dokan-lite' ); ?>">
                                            </div>
                                        </div>

                                        <div class="content-half-part to">
                                            <div class="dokan-input-group">
                                                <span class="dokan-input-group-addon"><?php _e( 'To', 'dokan-lite' ); ?></span>
                                                <input type="text" name="_sale_price_dates_to" class="dokan-form-control datepicker" value="<?php echo esc_attr( $_sale_price_dates_to ); ?>" maxlength="10" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" placeholder="<?php _e( 'YYYY-MM-DD', 'dokan-lite' ); ?>">
                                            </div>
                                        </div>
                                    </div><!-- .sale-schedule-container -->
                                </div>

                        <div class="dokan-form-group virtual-checkbox">
                            <label>
                                <input type="checkbox" <?php checked( $is_virtual, true ); ?> class="_is_virtual" name="_virtual" id="_virtual"> <?php _e( 'Virtual', 'dokan' ); ?> <i class="fa fa-question-circle tips" aria-hidden="true" data-title="<?php _e( 'Virtual products are intangible and aren\'t shipped.', 'dokan' ); ?>"></i>
                            </label>
                        </div>

                        <div class="dokan-form-group">
                            <label for="product_cat" class="form-label"><?php _e( 'Category', 'dokan' ); ?></label>
                            <?php
                            $term = array();
                            $term = wp_get_post_terms( $post_id, 'product_cat', array( 'fields' => 'ids') );

                            $multiple = dokan_get_option( 'product_category_style', 'dokan_selling', 'single' ) == 'single' ? '' : 'multiple';
                            $select_name = "multiple" == $multiple ? 'product_cat[]' : 'product_cat';

                            include_once DOKAN_LIB_DIR.'/class.taxonomy-walker.php';
                            $drop_down_category = wp_dropdown_categories( array(
                                'show_option_none' => __( '', 'dokan' ),
                                'hierarchical'     => 1,
                                'hide_empty'       => 0,
                                'name'             => $select_name,
                                'id'               => 'product_cat',
                                'taxonomy'         => 'product_cat',
                                'title_li'         => '',
                                'class'            => 'product_cat dokan-form-control dokan-select2',
                                'exclude'          => '',
                                'selected'         => $term,
                                'echo'             => 0,
                                'walker'           => new DokanTaxonomyWalker( $post_id )
                            ) );

                            $replace_attrb = "<select data-placeholder='".__( 'Select product category','dokan' )."' ". $multiple ;

                            echo str_replace( '<select', $replace_attrb, $drop_down_category );
                            ?>
                        </div>

                        <div class="dokan-form-group">
                            <label for="product_tag" class="form-label"><?php _e( 'Tags', 'dokan' ); ?></label>
                            <?php
                            require_once DOKAN_LIB_DIR . '/class.taxonomy-walker.php';
                            $term           = wp_get_post_terms( $post_id, 'product_tag', array( 'fields' => 'ids' ) );
                            $selected       = ( $term ) ? $term : array();
                            $drop_down_tags = wp_dropdown_categories( array(
                                'show_option_none' => __( '', 'dokan' ),
                                'hierarchical'     => 1,
                                'hide_empty'       => 0,
                                'name'             => 'product_tag[]',
                                'id'               => 'product_tag',
                                'taxonomy'         => 'product_tag',
                                'title_li'         => '',
                                'class'            => ' dokan-select2 product_tags dokan-form-control chosen',
                                'exclude'          => '',
                                'selected'         => $selected,
                                'echo'             => 0,
                                'walker'           => new DokanTaxonomyWalker( $post_id )
                            ) );

                            echo str_replace( '<select', '<select data-placeholder="' . __( 'Select product tags', 'dokan' ) . '" multiple="multiple" ', $drop_down_tags );
                            ?>
                        </div>

                    </div><!-- .content-half-part -->

                    <div class="content-half-part featured-image">

                        <div class="dokan-new-product-featured-img dokan-feat-image-upload">
                            <?php
                            $wrap_class        = ' dokan-hide';
                            $instruction_class = '';
                            $feat_image_id     = 0;

                            if ( has_post_thumbnail( $post_id ) ) {
                                $wrap_class        = '';
                                $instruction_class = ' dokan-hide';
                                $feat_image_id     = get_post_thumbnail_id( $post_id );
                            }
                            ?>

                            <div class="instruction-inside<?php echo $instruction_class; ?>">
                                <input type="hidden" name="feat_image_id" class="dokan-feat-image-id" value="<?php echo $feat_image_id; ?>">

                                <i class="fa fa-cloud-upload"></i>
                                <a href="#" class="dokan-feat-image-btn btn btn-sm"><?php _e( 'Upload a product cover image', 'dokan' ); ?></a>
                            </div>

                            <div class="image-wrap<?php echo $wrap_class; ?>">
                                <a class="close dokan-remove-feat-image">&times;</a>
                                <?php if ( $feat_image_id ) { ?>
                                    <?php echo get_the_post_thumbnail( $post_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array( 'height' => '', 'width' => '' ) ); ?>
                                <?php } else { ?>
                                    <img height="" width="" src="" alt="">
                                <?php } ?>
                            </div>
                        </div><!-- .dokan-feat-image-upload -->

                        <div class="dokan-product-gallery">
                            <div class="dokan-side-body" id="dokan-product-images">
                                <div id="product_images_container">
                                    <ul class="product_images dokan-clearfix">
                                        <?php
                                        $product_images = get_post_meta( $post_id, '_product_image_gallery', true );
                                        $gallery        = explode( ',', $product_images );

                                        if ( $gallery ) {
                                            foreach ( $gallery as $image_id ) {
                                                if ( empty( $image_id ) ) {
                                                    continue;
                                                }

                                                $attachment_image = wp_get_attachment_image_src( $image_id, 'thumbnail' );
                                                ?>
                                                <li class="image" data-attachment_id="<?php echo $image_id; ?>">
                                                    <img src="<?php echo $attachment_image[0]; ?>" alt="">
                                                    <a href="#" class="action-delete" title="<?php esc_attr_e( 'Delete image', 'dokan' ); ?>">&times;</a>
                                                </li>
                                                <?php
                                            }
                                        }
                                        ?>
                                        <li class="add-image add-product-images tips" data-title="<?php _e( 'Add gallery image', 'dokan' ); ?>">
                                            <a href="#" class="add-product-images"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                        </li>
                                    </ul>
                                    <input type="hidden" id="product_image_gallery" name="product_image_gallery" value="<?php echo esc_attr( $product_images ); ?>">
                                </div>
                            </div>
                        </div> <!-- .product-gallery -->
                    </div><!-- .content-half-part -->
                </div><!-- .dokan-form-top-area -->

                <div class="dokan-product-short-description">
                    <label for="post_excerpt" class="form-label"><?php _e( 'Short Description', 'dokan' ); ?></label>
                    <?php wp_editor( $post_excerpt, 'post_excerpt', array( 'editor_height' => 50, 'quicktags' => false, 'media_buttons' => false, 'teeny' => true, 'editor_class' => 'post_excerpt' ) ); ?>
                </div>

                <div class="dokan-product-description">
                    <label for="post_content" class="form-label"><?php _e( 'Description', 'dokan' ); ?></label>
                    <?php wp_editor( $post_content, 'post_content', array( 'editor_height' => 50, 'quicktags' => false, 'media_buttons' => false, 'teeny' => true, 'editor_class' => 'post_content' ) ); ?>
                </div>

                <?php do_action( 'dokan_new_product_form' ); ?>


                <?php if ( !empty( $post_id ) ): ?>

                <div class="dokan-other-options dokan-edit-row dokan-clearfix">
                    <div class="dokan-section-heading" data-togglehandler="dokan_other_options">
                        <h2><i class="fa fa-cog" aria-hidden="true"></i> <?php _e( 'Other Options', 'dokan' ); ?></h2>
                        <p><?php _e( 'Set your extra product options', 'dokan' ); ?></p>
                        <a href="#" class="dokan-section-toggle">
                            <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true"></i>
                        </a>
                        <div class="dokan-clearfix"></div>
                    </div>

                    <div class="dokan-section-content">
                        <div class="dokan-form-group content-half-part">
                            <label for="post_status" class="form-label"><?php _e( 'Product Status', 'dokan' ); ?></label>
                            <?php if ( $post_status != 'pending' ) { ?>
                                <?php
                                $post_statuses = apply_filters( 'dokan_post_status', array(
                                    'publish' => __( 'Online', 'dokan' ),
                                    'draft'   => __( 'Draft', 'dokan' )
                                ), $post );
                                ?>

                                <select id="post_status" class="dokan-form-control" name="post_status">
                                    <?php foreach ( $post_statuses as $status => $label ) { ?>
                                        <option value="<?php echo $status; ?>"<?php selected( $post_status, $status ); ?>><?php echo $label; ?></option>
                                <?php } ?>
                                </select>
                            <?php } else { ?>
                                <?php $pending_class = $post_status == 'pending' ? '  dokan-label dokan-label-warning' : ''; ?>
                                <span class="dokan-toggle-selected-display<?php echo $pending_class; ?>"><?php echo dokan_get_post_status( $post_status ); ?></span>
                            <?php } ?>
                        </div>

                        <div class="dokan-form-group content-half-part">
                            <label for="_visibility" class="form-label"><?php _e( 'Visibility', 'dokan' ); ?></label>
                            <select name="_visibility" id="_visibility" class="dokan-form-control">
                                <?php foreach ( $visibility_options as $name => $label ): ?>
                                    <option value="<?php echo $name; ?>" <?php selected( $_visibility, $name ); ?>><?php echo $label; ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="dokan-clearfix"></div>

                        <div class="dokan-form-group">
                            <label for="_purchase_note" class="form-label"><?php _e( 'Purchase Note', 'dokan' ); ?></label>
                                <?php dokan_post_input_box( $post_id, '_purchase_note', array( 'placeholder' => __( 'Customer will get this info in their order email', 'dokan' ) ), 'textarea' ); ?>
                        </div>

                        <div class="dokan-form-group">
                            <?php $_enable_reviews = ( $post->comment_status == 'open' ) ? 'yes' : 'no'; ?>
                            <?php dokan_post_input_box( $post_id, '_enable_reviews', array( 'value' => $_enable_reviews, 'label' => __( 'Enable product reviews', 'dokan' ) ), 'checkbox' ); ?>
                        </div>

                    </div>
                </div><!-- .dokan-other-options -->
                <?php

                    do_action( 'dokan_product_edit_after_options' );

                    wp_nonce_field( 'dokan_edit_product', 'dokan_edit_product_nonce' );
                ?>

                    <input type="hidden" name="dokan_update_product" value="<?php esc_attr_e( 'Save Product', 'dokan' ); ?>"/>
                    <input type="submit" name="dokan_update_product" class="dokan-btn dokan-btn-theme dokan-btn-lg btn-block" value="<?php esc_attr_e( 'Save Product', 'dokan' ); ?>"/>

                <?php else: ?>
                    <div class="dokan-form-group dokan-clearfix"></div>
                    <?php wp_nonce_field( 'dokan_add_new_product', 'dokan_add_new_product_nonce' ); ?>
                    <input type="hidden" name="add_product" value="<?php esc_attr_e( 'Save Product', 'dokan' ); ?>"/>
                    <input type="submit" name="add_product" class="dokan-btn dokan-btn-theme dokan-btn-lg btn-block" value="<?php esc_attr_e( 'Save Product', 'dokan' ); ?>"/>

                <?php endif; ?>

                <!--hidden input for Firefox issue-->
                <input type="hidden" name="_stock_status" value="instock"/>
                <input type="hidden" name="_sku" value=""/>
                <input type="hidden" name="product_shipping_class" value="-1"/>
                <input type="hidden" name="price" value=""/>
                <input type="hidden" name="product_type" value="service"/>
            </form>

                <?php } else { ?>
                    <div class="dokan-alert dokan-alert">
                        <?php dokan_seller_not_enabled_notice() ?>
                    </div>
                <?php } ?>

    <?php } else { ?>

        <?php do_action( 'dokan_can_post_notice' ); ?>

        <?php
    }

    wp_reset_postdata();
    ?>
</div> <!-- #primary .content-area -->
