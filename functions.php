<?php

define( 'BANTU_FONTS_URL', 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap' );

/* Preconnect hints — must land before the font request */
add_action( 'wp_head', function() {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}, 2 );

/* Load fonts and theme stylesheet on the frontend */
add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_style( 'bantu-learn-fonts', BANTU_FONTS_URL, [], null );

	wp_dequeue_style( 'theme-style' );
	wp_deregister_style( 'theme-style' );
	wp_enqueue_style( 'theme-style', get_stylesheet_uri(), [ 'bantu-learn-fonts' ], filemtime( get_stylesheet_directory() . '/style.css' ) );
} );

/* Load fonts and theme stylesheet in the block editor */
add_action( 'after_setup_theme', function() {
	add_editor_style( BANTU_FONTS_URL );
	add_editor_style( 'style.css' );
} );

/* Add theme support */
add_theme_support('title-tag');
add_theme_support('editor-styles');
add_editor_style('assets/css/editor.css');

/* Register ACF fields */
function bl_register_acf_blocks() {
    register_block_type( get_template_directory() . '/blocks/course-hero' );
    register_block_type( get_template_directory() . '/blocks/course-slots' );
    register_block_type( get_template_directory() . '/blocks/course-overview' );
    register_block_type( get_template_directory() . '/blocks/course-outcomes' );
    register_block_type( get_template_directory() . '/blocks/course-who' );
    register_block_type( get_template_directory() . '/blocks/course-steps' );
    register_block_type( get_template_directory() . '/blocks/course-cancellation' );
    register_block_type( get_template_directory() . '/blocks/course-related' );
    register_block_type( get_template_directory() . '/blocks/course-list' );
}
add_action( 'init', 'bl_register_acf_blocks' );

/* Add courses to the primary navigation menu */
require_once get_template_directory() . '/inc/add-courses-to-nav.php';

/* Fetch time slots for an Amelia service, shaped like the ACF time_slots repeater */
function bl_get_amelia_time_slots( $service_id ) {
    global $wpdb;

    $day_full = [
        1 => 'Monday', 2 => 'Tuesday',  3 => 'Wednesday',
        4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday',
    ];
    $day_abbr = [
        1 => 'Mon', 2 => 'Tue', 3 => 'Wed',
        4 => 'Thu', 5 => 'Fri', 6 => 'Sat', 7 => 'Sun',
    ];

    $rows = $wpdb->get_results( $wpdb->prepare(
        "SELECT wd.dayIndex, p.startTime, s.duration
         FROM {$wpdb->prefix}amelia_providers_to_periods p
         JOIN {$wpdb->prefix}amelia_providers_to_periods_services ps ON ps.periodId = p.id
         JOIN {$wpdb->prefix}amelia_providers_to_weekdays wd ON wd.id = p.weekDayId
         JOIN {$wpdb->prefix}amelia_services s ON s.id = ps.serviceId
         WHERE ps.serviceId = %d
         ORDER BY p.startTime, wd.dayIndex",
        (int) $service_id
    ) );

    // Group by startTime so days sharing a slot are combined: "Tue / Wed / Thu"
    $grouped = [];
    foreach ( $rows as $row ) {
        $key = $row->startTime . '|' . $row->duration;
        $grouped[ $key ]['days'][]    = (int) $row->dayIndex;
        $grouped[ $key ]['startTime'] = $row->startTime;
        $grouped[ $key ]['duration']  = $row->duration;
    }

    $slots = [];
    foreach ( $grouped as $group ) {
        $days = $group['days'];
        if ( count( $days ) === 1 ) {
            $label = $day_full[ $days[0] ] ?? ( 'Day ' . $days[0] );
        } else {
            $label = implode( ' / ', array_map( fn( $d ) => $day_abbr[ $d ] ?? ( 'Day ' . $d ), $days ) );
        }
        $slots[] = [
            'slot_name'     => $label,
            'slot_time'     => date( 'g:i a', strtotime( $group['startTime'] ) ),
            'slot_duration' => round( $group['duration'] / 60 ) . ' min',
        ];
    }

    return $slots ?: null;
}