<?php
/**
 * The template for displaying a single event
 *
 * @package Mobilizon_Mirror
 * @subpackage Mobilizon_Mirror
 * @since 1.0.0
 */

get_header();
?>

<?php
$metadata        = get_post_meta( get_the_ID() );
$begin_unix_time = strtotime( $metadata['beginsOn'][0] );
$end_unix_time   = strtotime( $metadata['endsOn'][0] );
?>

<main id="event">
	<?php the_post_thumbnail(); ?>
	<div class="intro-wrapper">
		<?php if ( has_post_thumbnail() ) { ?>
			<div class="date-calendar-icon-wrapper">
				<time class="datetime-container">
					<div class="datetime-container-header"></div>
					<div class="datetime-container-content">
						<span class="day"><?php echo esc_html( wp_date( 'd', $begin_unix_time ) ); ?></span>
						<span class="month"><?php echo esc_html( wp_date( 'M', $begin_unix_time ) ); ?></span>
					</div>
				</time>
			</div>
		<?php } ?>
		<h2 class="event-title">
			<?php the_title(); ?>
		</h2>
		<div class="event-tag-list">
			<?php foreach ( wp_get_post_tags( get_the_ID() ) as $event_tag ) { ?>
			<a href="<?php echo esc_url( get_term_link( $event_tag, 'tag' ) ); ?>"><button class="wp-block-button"><?php echo esc_attr( $event_tag->name ); ?></button></a>
			<?php } ?>
		</div>
	</div>
	<div class="event-description-wrapper">
		<aside class="event-metadata">
			<h4> <?php esc_html_e( 'Place', 'mobilizon-mirror' ); ?> </h4>
			<div class="eventMetaDataBlock">
			<?php
			echo join(
				'<br>',
				array(
					esc_attr( $metadata['place'][0] ),
					esc_attr( $metadata['street'][0] ),
					esc_attr( $metadata['postalCode'][0] ) . ' ' . esc_attr( $metadata['city'][0] ),
				)
			);
			?>
			</div>
			<h4> <?php esc_html_e( 'Date and Time', 'mobilizon-mirror' ); ?> </h4>
			<div class="eventMetaDataBlock">
				<?php
				if ( wp_date( 'd. M. Y', $begin_unix_time ) === wp_date( 'd. M. Y', $end_unix_time ) ) {
							echo esc_html( wp_date( 'D, d. M. Y ', $begin_unix_time ) ) . esc_html__( 'from', 'mobilizon-mirror' ) . esc_html( wp_date( ' G:i     ', $begin_unix_time ) ) . ' ' . esc_html__( 'to', 'mobilizon-mirror' ) . ' ' . esc_html( wp_date( 'G:i', $end_unix_time ) );
				} else {
							echo esc_html( wp_date( 'D, d. M. Y ', $begin_unix_time ) ) . esc_html__( 'from', 'mobilizon-mirror' ) . esc_html( wp_date( ' G:i ', $begin_unix_time ) ) . ' ' . esc_html__( 'to', 'mobilizon-mirror' ) . ' <nobr>' . esc_html( wp_date( 'D, d. M. Y ', $end_unix_time ) ) . '</nobr> ' . esc_html( wp_date( 'G:i', $end_unix_time ) );
				}
				?>
			</div>
			<h4> <?php esc_html_e( 'Organized by', 'mobilizon-mirror' ); ?> </h4>
			<div class="eventMetaDataBlock">
				<a href="<?php echo esc_url( $metadata['organizerURL'][0] ); ?>" target="blank"><?php echo esc_attr( $metadata['organizerName'][0] ); ?></a>
			</div>
			<h4> <?php esc_html_e( 'Website', 'mobilizon-mirror' ); ?> </h4>
			<div class="eventMetaDataBlock">
				<a href="<?php echo esc_url( $metadata['onlineAddress'][0] ); ?>" target="blank"><?php echo( esc_url( wp_parse_url( $metadata['onlineAddress'][0], PHP_URL_HOST ) ) ); ?></a>
			</div>
		</aside>
		<article class="event-description">
			<?php the_content(); ?>

		</article>

	</div>
	<hr>
	<footer class="event-url">
	<?php echo esc_html_e( 'Originally posted on ', 'mobilizon-mirror' ); ?>
	<a href="<?php echo esc_url( $metadata['url'][0] ); ?>">
		<?php
		$url_parts = wp_parse_url( $metadata['url'][0] );
		echo esc_url( 'https://' . $url_parts['host'] );
		?>
	</a>
	</footer>

</main>
<?php get_footer(); ?>
