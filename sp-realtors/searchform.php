<?php
/**
 * Site search form (used by get_search_form()).
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

$sp_unique_id = wp_unique_id( 'search-form-' );
?>
<form role="search" method="get" class="sp-realtors-searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $sp_unique_id ); ?>" class="screen-reader-text">
		<?php esc_html_e( 'Search for:', 'sp-realtors' ); ?>
	</label>
	<input type="search" id="<?php echo esc_attr( $sp_unique_id ); ?>" class="search-field" placeholder="<?php esc_attr_e( 'Search…', 'sp-realtors' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	<button type="submit" class="spr-btn spr-btn--primary search-submit">
		<?php esc_html_e( 'Search', 'sp-realtors' ); ?>
	</button>
</form>
