<?php
/**
* Plugin Name: Events Manager - WooCommerce Integration
* Description: Integrates WooCommerce with Events Manager
* Version: 1.0.0
* Author: David Jensen
* Author URI: http://dkjensen.com
* Text Domain: events-manager-woocommerce
**/


if( ! defined( 'EVENTS_MANAGER_WOOCOMMERCE_PLUGIN_DIR' ) ) {
	define( 'EVENTS_MANAGER_WOOCOMMERCE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if( ! defined( 'EVENTS_MANAGER_WOOCOMMERCE_PLUGIN_URL' ) ) {
	define( 'EVENTS_MANAGER_WOOCOMMERCE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if( ! defined( 'EVENTS_MANAGER_WOOCOMMERCE_VER' ) ) {
	define( 'EVENTS_MANAGER_WOOCOMMERCE_VER', '1.0.0' );
}


require_once 'vendor/autoload.php';


/**
 * New metabox
 *
 * @return void
 */
function events_manager_woocommerce_metaboxes() {
	$prefix = '_events_manager_';

	$cmb = new_cmb2_box( array(
		'id'            => 'event_woocommerce_metabox',
		'title'         => __( 'Event WooCommerce Integration', 'cmb2' ),
		'object_types'  => array( 'event' ),
		'context'       => 'normal',
		'priority'      => 'high',
		'show_names'    => true,
    ) );
    
    $cmb->add_field( array(
        'name'              => __( 'Associated Product ID' ),
        'id'                => 'event_product_id',
        'type'              => 'post_search_text',
        'post_type'         => 'product',
        'select_type'       => 'radio',
        'select_behavior'   => 'replace',
    ) );

}
add_action( 'cmb2_admin_init', 'events_manager_woocommerce_metaboxes' );


/**
 * Function to retreive the product ID from an event
 *
 * @param [type] $event
 * @return void
 */
function events_manager_get_event_product_id( $event ) {
    $event = get_post( $event );

    if( $event ) {
        return get_post_meta( $event->ID, 'event_product_id', true );
    }

    return false;
}


/**
 * Add new replaceable placeholder #_PRODUCTLINK
 *
 * @param [type] $replace
 * @param [type] $event
 * @param [type] $full_result
 * @param [type] $target
 * @return void
 */
function events_manager_woocommerce_placeholders( $replace, $event, $full_result, $target ) {
    if( $replace == '#_PRODUCTLINK' ) {
        $product_id = events_manager_get_event_product_id( $event->post_id );

        return ! empty( $product_id ) ? esc_url( get_permalink( $product_id ) ) : esc_url( $event->get_permalink() );
    }

    return $replace;
}
add_filter( 'em_event_output_placeholder', 'events_manager_woocommerce_placeholders', 10, 4 );