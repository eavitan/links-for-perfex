<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Links for Perfex
Description: Add link management functionality to tasks and projects in Perfex CRM
Version: 2.0.0
Author: Custom Development
*/

define('LINKS_FOR_PERFEX_MODULE_NAME', 'links_for_perfex');

hooks()->add_action('admin_init', 'links_for_perfex_init_menu_items');
hooks()->add_action('admin_init', 'links_for_perfex_init_project_tabs');
hooks()->add_filter('get_task', 'links_for_perfex_add_links_to_task');
hooks()->add_action('before_task_description_section', 'links_for_perfex_render_task_links');

/**
 * Load the module helper
 */
$CI = &get_instance();
$CI->load->helper(LINKS_FOR_PERFEX_MODULE_NAME . '/links_for_perfex');

/**
 * Register activation module hook
 */
register_activation_hook(LINKS_FOR_PERFEX_MODULE_NAME, 'links_for_perfex_activation_hook');

function links_for_perfex_activation_hook()
{
    require_once(__DIR__ . '/install.php');
}

/**
 * Register uninstall module hook
 */
register_uninstall_hook(LINKS_FOR_PERFEX_MODULE_NAME, 'links_for_perfex_uninstall_hook');

function links_for_perfex_uninstall_hook()
{
    require_once(__DIR__ . '/uninstall.php');
}

/**
 * Register language files
 */
register_language_files(LINKS_FOR_PERFEX_MODULE_NAME, [LINKS_FOR_PERFEX_MODULE_NAME]);

/**
 * Init module menu items in admin
 */
function links_for_perfex_init_menu_items()
{
    $CI = &get_instance();

    if (is_admin() || has_permission('tasks', '', 'view')) {
        // Add menu item under Setup for module configuration
        $CI->app_menu->add_setup_menu_item('links-for-perfex', [
            'name'     => _l('links_for_perfex'),
            'href'     => admin_url('links_for_perfex/settings'),
            'position' => 35,
        ]);

        // Add demo menu items for testing
        $CI->app_menu->add_setup_children_item('links-for-perfex', [
            'slug'     => 'links-for-perfex-task-demo',
            'name'     => 'Task Demo',
            'href'     => admin_url('links_for_perfex/task_demo'),
            'position' => 1,
        ]);

        $CI->app_menu->add_setup_children_item('links-for-perfex', [
            'slug'     => 'links-for-perfex-project-demo',
            'name'     => 'Project Demo',
            'href'     => admin_url('links_for_perfex/project_demo'),
            'position' => 2,
        ]);
    }
}

/**
 * Initialize project tabs for the module
 */
function links_for_perfex_init_project_tabs()
{
    $CI = &get_instance();

    // Add the Links tab to projects
    $CI->app_tabs->add_project_tab('project_links', [
        'name'     => _l('links'),
        'icon'     => 'fa fa-link',
        'view'     => 'links_for_perfex/admin/projects/project_links',
        'position' => 35,
    ]);
}

/**
 * Add links data to task object
 */
function links_for_perfex_add_links_to_task($task)
{
    if ($task) {
        $task->links = links_for_perfex_get_links('task', $task->id, false);
    }
    return $task;
}

/**
 * Render task links section in task view
 */
function links_for_perfex_render_task_links($task)
{
    $CI = &get_instance();

    // Make sure the task object is available to the view
    $CI->load->vars(['task' => $task]);

    // Load and display the task links section
    $CI->load->view(LINKS_FOR_PERFEX_MODULE_NAME . '/admin/tasks/_links_section');
}

/**
 * Render project links section in project view
 */
function links_for_perfex_render_project_links($project)
{
    $CI = &get_instance();

    // Make sure the project object is available to the view
    $CI->load->vars(['project' => $project]);

    // Load and display the project links section
    $CI->load->view(LINKS_FOR_PERFEX_MODULE_NAME . '/admin/projects/_links_section');
}