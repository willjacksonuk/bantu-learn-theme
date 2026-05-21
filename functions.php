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
}
add_action( 'init', 'bl_register_acf_blocks' );