<?php
/**
 * Blog sidebar.
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_active_sidebar( 'blog-sidebar' ) ) {
	return;
}
?>
<aside id="secondary" class="widget-area" aria-label="<?php esc_attr_e( 'Blog sidebar', 'sp-realtors' ); ?>">
	<?php dynamic_sidebar( 'blog-sidebar' ); ?>
</aside>
