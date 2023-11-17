<?php
$form_string .= gf_apply_filters( array( 'gform_form_tag', $form_id ), "<form method='post' enctype='multipart/form-data' {$target} id='gform_{$form_id}' {$form_css_class} action='{$action}' data-formid='{$form_id}' $novalidate>", $form );



// If Save and Continue token was provided but expired/invalid, display error message.
if ( isset( $_GET['gf_token'] ) && ! is_array( $incomplete_submission_info ) ) {

    /**
     * Modify the error message displayed when an expired/invalid Save and Continue link is used.
     *
     * @since 2.4
     *
     * @param string $message Save & Continue expired/invalid link error message.
     * @param array  $form    The current Form object.
     */
    $savecontinue_expired_message = gf_apply_filters( array(
        'gform_savecontinue_expired_message',
        $form['id'],
    ), esc_html__( 'Save and Continue link used is expired or invalid.', 'gravityforms' ), $form );

    // If message is not empty, add to form string.
    if ( ! empty( $savecontinue_expired_message ) ) {
        $form_string .= sprintf(
            '<div class="validation_error gform_validation_error">%s</div>',
            $savecontinue_expired_message
        );
    }

}

/* If the form was submitted, has multiple pages and is invalid, set the current page to the first page with an invalid field. */
if ( $has_pages && $is_postback && ! $is_valid ) {
    self::set_current_page( $form_id, GFFormDisplay::get_first_page_with_error( $form ) );
}

$current_page = self::get_current_page( $form_id );

if ( $has_pages && ! $is_admin ) {
    $pagination_type = rgars( $form, 'pagination/type' );

    if ( $pagination_type == 'percentage' ) {
        $form_string .= self::get_progress_bar( $form, $current_page, $confirmation_message );
    } else if ( $pagination_type == 'steps' ) {
        $form_string .= self::get_progress_steps( $form, $current_page );
    }
}


$form_string .= "
            <div class='gform-body gform_body'>";

//add first page if this form has any page fields
if ( $has_pages ) {
    $style         = self::is_page_active( $form_id, 1 ) ? '' : "style='display:none;'";
    $class         = ' ' . rgar( $form, 'firstPageCssClass', '' );
    $class         = esc_attr( $class );
    $form_string .= "<div id='gform_page_{$form_id}_1' class='gform_page{$class}' data-js='page-field-id-1' {$style}>
                        <div class='gform_page_fields'>";
}

$tag = GFCommon::is_legacy_markup_enabled( $form ) ? 'ul' : 'div';
$form_string .= "<{$tag} id='gform_fields_{$form_id}' class='" . GFCommon::get_ul_classes( $form ) . "'>";

if ( is_array( $form['fields'] ) ) {

    // Add honeypot field if Honeypot is enabled.
    $honeypot_handler = GFForms::get_service_container()->get( Gravity_Forms\Gravity_Forms\Honeypot\GF_Honeypot_Service_Provider::GF_HONEYPOT_HANDLER );
    $form             = $honeypot_handler->maybe_add_honeypot_field( $form );

    foreach ( $form['fields'] as $field ) {
        $field->set_context_property( 'rendering_form', true );
        /* @var GF_Field $field */
        $field->conditionalLogicFields = self::get_conditional_logic_fields( $form, $field->id );

        if ( is_array( $submitted_values ) ) {
            $field_value = rgar( $submitted_values, $field->id );

            if ( $field->type === 'consent'
                 && ( $field_value[ $field->id . '.3' ] != GFFormsModel::get_latest_form_revisions_id( $form['id'] )
                      || $field_value[ $field->id . '.2' ] != $field->checkboxLabel ) ) {
                $field_value = GFFormsModel::get_field_value( $field, $field_values );
            }
        } else {
            $field_value = GFFormsModel::get_field_value( $field, $field_values );
        }

        $form_string .= self::get_field( $field, $field_value, false, $form, $field_values );

        if ( $field->layoutSpacerGridColumnSpan && ! GFCommon::is_legacy_markup_enabled( $form ) ) {
            $form_string .= sprintf( '<div class="spacer gfield" style="grid-column: span %d;"></div>', $field->layoutSpacerGridColumnSpan );
        }

    }
}
$form_string .= "</{$tag}>";

if ( $has_pages ) {
    $last_page_button = rgar( $form, 'lastPageButton', array() );
    $previous_button_alt = rgar( $last_page_button, 'imageAlt', __( 'Previous Page', 'gravityforms' ) );
    $previous_button = self::get_form_button( $form['id'], "gform_previous_button_{$form['id']}", $last_page_button, __( 'Previous', 'gravityforms' ), 'gform_previous_button gform-theme-button gform-theme-button--secondary', $previous_button_alt, self::get_current_page( $form_id ) - 1 );

    /**
     * Filter through the form previous button when paged
     *
     * @param int $form_id The Form ID to filter through
     * @param string $previous_button The HTML rendered button (rendered with the form ID and the function get_form_button)
     * @param array $form The Form object to filter through
     */
    $previous_button = gf_apply_filters( array( 'gform_previous_button', $form_id ), $previous_button, $form );
    $form_string .= '</div>' . self::gform_footer( $form, 'gform_page_footer ' . rgar( $form, 'labelPlacement', 'before' ), $ajax, $field_values, $previous_button, $display_title, $display_description, $tabindex, $form_theme, $style_settings ) . '
            </div>'; //closes gform_page
}

$form_string .= '</div>'; //closes gform_body

//suppress form footer for multi-page forms (footer will be included on the last page
$label_placement = rgar( $form, 'labelPlacement', 'before' );
if ( ! $has_pages ) {
    $form_string .= self::gform_footer( $form, 'gform_footer ' . $label_placement, $ajax, $field_values, '', $display_title, $display_description, $tabindex, $form_theme, $style_settings );
}

$form_string .= '
            </form>
            ?>
            <?php