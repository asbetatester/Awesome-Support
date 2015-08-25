<?php
/**
 * Get field container class.
 *
 * @since      3.0.0
 *
 * @param  string $field_name Name of the field we're getting the container class for
 * @param  string $extra      Extra classes to pass to the function
 *
 * @deprecated 3.2.0
 * @return string             The class tag with appropriate classes
 */
function wpas_get_field_container_class( $field_name = '', $extra = '' ) {

	$class = 'wpas-form-group';

	if ( isset( $_SESSION['wpas_submission_error'] ) && is_array( $_SESSION['wpas_submission_error'] ) && in_array( $field_name, $_SESSION['wpas_submission_error'] ) ) {
		$class .= ' has-error';
	}

	if ( '' != $extra ) {
		$class .= " $extra";
	}

	return $class;

}

/**
 * Get field class.
 *
 * @param  string $field_name Name of the field we're getting the class for
 * @param  string $extra      Extra classes to pass to the function
 * @param         $echo       bool Whether to echo the result or return it
 *
 * @since      3.0.0
 * @deprecated 3.2.0
 * @return string             The class tag with appropriate classes
 */
function wpas_get_field_class( $field_name = '', $extra = '', $echo = true ) {

	$class = 'wpas-form-control';

	if ( '' != $extra ) {
		$class .= " $extra";
	}

	if ( true === $echo ) {
		echo "class='$class'";
	} else {
		return $class;
	}

}

/**
 * Get temporary field value.
 *
 * Once a form is submitted, all values are kept
 * in session in case the ticket submission fails.
 * Once the submission form reloads we can pre-popupate fields
 * and avoid the pain of re-typing everything for the user.
 * When a submission is valid, the session is destroyed.
 *
 * @param  string $field_name The name of the field to get the value for
 * @return string             The temporary value for this field
 * @since  3.0.0
 * @deprecated 3.2.0
 */
function wpas_get_field_value( $field_name ) {

	$meta = get_post_meta( get_the_ID(), '_wpas_' . $field_name, true );

	if ( isset( $_SESSION['wpas_submission_form'] ) && is_array( $_SESSION['wpas_submission_form'] ) && array_key_exists( $field_name, $_SESSION['wpas_submission_form'] ) ) {
		$value = $_SESSION['wpas_submission_form'][$field_name];
	} elseif ( !empty( $meta ) ) {
		$value = $meta;
	} else {
		$value = '';
	}

	return apply_filters( 'wpas_get_field_value', esc_attr( wp_unslash( $value ) ), $field_name );

}

/**
 * Shows the message field.
 *
 * The function echoes the textarea where the user
 * may input the ticket description. The field can be
 * either a textarea or a WYSIWYG depending on the plugin settings.
 * The WYSIWYG editor uses TinyMCE with a minimal configuration.
 *
 * @since      3.0.0
 * @deprecated 3.2.0
 *
 * @param  array $editor_args Arguments used for TinyMCE
 *
 * @return void
 */
function wpas_get_message_textarea( $editor_args = array() ) {

	/**
	 * Check if the description field should use the WYSIWYG editor
	 *
	 * @var string
	 */
	$textarea_class = ( true === ( $wysiwyg = boolval( wpas_get_option( 'frontend_wysiwyg_editor' ) ) ) ) ? 'wpas-wysiwyg' : 'wpas-textarea';

	if ( true === $wysiwyg ) {

		$editor_defaults = apply_filters( 'wpas_ticket_editor_args', array(
			'media_buttons' => false,
			'textarea_name' => 'wpas_message',
			'textarea_rows' => 10,
			'tabindex'      => 2,
			'editor_class'  => wpas_get_field_class( 'wpas_message', $textarea_class, false ),
			'quicktags'     => false,
			'tinymce'       => array(
				'toolbar1' => 'bold,italic,underline,strikethrough,hr,|,bullist,numlist,|,link,unlink',
				'toolbar2' => ''
			),
		) );

		?><div class="wpas-submit-ticket-wysiwyg"><?php
		wp_editor( wpas_get_field_value( 'wpas_message' ), 'wpas-ticket-message', apply_filters( 'wpas_reply_wysiwyg_args', $editor_defaults ) );
		?></div><?php

	} else {

		/**
		 * Define if the body can be submitted empty or not.
		 *
		 * @since  3.0.0
		 * @var boolean
		 */
		$can_submit_empty = apply_filters( 'wpas_can_message_be_empty', false );
		?>
		<div class="wpas-submit-ticket-wysiwyg">
			<textarea <?php wpas_get_field_class( 'wpas_message', $textarea_class ); ?> id="wpas-ticket-message" name="wpas_message" placeholder="<?php echo apply_filters( 'wpas_form_field_placeholder_wpas_message', __( 'Describe your problem as accurately as possible', 'wpas' ) ); ?>" rows="10" <?php if ( false === $can_submit_empty ): ?>required="required"<?php endif; ?>><?php echo wpas_get_field_value( 'wpas_message' ); ?></textarea>
		</div>
	<?php }

}

/**
 * Get temporary user data.
 *
 * If the user registration fails some of the user data is saved
 * (all except the password) and can be used to pre-populate the registration
 * form after the page reloads. This function returns the desired field value
 * if any.
 *
 * @since      3.0.0
 * @deprecated 3.2.0
 *
 * @param  string $field Name of the field to get the value for
 *
 * @return string        The sanitized field value if any, an empty string otherwise
 */
function wpas_get_registration_field_value( $field ) {

	if ( isset( $_SESSION ) && isset( $_SESSION['wpas_registration_form'][ $field ] ) ) {
		return sanitize_text_field( $_SESSION['wpas_registration_form'][ $field ] );
	} else {
		return '';
	}

}