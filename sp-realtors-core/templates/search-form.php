<?php
/**
 * Fallback property search form (GET → /properties/).
 *
 * Works without JavaScript. Theme override: {theme}/template-parts/search-form.php
 *
 * @package SP_Realtors_Core
 *
 * @var array $args { @type array $filters Current filter values. }
 */

defined( 'ABSPATH' ) || exit;

$spr_filters  = isset( $args['filters'] ) ? $args['filters'] : spr_get_filter_values();
$spr_purposes = spr_get_filter_terms( 'purpose' );
$spr_current  = ! empty( $spr_filters['purpose'] ) ? $spr_filters['purpose'][0] : 'buy';
$spr_uid      = wp_unique_id( 'spr-search-' );
?>
<form class="spr-search" role="search" method="get" action="<?php echo esc_url( spr_get_properties_url() ); ?>">
	<?php if ( $spr_purposes ) : ?>
		<fieldset class="spr-search__purpose">
			<legend class="screen-reader-text"><?php esc_html_e( 'I want to', 'sp-realtors-core' ); ?></legend>
			<?php foreach ( $spr_purposes as $spr_term ) : ?>
				<label class="spr-search__tab">
					<input type="radio" name="purpose" value="<?php echo esc_attr( $spr_term->slug ); ?>" <?php checked( $spr_current, $spr_term->slug ); ?>>
					<span><?php echo esc_html( $spr_term->name ); ?></span>
				</label>
			<?php endforeach; ?>
		</fieldset>
	<?php endif; ?>

	<div class="spr-search__fields">
		<div class="spr-search__field">
			<label for="<?php echo esc_attr( $spr_uid ); ?>-location"><?php esc_html_e( 'Location', 'sp-realtors-core' ); ?></label>
			<select id="<?php echo esc_attr( $spr_uid ); ?>-location" name="location">
				<option value=""><?php esc_html_e( 'All locations', 'sp-realtors-core' ); ?></option>
				<?php foreach ( spr_get_filter_terms( 'location' ) as $spr_term ) : ?>
					<option value="<?php echo esc_attr( $spr_term->slug ); ?>" <?php selected( in_array( $spr_term->slug, $spr_filters['location'], true ) ); ?>><?php echo esc_html( $spr_term->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="spr-search__field">
			<label for="<?php echo esc_attr( $spr_uid ); ?>-type"><?php esc_html_e( 'Property Type', 'sp-realtors-core' ); ?></label>
			<select id="<?php echo esc_attr( $spr_uid ); ?>-type" name="ptype">
				<option value=""><?php esc_html_e( 'All types', 'sp-realtors-core' ); ?></option>
				<?php foreach ( spr_get_filter_terms( 'ptype' ) as $spr_term ) : ?>
					<option value="<?php echo esc_attr( $spr_term->slug ); ?>" <?php selected( in_array( $spr_term->slug, $spr_filters['ptype'], true ) ); ?>><?php echo esc_html( $spr_term->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="spr-search__field">
			<label for="<?php echo esc_attr( $spr_uid ); ?>-budget"><?php esc_html_e( 'Budget', 'sp-realtors-core' ); ?></label>
			<select id="<?php echo esc_attr( $spr_uid ); ?>-budget" name="budget">
				<option value=""><?php esc_html_e( 'Any budget', 'sp-realtors-core' ); ?></option>
				<?php foreach ( $spr_purposes as $spr_term ) : ?>
					<?php $spr_ranges = spr_get_budget_ranges( $spr_term->slug ); ?>
					<?php if ( $spr_ranges ) : ?>
						<optgroup label="<?php echo esc_attr( $spr_term->name ); ?>" data-purpose="<?php echo esc_attr( $spr_term->slug ); ?>">
							<?php foreach ( $spr_ranges as $spr_key => $spr_range ) : ?>
								<option value="<?php echo esc_attr( $spr_key ); ?>" <?php selected( $spr_filters['budget'], $spr_key ); ?>><?php echo esc_html( $spr_range['label'] ); ?></option>
							<?php endforeach; ?>
						</optgroup>
					<?php endif; ?>
				<?php endforeach; ?>
			</select>
		</div>

		<button type="submit" class="spr-btn spr-btn--primary spr-search__submit"><?php esc_html_e( 'Search', 'sp-realtors-core' ); ?></button>
	</div>
</form>
