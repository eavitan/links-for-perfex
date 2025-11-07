<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Links for Perfex Module Installation
 *
 * This file is executed when the module is activated.
 * It creates the necessary database tables and sets up default options.
 */

// Load the install helper
require_once(__DIR__ . '/helpers/links_for_perfex_install_helper.php');

// Run the installation sequence
init_links_for_perfex_install_sequence();

// Create the database table
$CI = &get_instance();

// Create the links table if it doesn't exist
if (!$CI->db->table_exists(db_prefix() . 'links_for_perfex')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'links_for_perfex` (
        `id` int NOT NULL AUTO_INCREMENT,
        `url` varchar(500) NOT NULL,
        `title` varchar(255) DEFAULT NULL,
        `description` text DEFAULT NULL,
        `relation` enum("task","project") NOT NULL,
        `relation_id` int NOT NULL,
        `dateadded` datetime NOT NULL,
        `addedfrom` int NOT NULL,
        `last_modified` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        INDEX `idx_relation` (`relation`, `relation_id`),
        INDEX `idx_addedfrom` (`addedfrom`),
        INDEX `idx_dateadded` (`dateadded`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;');

    log_activity('Links for Perfex: Database table created successfully');
}

log_activity('Links for Perfex module installation completed');