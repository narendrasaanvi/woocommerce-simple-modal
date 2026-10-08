<?php

/**
 * Plugin Name: WooCommerce Simple Modal Popup
 * Description: Simple modal popup for WooCommerce websites with Elementor Free shortcode support.
 * Version: 1.0.0
 * Author: Custom
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WSMP_VERSION', '1.0.0');
define('WSMP_PATH', plugin_dir_path(__FILE__));
define('WSMP_URL', plugin_dir_url(__FILE__));

require_once WSMP_PATH . 'includes/admin.php';
require_once WSMP_PATH . 'includes/frontend.php';

register_activation_hook(__FILE__, 'wsmp_activate');

function wsmp_activate()
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'wsmp_popup';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table_name} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL DEFAULT '',
        image_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
        content LONGTEXT NULL,
        button_text VARCHAR(255) NOT NULL DEFAULT '',
        button_url TEXT NULL,
        open_new_tab TINYINT(1) NOT NULL DEFAULT 0,
        status TINYINT(1) NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        PRIMARY KEY (id)
    ) {$charset_collate};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta($sql);

    $count = $wpdb->get_var(
        "SELECT COUNT(*) FROM {$table_name}"
    );

    if ((int) $count === 0) {
        $wpdb->insert(
            $table_name,
            array(
                'title' => '',
                'image_id' => 0,
                'content' => '',
                'button_text' => '',
                'button_url' => '',
                'open_new_tab' => 0,
                'status' => 0,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ),
            array(
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
                '%s',
                '%s',
            )
        );
    }
}