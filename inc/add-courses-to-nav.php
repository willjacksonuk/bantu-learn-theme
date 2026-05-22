<?php

add_filter('render_block', 'inject_course_links_into_navigation', 10, 2);

function inject_course_links_into_navigation($block_content, $block) {

    // Only target navigation link blocks
    if (
        !isset($block['blockName']) ||
        $block['blockName'] !== 'core/navigation-link'
    ) {
        return $block_content;
    }

    // Only target the menu item called "Courses"
    $label = $block['attrs']['label'] ?? '';

    if ($label !== 'Courses') {
        return $block_content;
    }

    // Get all non-archived courses
    $courses = get_posts([
        'post_type' => 'course',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
        'meta_query' => [
            'relation' => 'OR',
            [
                'key' => 'archived',
                'value' => '0',
                'compare' => '='
            ],
            [
                'key' => 'archived',
                'compare' => 'NOT EXISTS'
            ]
        ]
    ]);

    if (!$courses) {
        return $block_content;
    }

    // Add submenu classes to parent item
    $block_content = str_replace(
        'wp-block-navigation-item',
        'wp-block-navigation-item has-child open-on-hover-click',
        $block_content
    );

    // Build submenu
    $submenu = '
        <button
            class="wp-block-navigation__submenu-icon wp-block-navigation-submenu__toggle"
            aria-label="Open menu"
        ></button>

        <ul class="wp-block-navigation__submenu-container">
    ';

    foreach ($courses as $course) {

        if (get_field('coming_soon', $course->ID)) {
            continue;
        }

        $submenu .= sprintf(
            '
            <li class="wp-block-navigation-item">
                <a class="wp-block-navigation-item__content" href="%s">
                    <span class="wp-block-navigation-item__label">%s</span>
                </a>
            </li>
            ',
            esc_url(get_permalink($course->ID)),
            esc_html($course->post_title)
        );
    }

    $submenu .= '</ul>';

    // Inject submenu before closing </li>
    $block_content = str_replace(
        '</li>',
        $submenu . '</li>',
        $block_content
    );

    return $block_content;
}