<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Initialize the links for perfex installation sequence
 */
function init_links_for_perfex_install_sequence()
{
    $CI = &get_instance();

    log_activity('Links for Perfex module activated');

    // Set default module options if they don't exist
    if (get_option('links_for_perfex_enabled') === false) {
        add_option('links_for_perfex_enabled', '1');
    }

    if (get_option('links_for_perfex_version') === false) {
        add_option('links_for_perfex_version', '2.0.0');
    }
}