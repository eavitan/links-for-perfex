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
 * A page's title and meta description, for a link saved without a title (2.0.4).
 * Only public addresses: each hop's host must resolve to a public IP, and the request is pinned to
 * that IP, so a link cannot make the CRM read its own network. 6 s and 512 KB in all, 2xx only.
 * @param string $url
 * @return array ['title' => string, 'description' => string], '' for what was not found
 */
function links_for_perfex_fetch_page_meta($url)
{
    $none     = ['title' => '', 'description' => ''];
    $start    = $url;
    $deadline = microtime(true) + 6;

    for ($hop = 0; ; $hop++) {
        $parts  = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host   = $parts['host'] ?? '';
        $left   = (int) (($deadline - microtime(true)) * 1000);
        if ($hop > 5 || $left < 200 || !in_array($scheme, ['http', 'https'], true) || $host === '') {
            return $none;
        }
        $ip = filter_var($host, FILTER_VALIDATE_IP) ? $host : gethostbyname($host);
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return $none;
        }
        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);

        $html = '';
        $ch   = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RESOLVE           => [$host . ':' . $port . ':' . $ip],
            CURLOPT_FOLLOWLOCATION    => false, // each redirect is checked above, on the next hop
            CURLOPT_PROTOCOLS         => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_CONNECTTIMEOUT_MS => min(3000, $left),
            CURLOPT_TIMEOUT_MS        => $left,
            CURLOPT_USERAGENT         => 'Mozilla/5.0 (compatible; LinksForPerfex)',
            CURLOPT_HTTPHEADER        => ['Accept: text/html', 'Accept-Language: he,en;q=0.8'],
            CURLOPT_ENCODING          => '',
            CURLOPT_WRITEFUNCTION     => function ($ch, $chunk) use (&$html) {
                $html .= $chunk;

                return strlen($html) > 524288 ? 0 : strlen($chunk); // the head is long read by then
            },
        ]);
        curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $next = (string) curl_getinfo($ch, CURLINFO_REDIRECT_URL);
        $type = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($code >= 300 && $code < 400 && $next !== '') {
            $url = $next;
            continue;
        }
        break;
    }

    // A private Google Doc answers with Google's sign-in page; its title is not the link's.
    // shortcut: known sign-in addresses only, extend the pattern when another one shows up
    if ($code < 200 || $code >= 300 || $html === '' || ($type !== '' && stripos($type, 'html') === false)
        || ($url !== $start && preg_match('#^https?://(accounts|login|signin)\.|/(sign-?in|log-?in|servicelogin)\b#i', $url))) {
        return $none;
    }

    if (preg_match('/charset=["\']?([\w-]+)/i', $type . ' ' . substr($html, 0, 4096), $m) && strcasecmp($m[1], 'utf-8') !== 0) {
        $converted = @iconv($m[1], 'UTF-8//IGNORE', $html);
        $html      = $converted !== false ? $converted : $html;
    }

    $doc = new DOMDocument();
    $quiet = libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();
    libxml_use_internal_errors($quiet);

    $meta = [];
    foreach ($doc->getElementsByTagName('meta') as $tag) {
        $key = strtolower($tag->getAttribute('property') ?: $tag->getAttribute('name'));
        if ($key !== '' && !isset($meta[$key])) {
            $meta[$key] = $tag->getAttribute('content');
        }
    }
    $titleTag = $doc->getElementsByTagName('title')->item(0);

    $clean = function ($s, $max) {
        $s = trim(preg_replace('/\s+/u', ' ', (string) $s) ?? '');

        return mb_check_encoding($s, 'UTF-8') ? mb_substr($s, 0, $max) : '';
    };

    return [
        'title'       => $clean(($meta['og:title'] ?? '') ?: ($titleTag ? $titleTag->textContent : ''), 255),
        'description' => $clean(($meta['description'] ?? '') ?: ($meta['og:description'] ?? ''), 1000),
    ];
}

/**
 * Get link icon based on URL
 * @param string $url The URL to analyze
 * @return string Font Awesome icon class
 */
function links_for_perfex_get_link_icon($url)
{
    // Font Awesome 6 (Perfex's): the views print "fa " first, and brands need fa-brands
    $url = strtolower($url);

    if (strpos($url, 'github.com') !== false) {
        return 'fa-brands fa-github';
    } elseif (strpos($url, 'docs.google.com') !== false || strpos($url, 'drive.google.com') !== false) {
        return 'fa-brands fa-google';
    } elseif (strpos($url, 'figma.com') !== false) {
        return 'fa-brands fa-figma';
    } elseif (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
        return 'fa-brands fa-youtube';
    } elseif (strpos($url, 'dropbox.com') !== false) {
        return 'fa-brands fa-dropbox';
    } else {
        return 'fa-external-link';
    }
}