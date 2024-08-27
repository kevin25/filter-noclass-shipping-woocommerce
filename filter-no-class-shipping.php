<?php
/*
Plugin Name: WooCommerce Product Filter
Description: Filters simple and variable products without shipping class
Version: 1.0
Author: Kevin Ng.
*/

add_filter('woocommerce_product_query', 'filter_products_without_class_shipping');https://github.com/kevin25/filter-noclass-shipping-woocommerce/tree/main

function filter_products_without_class_shipping($q) {
   add_filter('parse_query', 'filter_admin_products_without_class_shipping');

function filter_admin_products_without_class_shipping($query) {
    global $pagenow, $typenow;

    // Check if we're on the admin products list page
    if (is_admin() && $pagenow == 'edit.php' && $typenow == 'product') {
        // Check if our custom filter is not set (to allow toggling the filter off)
        if (!isset($_GET['show_all_shipping_classes'])) {
            $query->query_vars['tax_query'][] = array(
                'taxonomy' => 'product_shipping_class',
                'field' => 'id',
                'terms' => get_terms('product_shipping_class', array('fields' => 'ids')),
                'operator' => 'NOT IN'
            );

            // Filter only simple and variable products
            $query->query_vars['tax_query'][] = array(
                'taxonomy' => 'product_type',
                'field' => 'slug',
                'terms' => array('simple', 'variable'),
                'operator' => 'IN'
            );
        }
    }
    return $query;
}

// Add a button to toggle the filter
add_action('restrict_manage_posts', 'add_filter_button');

function add_filter_button() {
    global $typenow;

    if ($typenow == 'product') {
        $filter_active = !isset($_GET['show_all_shipping_classes']);
        $url = add_query_arg($filter_active ? 'show_all_shipping_classes' : 'filter_no_shipping_class', '1');
        $text = $filter_active ? 'Show All Shipping Classes' : 'Filter No Shipping Class';
        echo '<a href="' . esc_url($url) . '" class="button">' . esc_html($text) . '</a>';
    }
}
}
