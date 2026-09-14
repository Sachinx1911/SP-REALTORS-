<?php
/**
 * Floating search card wrapping the plugin's search form
 * (spr_get_template_part('search-form') renders the actual fields — see
 * sp-realtors-core/templates/search-form.php for the markup/classes styled here).
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

if ( ! sp_realtors_core_active() ) {
	return;
}
?>
<div class="sp-realtors-search-card">
	<?php spr_get_template_part( 'search-form' ); ?>
</div>
