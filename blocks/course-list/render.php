<?php

/**
 * Block: Course List
 * Renders all non-archived courses as cards.
 */

$courses = new WP_Query([
	'post_type'      => 'course',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
	'meta_query'     => [
		'relation' => 'OR',
		[
			'key'     => 'archived',
			'value'   => '1',
			'compare' => '!=',
		],
		[
			'key'     => 'archived',
			'compare' => 'NOT EXISTS',
		],
	],
]);

if (! $courses->have_posts()) {
	return;
}
?>

<div class="courses-grid">
	<?php while ($courses->have_posts()) : $courses->the_post();

		$language      = get_field('language');
		$audience      = get_field('audience');
		$delivery_mode = get_field('delivery_mode');
		$short_desc    = get_field('short_description');
		$duration      = get_field('duration');
		$max_students  = get_field('max_students');
		$location_note = get_field('location_note');
		$price         = get_field('price');
		$coming_soon   = get_field('coming_soon');

		// Card colour: primary if coming soon, secondary otherwise
		$top_class    = $coming_soon ? 'course-card-top' : 'course-card-top coming-soon';
		$badge_class  = $coming_soon ? 'primary'            : 'secondary';

		// Course name line: Title – Audience – Delivery mode
		$name_parts  = array_filter([get_the_title(), $audience, $delivery_mode]);
		$course_name = implode(' – ', $name_parts);

	?>
		<?php if ($coming_soon) : ?>
			<div class="course-card coming-soon">
			<?php else : ?>
				<a href="<?php echo esc_url(get_permalink()); ?>" class="course-card">
				<?php endif; ?>

				<div class="course-card-top <?php echo esc_attr($top_class); ?>"></div>

				<div class="course-card-body">

					<?php if ($language) : ?>
						<p class="course-badge <?php echo esc_attr($badge_class); ?>"><?php echo esc_html($language); ?></p>
					<?php endif; ?>

					<p class="course-name"><?php echo esc_html($course_name); ?></p>

					<?php if ($short_desc) : ?>
						<p class="course-desc"><?php echo esc_html($short_desc); ?></p>
					<?php endif; ?>

					<div class="course-meta">

						<?php if ($delivery_mode) : ?>
							<div class="course-meta-item">
								<i class="fa-solid fa-monitor" aria-hidden="true"></i>
								<p><?php echo esc_html($delivery_mode); ?></p>
							</div>
						<?php endif; ?>

						<?php if ($duration) : ?>
							<div class="course-meta-item">
								<i class="fa-solid fa-clock" aria-hidden="true"></i>
								<p><?php echo esc_html($duration); ?> weeks</p>
							</div>
						<?php endif; ?>

						<?php if ($max_students) : ?>
							<div class="course-meta-item">
								<i class="fa-solid fa-users" aria-hidden="true"></i>
								<p>Max <?php echo esc_html($max_students); ?></p>
							</div>
						<?php endif; ?>

						<?php if ($location_note) : ?>
							<div class="course-meta-item">
								<i class="fa-solid fa-globe" aria-hidden="true"></i>
								<p><?php echo esc_html($location_note); ?></p>
							</div>
						<?php endif; ?>

					</div>

				</div>

				<div class="course-card-footer">

					<?php if ($price) : ?>
						<p class="course-price">£<?php echo esc_html($price); ?></p>
					<?php endif; ?>

					<div class="course-cta">
						<?php if ($coming_soon) : ?>
							<p>Coming soon</p>
						<?php else : ?>
							<p>View course</p>
							<i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
						<?php endif; ?>
					</div>

				</div>

				<?php if ($coming_soon) : ?>
			</div>
		<?php else : ?>
			</a>
		<?php endif; ?>

	<?php endwhile;
	wp_reset_postdata(); ?>
</div>