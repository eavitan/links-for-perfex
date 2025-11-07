<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Initialize the links for perfex uninstall sequence
 */
function init_links_for_perfex_uninstall_sequence()
{
    $CI = &get_instance();

    // Get the current environment (defined in index.php or config)
    $current_environment = defined('ENVIRONMENT') ? ENVIRONMENT : 'production';

    // Only perform database cleanup in development environment
    if ($current_environment === 'development') {

        log_activity('Links for Perfex: Starting uninstall process in development environment');

        // Drop the links table if it exists
        if ($CI->db->table_exists(db_prefix() . 'links_for_perfex')) {
            $CI->db->query('DROP TABLE `' . db_prefix() . 'links_for_perfex`');
            log_activity('Links for Perfex: Database table dropped successfully');
        }

        // Remove module options
        delete_option('links_for_perfex_enabled');
        delete_option('links_for_perfex_version');

        log_activity('Links for Perfex: Module options removed');
        log_activity('Links for Perfex: Uninstall completed successfully in development environment');

    } else {

        // In production or other environments, only log the uninstall but don't delete data
        log_activity('Links for Perfex: Module uninstalled (data preserved in ' . $current_environment . ' environment)');

        // Still remove the enabled flag so the module appears as deactivated
        delete_option('links_for_perfex_enabled');

    }
}