<?php
/**
 * Template Name: Mobilizon_Mirror Event Archive List
 *
 * @package Mobilizon_Mirror
 */

get_header(); ?>

<div id="primary" class="site-content">
	<div id="event-list" role="main">

	<?php
	// Query concerts.
	$args = array(
		'posts_per_page' => -1,
		'post_type'      => 'mobilizon_event',
		'order'          => 'ASC',
		'orderby'        => 'meta_value',
		'meta_key'       => 'beginsOn',
		'meta_type'      => 'DATETIME',
	);

	$the_query = new WP_Query( $args );

	if ( $the_query->have_posts() ) :
		while ( $the_query->have_posts() ) :
			$the_query->the_post();
			?>
			<time>
				<?php echo esc_html( wp_date( 'D, d. M. Y, G:i', strtotime( get_post_meta( get_the_ID(), 'beginsOn' )[0] ) ) ); ?>
			</time>
			<a href="<?php the_permalink(); ?>">
				<h3 class="event-title"><?php the_title(); ?></h3>
			</a>
			<div class="entry-content">
			</div><!-- .entry-content -->
		<?php endwhile; // end of the loop. ?>

	<?php else : ?>
		<p><?php esc_html_e( 'Currently there are no events scheduled', 'mobilizon-mirror' ); ?></p>
	<?php endif; ?>

	</div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>
