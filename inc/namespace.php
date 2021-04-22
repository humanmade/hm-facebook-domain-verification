<?php
/**
 * Register setting in admin and output Facebook domain verification meta tag.
 */

namespace HM\Facebook_Domain_Verification;

/**
 * Register actions and filters.
 *
 * @return void
 */
function bootstrap() {
	add_action( 'admin_init', __NAMESPACE__ . '\\add_settings' );
	add_action( 'wp_head', __NAMESPACE__ . '\\meta_tag' );
}

/**
 * Add Facebook domain verification section on general setttings screen with a single field for the verification ID.
 *
 * @return void
 */
function add_settings() {

	// Add settings section.
	add_settings_section( 'hm_facebook_verification', esc_html__( 'Facebook Domain Verification', 'hm_fdv' ), null, 'general' );

	// Add settings field.
	settings_field(
		[
			'name' => 'hm_facebook_domain_verification_code',
			'label' => __( 'Verification code', 'hm_fdv' ),
			'description' => __( 'Enter your domain verification code from Facebook.', 'hm_fdv' ),
		]
	);
}

/**
 * Wrapper around `add_settings_field` and `register_setting`.
 *
 * @param array $args Array of field name, label and description.
 * @return void
 */
function settings_field( $args ) {
	$args = wp_parse_args(
		$args,
		[
			'name' => '',
			'label' => '',
			'description' => '',
		]
	);

	// Add settings field.
	add_settings_field(
		$args['name'],
		settings_field_label(
			[
				'name' => $args['name'],
				'label' => $args['label'],
			]
		),
		__NAMESPACE__ . '\\settings_text_field',
		'general',
		'hm_facebook_verification',
		[
			'value'       => get_option( $args['name'], '' ),
			'name'        => $args['name'],
			'description' => $args['description']
		]
	);

	// Register settings field.
	register_setting( 'general', $args['name'], 'sanitize_text_field' );
}

/**
 * Format a field label.
 *
 * @param array $args Array of field name and label.
 * @return string
 */
function settings_field_label( $args ) {
	$args = wp_parse_args(
		$args,
		[
			'name' => '',
			'label' => '',
		]
	);

	return sprintf(
		'<label for="%1$s">%2$s</label>',
		esc_attr( $args['name'] ),
		esc_html( $args['label'] )
	);
}

/**
 * Render a text field.
 *
 * @param array $args Array of field name, value and description.
 * @return void
 */
function settings_text_field( $args ) {
	$args = wp_parse_args(
		$args,
		[
			'name' => '',
			'value' => '',
			'description' => ''
		]
	);

	printf( '<input type="text" class="regular-text" id="%1$s" name="%1$s" value="%2$s" %3$s/>%4$s',
		esc_attr( $args['name'] ),
		esc_attr( $args['value'] ),
		$args['description'] ? sprintf( 'aria-describedby="%s"', esc_attr( $args['name'] . '-description' ) ) : '',
		$args['description'] ? sprintf( '<p class="description" id="%1$s">%2$s</p>', esc_attr( $args['name'] . '-description' ), esc_html( $args['description'] ) ) : ''
	);
}

/**
 * Render Facebook domain verification meta tag in document head.
 *
 * @return void
 */
function meta_tag() {
	$facebook_domain_verification_code = get_option( 'hm_facebook_domain_verification_code', '' );
	if ( empty( $facebook_domain_verification_code ) ) {
		return;
	}

	printf(
		'<meta name="facebook-domain-verification" content="%s" />',
		esc_attr( $facebook_domain_verification_code )
	);
}
