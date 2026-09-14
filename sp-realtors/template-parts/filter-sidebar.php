<?php
/**
 * Property archive filters: purpose tabs + type/configuration/location
 * checkboxes + budget. One GET form — desktop sidebar, mobile drawer
 * (inc/enqueue.php + main.js just toggle a class; the form works with JS off).
 *
 * @package SP_Realtors
 */

defined( 'ABSPATH' ) || exit;

if ( ! sp_realtors_core_active() ) {
	return;
}

$sp_filters  = spr_get_filter_values();
$sp_purposes = spr_get_filter_terms( 'purpose' );
$sp_purpose  = ! empty( $sp_filters['purpose'] ) ? $sp_filters['purpose'][0] : 'buy';
$sp_uid      = wp_unique_id( 'spr-filters-' );
?>
<form class="spr-filters" method="get" action="<?php echo esc_url( spr_get_properties_url() ); ?>">
	<input type="hidden" name="sort" value="<?php echo esc_attr( $sp_filters['sort'] ); ?>">

	<div class="spr-filters__head">
		<h2><?php esc_html_e( 'Filters', 'sp-realtors' ); ?></h2>
		<button type="button" class="spr-filters__close" aria-label="<?php esc_attr_e( 'Close filters', 'sp-realtors' ); ?>">
			<?php echo sp_realtors_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</button>
	</div>

	<?php if ( $sp_purposes ) : ?>
		<fieldset class="spr-filters__group spr-filters__group--purpose">
			<legend><?php esc_html_e( 'I want to', 'sp-realtors' ); ?></legend>
			<div class="spr-search__purpose">
				<?php foreach ( $sp_purposes as $sp_term ) : ?>
					<label class="spr-search__tab">
						<input type="radio" name="purpose" value="<?php echo esc_attr( $sp_term->slug ); ?>" <?php checked( $sp_purpose, $sp_term->slug ); ?>>
						<span><?php echo esc_html( $sp_term->name ); ?></span>
					</label>
				<?php endforeach; ?>
			</div>
		</fieldset>
	<?php endif; ?>

	<?php
	$sp_groups = array(
		'ptype'  => __( 'Property Type', 'sp-realtors' ),
		'config' => __( 'Configuration', 'sp-realtors' ),
	);
	foreach ( $sp_groups as $sp_key => $sp_label ) :
		$sp_terms = spr_get_filter_terms( $sp_key );
		if ( ! $sp_terms ) {
			continue;
		}
		?>
		<fieldset class="spr-filters__group">
			<legend><?php echo esc_html( $sp_label ); ?></legend>
			<?php foreach ( $sp_terms as $sp_term ) : ?>
				<label class="spr-filters__checkbox">
					<input type="checkbox" name="<?php echo esc_attr( $sp_key ); ?>[]" value="<?php echo esc_attr( $sp_term->slug ); ?>" <?php checked( in_array( $sp_term->slug, $sp_filters[ $sp_key ], true ) ); ?>>
					<span><?php echo esc_html( $sp_term->name ); ?></span>
				</label>
			<?php endforeach; ?>
		</fieldset>
		<?php
	endforeach;

	$sp_ranges = spr_get_budget_ranges( $sp_purpose );
	if ( $sp_ranges ) :
		?>
		<fieldset class="spr-filters__group">
			<legend><?php esc_html_e( 'Budget', 'sp-realtors' ); ?></legend>
			<label class="spr-filters__checkbox">
				<input type="radio" name="budget" value="" <?php checked( '', $sp_filters['budget'] ); ?>>
				<span><?php esc_html_e( 'Any budget', 'sp-realtors' ); ?></span>
			</label>
			<?php foreach ( $sp_ranges as $sp_key => $sp_range ) : ?>
				<label class="spr-filters__checkbox">
					<input type="radio" name="budget" value="<?php echo esc_attr( $sp_key ); ?>" <?php checked( $sp_key, $sp_filters['budget'] ); ?>>
					<span><?php echo esc_html( $sp_range['label'] ); ?></span>
				</label>
			<?php endforeach; ?>
		</fieldset>
	<?php endif; ?>

	<?php $sp_locations = spr_get_filter_terms( 'location' ); ?>
	<?php if ( $sp_locations ) : ?>
		<fieldset class="spr-filters__group">
			<legend><?php esc_html_e( 'Location', 'sp-realtors' ); ?></legend>
			<?php foreach ( $sp_locations as $sp_term ) : ?>
				<label class="spr-filters__checkbox">
					<input type="checkbox" name="location[]" value="<?php echo esc_attr( $sp_term->slug ); ?>" <?php checked( in_array( $sp_term->slug, $sp_filters['location'], true ) ); ?>>
					<span><?php echo esc_html( $sp_term->name ); ?></span>
				</label>
			<?php endforeach; ?>
		</fieldset>
	<?php endif; ?>

	<div class="spr-filters__actions">
		<button type="submit" class="spr-btn spr-btn--primary"><?php esc_html_e( 'Apply Filters', 'sp-realtors' ); ?></button>
		<?php if ( spr_has_active_filters( $sp_filters ) ) : ?>
			<a class="spr-filters__clear" href="<?php echo esc_url( spr_get_properties_url() ); ?>"><?php esc_html_e( 'Clear All', 'sp-realtors' ); ?></a>
		<?php endif; ?>
	</div>
</form>
