<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Links for Perfex Module Uninstall
 *
 * This file is executed when the module is uninstalled.
 * Database cleanup is only performed in development environments.
 */

// Load the uninstall helper
require_once(__DIR__ . '/helpers/links_for_perfex_uninstall_helper.php');

// Run the uninstall sequence
init_links_for_perfex_uninstall_sequence();