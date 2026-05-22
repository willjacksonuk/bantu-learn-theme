<?php

/**
 * Template: Single Course
 * Post type: course
 * Requires: ACF Pro
 */

add_filter('document_title_parts', function ($parts) {
    $parts['title'] = get_the_title();
    $parts['site']  = 'Bantu Learn';
    return $parts;
});



get_header();

if (have_posts()) :
    while (have_posts()) : the_post();

        // ── Core fields ───────────────────────────────────────────────────────────────
        $language       = get_field('language');
        $level          = get_field('level');
        $audience       = get_field('audience');
        $short_desc     = get_field('short_description');
        $delivery_mode  = get_field('delivery_mode');
        $duration       = get_field('duration');
        $session_length = get_field('session_length');
        $max_students   = get_field('max_students');
        $location_note  = get_field('location_note');
        $format         = get_field('format');
        $teacher        = get_field('teacher');
        $booking_url       = get_field('booking_url') ?: '#';
        $amelia_service_id = get_field('amelia_service_id');

        // ── Pricing ───────────────────────────────────────────────────────────────────
        $price       = get_field('price');
        $price_label = get_field('price_label');
        $price_equiv = get_field('price_equivalent');

        // ── Repeaters ─────────────────────────────────────────────────────────────────
        $time_slots      = $amelia_service_id
            ? (bl_get_amelia_time_slots($amelia_service_id) ?: get_field('time_slots'))
            : get_field('time_slots');
        $outcomes        = get_field('learning_outcomes');
        $who_for         = get_field('who_its_for');
        $steps           = get_field('lesson_steps');
        $cancellation    = get_field('cancellation_policy');
        $related_courses = get_field('related_courses');

        // ── Derived strings ───────────────────────────────────────────────────────────
        $badge        = implode(' – ', array_filter([$language, $level, $audience]));
        $duration_str = $duration && $session_length ? "{$duration} weeks, {$session_length} min sessions" : '';
        $group_str    = $max_students ? "Max {$max_students} students" : '';
        $schedule_str = $duration ? "{$duration} weeks · one lesson per week" : '';
        $lesson_str   = $session_length ? "{$session_length} minutes" : '';
        $group_full   = $max_students ? "Maximum {$max_students} students" : '';

