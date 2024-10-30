<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @package Mobilizon_Mirror
 * Template Name: Mobilizon_Mirror Event Archive Cards
 * Description: Integrate Mobilizon
 */

get_header(); ?>

<div id="primary" class="site-content">
	<div id="event-card-list" role="main">
		<?php
		// Query concerts.
		$query_args = array(
			'posts_per_page' => -1,
			'post_type'      => 'mobilizon_event',
			'order'          => 'ASC',
			'orderby'        => 'meta_value',
			'meta_key'       => 'beginsOn',
			'meta_type'      => 'DATETIME',
		);
		$the_query  = new WP_Query( $query_args );
		if ( $the_query->have_posts() ) :
			?>
			<div class="event-columns">
				<?php
				while ( $the_query->have_posts() ) :
					$the_query->the_post();
					?>
					<div class="event-card-container">
						<a class="event-card" href="<?php the_permalink(); ?>">
							<figure class="event-card-image">
								<?php
								if ( has_post_thumbnail() ) {
											the_post_thumbnail();
								} else {
									?>
									<div class="is-16by9"> </div>
								<?php } ?>
								<div class="tag-container">
								<?php foreach ( wp_get_post_tags( get_the_ID(), array( 'number' => 3 ) ) as $event_tag ) { ?>
									<span><?php echo esc_attr( $event_tag->name ); ?></span>
									<?php } ?>
								</div>
							</figure>
							<div class="card-content">
								<div class="media-left">
									<time class="datetime-container">
										<div class="datetime-container-header"></div>
										<div class="datetime-container-content">
											<?php $begins_on = get_post_meta( get_the_ID(), 'beginsOn' )[0]; ?>
											<span class="day"> <?php echo esc_html( wp_date( 'd', strtotime( $begins_on ) ) ); ?></span>
											<span class="month"> <?php echo esc_html( wp_date( 'M', strtotime( $begins_on ) ) ); ?></span>
										</div>
									</time>
								</div>
								<h4 class="event-title"><?php the_title(); ?></h4>
							</div>
						</a>
					</div>
				<?php endwhile; // end of the loop. ?>
			</div>
		<?php else : ?>
			<p><?php esc_html_e( 'Currently there are no events scheduled' ); ?></p>
		<?php endif; ?>
	</div><!-- #event-card-list -->
</div><!-- #primary -->
<?php get_footer(); ?>
