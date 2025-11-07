<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Get links for a specific task or project
 * @param string $relation 'task' or 'project'
 * @param int $relation_id ID of task or project
 * @param boolean $use_demo_fallback Whether to show demo links if no real links exist
 * @return array Array of link objects
 */
function links_for_perfex_get_links($relation = 'task', $relation_id = 0, $use_demo_fallback = false)
{
    $CI = &get_instance();
    $CI->load->model('links_for_perfex/links_model');

    // Get real links from database
    $links = $CI->links_model->get_links($relation, $relation_id);

    // If no links exist and demo fallback is enabled, show demo links for demonstration
    if (empty($links) && $use_demo_fallback) {
        return links_for_perfex_get_demo_links($relation, $relation_id);
    }

    return $links;
}

/**
 * Get demo links for demonstration purposes (fallback)
 * @param string $relation 'task' or 'project'
 * @param int $relation_id ID of task or project
 * @return array Array of demo link objects
 */
function links_for_perfex_get_demo_links($relation = 'task', $relation_id = 0)
{
    // Hardcoded demo links for initial testing and fallback
    $demo_links = [
        (object) [
            'id' => 'demo-1',
            'url' => 'https://github.com/example/project-repo',
            'title' => 'Project Repository',
            'relation' => $relation,
            'relation_id' => $relation_id,
            'description' => 'Main GitHub repository for this project',
            'dateadded' => date('Y-m-d H:i:s'),
            'addedfrom' => get_staff_user_id() ?: 1,
            'is_demo' => true,
        ],
        (object) [
            'id' => 'demo-2',
            'url' => 'https://docs.example.com/project-specs',
            'title' => 'Project Documentation',
            'relation' => $relation,
            'relation_id' => $relation_id,
            'description' => 'Detailed project specifications and requirements',
            'dateadded' => date('Y-m-d H:i:s'),
            'addedfrom' => get_staff_user_id() ?: 1,
            'is_demo' => true,
        ],
        (object) [
            'id' => 'demo-3',
            'url' => 'https://figma.com/design-mockups',
            'title' => 'Design Mockups',
            'relation' => $relation,
            'relation_id' => $relation_id,
            'description' => 'UI/UX design mockups and wireframes',
            'dateadded' => date('Y-m-d H:i:s'),
            'addedfrom' => get_staff_user_id() ?: 1,
            'is_demo' => true,
        ],
        (object) [
            'id' => 'demo-4',
            'url' => 'https://drive.google.com/folder/shared-resources',
            'title' => '',  // Demo link without title
            'relation' => $relation,
            'relation_id' => $relation_id,
            'description' => 'Shared Google Drive folder with project assets',
            'dateadded' => date('Y-m-d H:i:s'),
            'addedfrom' => get_staff_user_id() ?: 1,
            'is_demo' => true,
        ]
    ];

    return $demo_links;
}

/**
 * Format a link for display
 * @param object $link Link object
 * @return string Formatted HTML for link display
 */
function links_for_perfex_format_link($link)
{
    $CI = &get_instance();

    $title = !empty($link->title) ? htmlspecialchars($link->title) : htmlspecialchars($link->url);
    $url = htmlspecialchars($link->url);
    $description = !empty($link->description) ? htmlspecialchars($link->description) : '';

    $html = '<div class="link-item" data-link-id="' . $link->id . '">';
    $html .= '<div class="link-header">';
    $html .= '<i class="fa fa-external-link text-muted"></i> ';
    $html .= '<a href="' . $url . '" target="_blank" rel="noopener noreferrer" class="link-url">' . $title . '</a>';
    $html .= '<div class="pull-right">';
    $html .= '<small class="text-muted">' . _dt($link->dateadded) . '</small>';
    $html .= '</div>';
    $html .= '</div>';

    if (!empty($description)) {
        $html .= '<div class="link-description text-muted"><small>' . $description . '</small></div>';
    }

    if (!empty($link->title) && $link->title !== $link->url) {
        $html .= '<div class="link-original-url"><small class="text-muted">' . $url . '</small></div>';
    }

    $html .= '</div>';

    return $html;
}

/**
 * Get link icon based on URL
 * @param string $url The URL to analyze
 * @return string Font Awesome icon class
 */
function links_for_perfex_get_link_icon($url)
{
    $url = strtolower($url);

    if (strpos($url, 'github.com') !== false) {
        return 'fa-github';
    } elseif (strpos($url, 'docs.google.com') !== false || strpos($url, 'drive.google.com') !== false) {
        return 'fa-google';
    } elseif (strpos($url, 'figma.com') !== false) {
        return 'fa-pencil-square-o';
    } elseif (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
        return 'fa-youtube-play';
    } elseif (strpos($url, 'dropbox.com') !== false) {
        return 'fa-dropbox';
    } else {
        return 'fa-external-link';
    }
}