?>

        <!-- ── HERO ──────────────────────────────────────────────────────────────────── -->
        <section class="course-hero">
            <div class="course-hero-inner">

                <!-- Left: course info -->
                <div>
                    <?php if ($badge) : ?>
                        <p class="course-hero-badge"><?php echo esc_html($badge); ?></p>
                    <?php endif; ?>

                    <h1><?php the_title(); ?> - <?php echo esc_html($audience); ?> (<?php echo esc_html($delivery_mode); ?>)</h1>

                    <?php if ($short_desc) : ?>
                        <p class="course-hero-intro"><?php echo esc_html($short_desc); ?></p>
                    <?php endif; ?>

                    <div class="course-hero-tags">
                        <?php if ($delivery_mode) : ?>
                            <p class="hero-tag"><i class="fa-solid fa-display" aria-hidden="true"></i> <?php echo esc_html($delivery_mode); ?></p>
                        <?php endif; ?>
                        <?php if ($duration_str) : ?>
                            <p class="hero-tag"><i class="fa-solid fa-clock" aria-hidden="true"></i> <?php echo esc_html($duration_str); ?></p>
                        <?php endif; ?>
                        <?php if ($group_str) : ?>
                            <p class="hero-tag"><i class="fa-solid fa-users" aria-hidden="true"></i> <?php echo esc_html($group_str); ?></p>
                        <?php endif; ?>
                        <?php if ($location_note) : ?>
                            <p class="hero-tag"><i class="fa-solid fa-globe" aria-hidden="true"></i> <?php echo esc_html($location_note); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right: booking card -->
                <div class="booking-card">
                    <div class="booking-price">
                        <?php if ($price) : ?>
                            <p class="booking-price-main">£<?php echo esc_html($price); ?></p>
                            <p class="booking-price-period">/ <?php echo esc_html($duration); ?> weeks</p>
                        <?php endif; ?>
                    </div>
                    <?php if ($price && $duration && $session_length) : ?>
                        <p class="booking-rate">Equivalent to £<?php echo esc_html(round($price / $duration / (60 / $session_length))); ?>/hr</p>
                    <?php endif; ?>

                    <?php if ($price_equiv) : ?>
                        <p class="booking-rate"><?php echo esc_html($price_equiv); ?></p>
                    <?php endif; ?>
                    <?php if ($time_slots && is_array($time_slots)) : ?>
                        <?php if ($max_students) : ?>
                            <div class="booking-slots">
                                <p><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> Limited places — groups capped at <?php echo esc_html($max_students); ?></p>
                            </div>
                        <?php endif; ?>
                        <p class="bl-note">Available time slots:</p>
                        <div class="booking-times">
                            <?php foreach ($time_slots as $i => $slot) : ?>
                                <div class="time-option<?php echo $i === 0 ? ' selected' : ''; ?>">
                                    <p class="time-option-label"><?php echo esc_html($slot['slot_name']); ?></p>
                                    <p class="time-option-sub"><?php echo esc_html($slot['slot_time']); ?> · <?php echo esc_html($slot['slot_duration']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <a class="wp-block-button__link has-background wp-element-button bl-btn primary<?php echo $amelia_service_id ? ' amelia-popup' : ''; ?>"
                            href="<?php echo $amelia_service_id ? '#' : esc_url($booking_url); ?>"
                            style="display:block;width:100%;text-align:center;margin-bottom:16px;">
                            Book your place <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    <?php else : ?>
                        <p class="bl-note">Sorry, no slots are currently available.</p>
                    <?php endif; ?>

                    <div class="booking-trust">
                        <p class="booking-trust-item"><i class="fa-solid fa-lock" aria-hidden="true"></i> Secure payment via Stripe or PayPal</p>
                        <p class="booking-trust-item"><i class="fa-solid fa-rotate" aria-hidden="true"></i> Reschedule up to 48 hrs before start</p>
                        <p class="booking-trust-item"><i class="fa-solid fa-award" aria-hidden="true"></i> Certificate of completion included</p>
                    </div>
                </div>

            </div>
        </section>

        <!-- ── BODY ──────────────────────────────────────────────────────────────────── -->
        <div class="course-body">

            <!-- Main content -->
            <div class="course-content">

                <!-- Course overview -->
                <div class="content-section">
                    <h2>Course overview</h2>
                    <div class="overview-table">
                        <?php if ($language) : ?>
                            <div class="overview-row">
                                <div class="overview-key"><i class="fa-solid fa-globe" aria-hidden="true"></i> Language</div>
                                <div class="overview-val"><?php echo esc_html($language); ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if ($level) : ?>
                            <div class="overview-row">
                                <div class="overview-key"><i class="fa-solid fa-chart-bar" aria-hidden="true"></i> Level</div>
                                <div class="overview-val"><?php echo esc_html($level); ?> — Complete beginner</div>
                            </div>
                        <?php endif; ?>
                        <?php if ($format) : ?>
                            <div class="overview-row">
                                <div class="overview-key"><i class="fa-solid fa-desktop" aria-hidden="true"></i> Format</div>
                                <div class="overview-val"><?php echo esc_html($format); ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if ($schedule_str) : ?>
                            <div class="overview-row">
                                <div class="overview-key"><i class="fa-solid fa-calendar" aria-hidden="true"></i> Schedule</div>
                                <div class="overview-val"><?php echo esc_html($schedule_str); ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if ($lesson_str) : ?>
                            <div class="overview-row">
                                <div class="overview-key"><i class="fa-solid fa-clock" aria-hidden="true"></i> Lesson length</div>
                                <div class="overview-val"><?php echo esc_html($lesson_str); ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if ($group_full) : ?>
                            <div class="overview-row">
                                <div class="overview-key"><i class="fa-solid fa-users" aria-hidden="true"></i> Group size</div>
                                <div class="overview-val"><?php echo esc_html($group_full); ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if ($teacher) : ?>
                            <div class="overview-row">
                                <div class="overview-key"><i class="fa-solid fa-chalkboard-teacher" aria-hidden="true"></i> Teacher</div>
                                <div class="overview-val"><?php echo esc_html($teacher); ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if ($location_note) : ?>
                            <div class="overview-row">
                                <div class="overview-key"><i class="fa-solid fa-map-marker-alt" aria-hidden="true"></i> Location</div>
                                <div class="overview-val"><?php echo esc_html($location_note); ?></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- What you'll learn -->
                <?php if ($outcomes) : ?>
                    <div class="content-section">
                        <h2>What you'll learn</h2>
                        <p>By the end of this course, you will be able to:</p>
                        <div class="learn-grid">
                            <?php foreach ($outcomes as $i => $outcome) : ?>
                                <div class="learn-item">
                                    <div class="item-check">
                                        <i class="fa-solid fa-check" aria-hidden="true"></i>
                                    </div>
                                    <p class="learn-item-title"><?php echo esc_html($outcome['outcome_title']); ?></p>
                                    <p class="learn-item-body"><?php echo esc_html($outcome['outcome_description']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Who it's for -->
                <?php if ($who_for) : ?>
                    <div class="content-section">
                        <h2>Who this course is for</h2>
                        <p>This <?php echo esc_html(get_the_title()); ?> course is ideal if:</p>
                        <div class="who-list">
                            <?php foreach ($who_for as $item) : ?>
                                <div class="who-item">
                                    <div class="item-check">
                                        <i class="fa-solid fa-check" aria-hidden="true"></i>
                                    </div>
                                    <p><?php echo esc_html($item['statement']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- How online lessons work -->
                <?php if ($steps) : ?>
                    <div class="content-section">
                        <h2>How online lessons work</h2>
                        <div class="online-steps">
                            <?php $step_num = 1;
                            foreach ($steps as $step) : ?>
                                <div class="online-step">
                                    <div class="online-step-dot"><?php echo $step_num++; ?></div>
                                    <div>
                                        <p class="online-step-title"><?php echo esc_html($step['step_title']); ?></p>
                                        <p class="online-step-body"><?php echo esc_html($step['step_description']); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Cancellation -->
                <?php if ($cancellation) : ?>
                    <div class="content-section">
                        <h2>Cancellation and rescheduling</h2>
                        <div class="cancel-box">
                            <?php echo wp_kses_post($cancellation); ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div><!-- .course-content -->

            <!-- Sidebar -->
            <aside class="course-sidebar">

                <!-- Sticky booking card -->
                <div class="sidebar-book-card">
                    <div class="booking-price">
                        <?php if ($price) : ?>
                            <p class="booking-price-main">£<?php echo esc_html($price); ?></p>
                            <p class="booking-price-period">/ <?php echo esc_html($duration); ?> weeks</p>
                        <?php endif; ?>
                    </div>
                    <?php if ($price_equiv) : ?>
                        <p class="booking-rate"><?php echo esc_html($price_equiv); ?> · full payment on booking</p>
                    <?php endif; ?>
                    <?php if ($time_slots && is_array($time_slots)) : ?>

                        <?php if ($max_students) : ?>
                            <div class="booking-slots">
                                <p><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> Limited places — groups capped at <?php echo esc_html($max_students); ?></p>
                            </div>
                        <?php endif; ?>

                        <a class="wp-block-button__link has-background wp-element-button bl-btn primary<?php echo $amelia_service_id ? ' amelia-popup' : ''; ?>"
                            href="<?php echo $amelia_service_id ? '#' : esc_url($booking_url); ?>"
                            style="display:block;width:100%;text-align:center;margin-bottom:16px;">
                            Book your place <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                        </a>
                    <?php else : ?>
                        <p class="bl-note">Sorry, no slots are currently available.</p>
                    <?php endif; ?>

                    <div class="booking-trust">
                        <p class="booking-trust-item"><i class="fa-solid fa-card" aria-hidden="true"></i> Card, PayPal, Apple &amp; Google Pay</p>
                        <p class="booking-trust-item"><i class="fa-solid fa-award" aria-hidden="true"></i> Certificate of completion</p>
                        <p class="booking-trust-item"><i class="fa-solid fa-refresh" aria-hidden="true"></i> Reschedule up to 48 hrs before</p>
                    </div>
                </div>

                <!-- Other courses -->
                <?php if ($related_courses) : ?>
                    <div class="sidebar-other-card">
                        <p class="sidebar-other-title">Other courses</p>
                        <div class="sidebar-other-grid">
                            <?php foreach ($related_courses as $related) :
                                $status = get_field('course_status', $related->ID) ?: 'live';
                                $is_live = $status === 'live';
                                $tag = $is_live ? 'a' : 'div';
                                $href = $is_live ? ' href="' . esc_url(get_permalink($related->ID)) . '"' : '';
                            ?>
                                <<?php echo $tag; ?> class="sidebar-other-item" <?php echo $href; ?>>
                                    <div class="sidebar-other-item-dot"></div>
                                    <div>
                                        <p class="sidebar-other-item-title"><?php echo esc_html(get_the_title($related->ID)); ?></p>
                                        <?php if (! $is_live) : ?>
                                            <p class="sidebar-other-item-body">Coming soon</p>
                                        <?php endif; ?>
                                    </div>
                                </<?php echo $tag; ?>>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </aside>

        </div><!-- .course-body -->

        <?php if ($amelia_service_id) : ?>
            <?php echo do_shortcode('[ameliastepbooking trigger="amelia-popup" trigger_type="class" in_dialog="1" service="' . esc_attr($amelia_service_id) . '"]'); ?>
        <?php endif; ?>

        <script>
            document.querySelectorAll('.time-option').forEach(function(opt) {
                opt.addEventListener('click', function() {
                    this.closest('.booking-times').querySelectorAll('.time-option').forEach(function(o) {
                        o.classList.remove('selected');
                    });
                    this.classList.add('selected');
                });
            });
        </script>

<?php
    endwhile;
endif;

get_footer();
