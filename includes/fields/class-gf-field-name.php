<?php /** @noinspection PhpCSValidationInspection */

// If Gravity Forms isn't loaded, bail.
if ( ! class_exists( 'GFForms' ) ) {
	die();
}

/**
 * Class GF_Field_Name
 *
 * Handles the behavior of the Name field.
 *
 * @since Unknown
 */
class GF_Field_Name extends GF_Field {

	/**
	 * Sets the field type.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @var string The type of field.
	 */
	public $type = 'name';

	/**
	 * Returns the HTML tag for the field container.
	 *
	 * @since 2.5
	 *
	 * @param array $form The current Form object.
	 *
	 * @return string
	 */
	public function get_field_container_tag( $form ) {

		if ( GFCommon::is_legacy_markup_enabled( $form ) ) {
			return parent::get_field_container_tag( $form );
		}

		return 'fieldset';

	}

	/**
	 * Sets the field title of the Name field.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @used-by GFCommon::get_field_type_title()
	 * @used-by GF_Field::get_form_editor_button()
	 *
	 * @return string
	 */
	public function get_form_editor_field_title() {
		return esc_attr__( 'Name', 'gravityforms' );
	}

	/**
	 * Returns the field's form editor description.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_form_editor_field_description() {
		return esc_attr__( 'Allows users to enter their name in the format you have specified.', 'gravityforms' );
	}

	/**
	 * Returns the field's form editor icon.
	 *
	 * This could be an icon url or a gform-icon class.
	 *
	 * @since 2.5
	 *
	 * @return string
	 */
	public function get_form_editor_field_icon() {
		return 'gform-icon--name-2';
	}

	/**
	 * Defines if conditional logic is supported by the Name field.
	 *
	 * @since Unknown
	 * @access public
	 *
	 * @used-by GFFormDetail::inline_scripts()
	 * @used-by GFFormSettings::output_field_scripts()
	 *
	 * @return bool true
	 */
	public function is_conditional_logic_supported() {
		return true;
	}

	/**
	 * Defines the IDs of required inputs.
	 *
	 * @since 2.5
	 *
	 * @return string[]
	 */
	public function get_required_inputs_ids() {
		return array( '3', '6' );
	}

	/**
	 * Validates Name field inputs.
	 *
	 * @since 1.9
	 * @since 2.6.5 Updated to use set_required_error().
	 * @access public
	 *
	 * @used-by GFFormDisplay::validate()
	 * @uses    GF_Field_Name::$isRequired
	 * @uses    GF_Field_Name::$nameFormat
	 * @uses    GF_Field_Name::get_input_property
	 * @uses    GF_Field_Name::$failed_validation
	 * @uses    GF_Field_Name::$validation_message
	 * @uses    GF_Field_Name::$errorMessage
	 *
	 * @param array|string $value The value of the field to validate. Not used here.
	 * @param array        $form  The Form Object. Not used here.
	 *
	 * @return void
	 */
	function validate( $value, $form ) {
		if ( $this->isRequired && $this->nameFormat != 'simple' ) {
			$this->set_required_error( $value, true );
		}
	}

	/**
	 * Defines the field settings available for the Name field in the form editor.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @used-by GFFormDetail::inline_scripts()
	 *
	 * @return array The field settings available.
	 */
	function get_form_editor_field_settings() {
		return array(
			'conditional_logic_field_setting',
			'prepopulate_field_setting',
			'error_message_setting',
			'label_setting',
			'admin_label_setting',
			'label_placement_setting',
			'sub_label_placement_setting',
			'default_input_values_setting',
			'input_placeholders_setting',
			'name_setting',
			'rules_setting',
			'visibility_setting',
			'description_setting',
			'css_class_setting',
			'autocomplete_setting',
		);
	}

	/**
	 * Gets the HTML markup for the field input.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @used-by GFCommon::get_field_input()
	 * @uses    GF_Field::is_entry_detail()
	 * @uses    GF_Field::is_form_editor()
	 * @uses    GF_Field_Name::$size
	 * @uses    GF_Field_Name::$id
	 * @uses    GF_Field_Name::$subLabelPlacement
	 * @uses    GF_Field_Name::$isRequired
	 * @uses    GF_Field_Name::$failed_validation
	 * @uses    GFForms::get()
	 * @uses    GFFormsModel::get_input()
	 * @uses    GFCommon::get_input_placeholder_attribute()
	 * @uses    GFCommon::get_tabindex()
	 * @uses    GFCommon::get_field_placeholder_attribute()
	 * @uses    GF_Field_Name::get_css_class()
	 *
	 * @param array      $form  The Form Object.
	 * @param string     $value The value of the field. Defaults to empty string.
	 * @param array|null $entry The Entry Object. Defaults to null.
	 *
	 * @return string The HTML markup for the field input.
	 */
	public function get_field_input( $form, $value = '', $entry = null ) {

		$is_entry_detail = $this->is_entry_detail();
		$is_form_editor  = $this->is_form_editor();
		$is_admin = $is_entry_detail || $is_form_editor;

		$form_id  = $form['id'];
		$id       = intval( $this->id );
		$field_id = $is_entry_detail || $is_form_editor || $form_id == 0 ? "input_$id" : 'input_' . $form_id . "_$id";
		$form_id  = ( $is_entry_detail || $is_form_editor ) && empty( $form_id ) ? rgget( 'id' ) : $form_id;

		$size         = $this->size;
		$class_suffix = rgget('view') == 'entry' ? '_admin' : '';
		$class        = $size . $class_suffix;
		$class        = esc_attr( $class );

		$disabled_text = $is_form_editor ? "disabled='disabled'" : '';
		$class_suffix  = $is_entry_detail ? '_admin' : '';

		$form_sub_label_placement = rgar( $form, 'subLabelPlacement' );
		$field_sub_label_placement = $this->subLabelPlacement;
		$is_sub_label_above       = $field_sub_label_placement == 'above' || ( empty( $field_sub_label_placement ) && $form_sub_label_placement == 'above' );
		$sub_label_class          = $field_sub_label_placement == 'hidden_label' ? "hidden_sub_label screen-reader-text" : '';

		$prefix = '';
		$first  = '';
		$middle = '';
		$last   = '';
		$suffix = '';

		if ( is_array( $value ) ) {
			$prefix = esc_attr( GFForms::get( $this->id . '.2', $value ) );
			$first  = esc_attr( GFForms::get( $this->id . '.3', $value ) );
			$middle = esc_attr( GFForms::get( $this->id . '.4', $value ) );
			$last   = esc_attr( GFForms::get( $this->id . '.6', $value ) );
			$suffix = esc_attr( GFForms::get( $this->id . '.8', $value ) );
		}

		$prefix_input = GFFormsModel::get_input( $this, $this->id . '.2' );
		$first_input  = GFFormsModel::get_input( $this, $this->id . '.3' );
		$middle_input = GFFormsModel::get_input( $this, $this->id . '.4' );
		$last_input   = GFFormsModel::get_input( $this, $this->id . '.6' );
		$suffix_input = GFFormsModel::get_input( $this, $this->id . '.8' );

		$first_placeholder_attribute  = GFCommon::get_input_placeholder_attribute( $first_input );
		$middle_placeholder_attribute = GFCommon::get_input_placeholder_attribute( $middle_input );
		$last_placeholder_attribute   = GFCommon::get_input_placeholder_attribute( $last_input );
		$suffix_placeholder_attribute = GFCommon::get_input_placeholder_attribute( $suffix_input );

		// ARIA labels.
		$required_attribute     = $this->isRequired ? 'aria-required="true"' : '';
		$invalid_attribute      = $this->failed_validation ? 'aria-invalid="true"' : 'aria-invalid="false"';
		$describedby_attribute  = $this->get_aria_describedby();
		$input_aria_describedby = '';


		if ( $this->nameFormat != 'simple' ) {
			// specific aria attributes for each individual input.
			$first_aria_attributes  = $this->get_aria_attributes( $value, '3');
			$middle_aria_attributes = $this->get_aria_attributes( $value, '4');
			$last_aria_attributes   = $this->get_aria_attributes( $value, '6');
			$suffix_aria_attributes = $this->get_aria_attributes( $value, '8');
		}

		$prefix_autocomplete = $this->enableAutocomplete ? $this->get_input_autocomplete_attribute( $prefix_input ) : '';
		$first_autocomplete  = $this->enableAutocomplete ? $this->get_input_autocomplete_attribute( $first_input ) : '';
		$middle_autocomplete = $this->enableAutocomplete ? $this->get_input_autocomplete_attribute( $middle_input ) : '';
		$last_autocomplete   = $this->enableAutocomplete ? $this->get_input_autocomplete_attribute( $last_input ) : '';
		$suffix_autocomplete = $this->enableAutocomplete ? $this->get_input_autocomplete_attribute( $suffix_input ) : '';

		switch ( $this->nameFormat ) {

			case 'advanced' :
			case 'extended' :
				$prefix_tabindex = GFCommon::get_tabindex();
				$first_tabindex  = GFCommon::get_tabindex();
				$middle_tabindex = GFCommon::get_tabindex();
				$last_tabindex   = GFCommon::get_tabindex();
				$suffix_tabindex = GFCommon::get_tabindex();

				$prefix_sub_label      = rgar( $prefix_input, 'customLabel' ) != '' ? $prefix_input['customLabel'] : gf_apply_filters( array( 'gform_name_prefix', $form_id ), esc_html__( 'Prefix', 'gravityforms' ), $form_id );
				$first_name_sub_label  = rgar( $first_input, 'customLabel' ) != '' ? $first_input['customLabel'] : gf_apply_filters( array( 'gform_name_first', $form_id ), esc_html__( 'First', 'gravityforms' ), $form_id );
				$middle_name_sub_label = rgar( $middle_input, 'customLabel' ) != '' ? $middle_input['customLabel'] : gf_apply_filters( array( 'gform_name_middle', $form_id ), esc_html__( 'Middle', 'gravityforms' ), $form_id );
				$last_name_sub_label   = rgar( $last_input, 'customLabel' ) != '' ? $last_input['customLabel'] : gf_apply_filters( array( 'gform_name_last', $form_id ), esc_html__( 'Last', 'gravityforms' ), $form_id );
				$suffix_sub_label      = rgar( $suffix_input, 'customLabel' ) != '' ? $suffix_input['customLabel'] : gf_apply_filters( array( 'gform_name_suffix', $form_id ), esc_html__( 'Suffix', 'gravityforms' ), $form_id );

				$prefix_markup = '';
				$first_markup  = '';
				$middle_markup = '';
				$last_markup   = '';
				$suffix_markup = '';

				if ( $is_sub_label_above ) {

					$style = ( $is_admin && rgar( $prefix_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $prefix_input, 'isHidden' ) ) {
						$prefix_select_class = isset( $prefix_input['choices'] ) && is_array( $prefix_input['choices'] ) ? 'name_prefix_select' : '';
						$prefix_markup       = self::get_name_prefix_field( $prefix_input, $id, $field_id, $prefix, $disabled_text, $prefix_tabindex );
						$prefix_markup       = "<span id='{$field_id}_2_container' class='name_prefix {$prefix_select_class} gform-grid-col gform-grid-col--size-auto' {$style}>
                                                    <label for='{$field_id}_2' class='gform-field-label gform-field-label--type-sub {$sub_label_class}'>{$prefix_sub_label}</label>
                                                    {$prefix_markup}
                                                  </span>";
					}

					$style = ( $is_admin && rgar( $first_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $first_input, 'isHidden' ) ) {
						$first_markup = "<span id='{$field_id}_3_container' class='name_first gform-grid-col gform-grid-col--size-auto' {$style}>
                                                    <label for='{$field_id}_3' class='gform-field-label gform-field-label--type-sub {$sub_label_class}'>{$first_name_sub_label}</label>
                                                    <input type='text' name='input_{$id}.3' id='{$field_id}_3' value='{$first}' {$first_tabindex} {$disabled_text} {$first_aria_attributes} {$first_placeholder_attribute} {$first_autocomplete} {$this->maybe_add_aria_describedby( $first_input, $field_id, $this['formId'] )}/>
                                                </span>";
					}

					$style = ( $is_admin && ( ! isset( $middle_input['isHidden'] ) || rgar( $middle_input, 'isHidden' ) ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ( isset( $middle_input['isHidden'] ) && $middle_input['isHidden'] == false ) ) {
						$middle_markup = "<span id='{$field_id}_4_container' class='name_middle gform-grid-col gform-grid-col--size-auto' {$style}>
                                                    <label for='{$field_id}_4' class='gform-field-label gform-field-label--type-sub {$sub_label_class}'>{$middle_name_sub_label}</label>
                                                    <input type='text' name='input_{$id}.4' id='{$field_id}_4' value='{$middle}' {$middle_tabindex} {$disabled_text} {$middle_aria_attributes} {$middle_placeholder_attribute} {$middle_autocomplete} {$this->maybe_add_aria_describedby( $middle_input, $field_id, $this['formId'] )}/>
                                                </span>";
					}

					$style = ( $is_admin && rgar( $last_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $last_input, 'isHidden' ) ) {
						$last_markup = "<span id='{$field_id}_6_container' class='name_last gform-grid-col gform-grid-col--size-auto' {$style}>
                                                            <label for='{$field_id}_6' class='gform-field-label gform-field-label--type-sub {$sub_label_class}'>{$last_name_sub_label}</label>
                                                            <input type='text' name='input_{$id}.6' id='{$field_id}_6' value='{$last}' {$last_tabindex} {$disabled_text} {$last_aria_attributes} {$last_placeholder_attribute} {$last_autocomplete} {$this->maybe_add_aria_describedby( $last_input, $field_id, $this['formId'] )}/>
                                                        </span>";
					}

					$style = ( $is_admin && rgar( $suffix_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $suffix_input, 'isHidden' ) ) {
						$suffix_select_class = isset( $suffix_input['choices'] ) && is_array( $suffix_input['choices'] ) ? 'name_suffix_select' : '';
						$suffix_markup       = "<span id='{$field_id}_8_container' class='name_suffix {$suffix_select_class} gform-grid-col gform-grid-col--size-auto' {$style}>
                                                        <label for='{$field_id}_8' class='gform-field-label gform-field-label--type-sub {$sub_label_class}'>{$suffix_sub_label}</label>
                                                        <input type='text' name='input_{$id}.8' id='{$field_id}_8' value='{$suffix}' {$suffix_tabindex} {$disabled_text} {$suffix_aria_attributes} {$suffix_placeholder_attribute} {$suffix_autocomplete} {$this->maybe_add_aria_describedby( $suffix_input, $field_id, $this['formId'] )}/>
                                                    </span>";
					}
				} else {
					$style = ( $is_admin && rgar( $prefix_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $prefix_input, 'isHidden' ) ) {
						$prefix_select_class = isset( $prefix_input['choices'] ) && is_array( $prefix_input['choices'] ) ? 'name_prefix_select' : '';
						$prefix_markup       = self::get_name_prefix_field( $prefix_input, $id, $field_id, $prefix, $disabled_text, $prefix_tabindex );
						$prefix_markup       = "<span id='{$field_id}_2_container' class='name_prefix {$prefix_select_class} gform-grid-col gform-grid-col--size-auto' {$style}>
                                                    {$prefix_markup}
                                                    <label for='{$field_id}_2' class='gform-field-label gform-field-label--type-sub {$sub_label_class}'>{$prefix_sub_label}</label>
                                                  </span>";
					}

					$style = ( $is_admin && rgar( $first_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $first_input, 'isHidden' ) ) {
						$first_markup = "<span id='{$field_id}_3_container' class='name_first gform-grid-col gform-grid-col--size-auto shyamalan' {$style}>
                                                    <input type='text' name='input_{$id}.3' id='{$field_id}_3' value='{$first}' {$first_tabindex} {$disabled_text} {$first_aria_attributes} {$first_placeholder_attribute} {$first_autocomplete} {$this->maybe_add_aria_describedby( $first_input, $field_id, $this['formId'] )}/>
                                                    <label for='{$field_id}_3' class='gform-field-label gform-field-label--type-sub {$sub_label_class}'>{$first_name_sub_label}</label>
                                                </span>";
					}

					$style = ( $is_admin && ( ! isset( $middle_input['isHidden'] ) || rgar( $middle_input, 'isHidden' ) ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ( isset( $middle_input['isHidden'] ) && $middle_input['isHidden'] == false ) ) {
						$middle_markup = "<span id='{$field_id}_4_container' class='name_middle gform-grid-col gform-grid-col--size-auto' {$style}>
                                                    <input type='text' name='input_{$id}.4' id='{$field_id}_4' value='{$middle}' {$middle_tabindex} {$disabled_text} {$middle_aria_attributes} {$middle_placeholder_attribute} {$middle_autocomplete} {$this->maybe_add_aria_describedby( $middle_input, $field_id, $this['formId'] )}/>
                                                    <label for='{$field_id}_4' class='gform-field-label gform-field-label--type-sub {$sub_label_class}'>{$middle_name_sub_label}</label>
                                                </span>";
					}

					$style = ( $is_admin && rgar( $last_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $last_input, 'isHidden' ) ) {
						$last_markup = "<span id='{$field_id}_6_container' class='name_last gform-grid-col gform-grid-col--size-auto' {$style}>
                                                    <input type='text' name='input_{$id}.6' id='{$field_id}_6' value='{$last}' {$last_tabindex} {$disabled_text} {$last_aria_attributes} {$last_placeholder_attribute} {$last_autocomplete} {$this->maybe_add_aria_describedby( $last_input, $field_id, $this['formId'] )}/>
                                                    <label for='{$field_id}_6' class='gform-field-label gform-field-label--type-sub {$sub_label_class}'>{$last_name_sub_label}</label>
                                                </span>";
					}

					$style = ( $is_admin && rgar( $suffix_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $suffix_input, 'isHidden' ) ) {
						$suffix_select_class = isset( $suffix_input['choices'] ) && is_array( $suffix_input['choices'] ) ? 'name_suffix_select' : '';
						$suffix_markup       = "<span id='{$field_id}_8_container' class='name_suffix {$suffix_select_class} gform-grid-col gform-grid-col--size-auto' {$style}>
                                                    <input type='text' name='input_{$id}.8' id='{$field_id}_8' value='{$suffix}' {$suffix_tabindex} {$disabled_text} {$suffix_aria_attributes} {$suffix_placeholder_attribute} {$suffix_autocomplete} {$this->maybe_add_aria_describedby( $suffix_input, $field_id, $this['formId'] )}/>
                                                    <label for='{$field_id}_8' class='gform-field-label gform-field-label--type-sub {$sub_label_class}'>{$suffix_sub_label}</label>
                                                </span>";
					}
				}
				$css_class = $this->get_css_class();


				return "<div class='ginput_complex{$class_suffix} ginput_container ginput_container--name {$css_class} gform-grid-row' id='{$field_id}'>
                            {$prefix_markup}
                            {$first_markup}
                            {$middle_markup}
                            {$last_markup}
                            {$suffix_markup}
                        </div>";
			case 'simple' :
				$value                 = esc_attr( $value );
				$class                 = esc_attr( $class );
				$tabindex              = GFCommon::get_tabindex();
				$placeholder_attribute = GFCommon::get_field_placeholder_attribute( $this );

				return "<div class='ginput_container ginput_container_name'>
                                    <input name='input_{$id}' id='{$field_id}' type='text' value='{$value}' class='{$class}' {$tabindex} {$disabled_text} {$required_attribute} {$invalid_attribute} {$describedby_attribute} {$placeholder_attribute} />
                                </div>";
			default :
				$first_tabindex       = GFCommon::get_tabindex();
				$last_tabindex        = GFCommon::get_tabindex();
				$first_name_sub_label = rgar( $first_input, 'customLabel' ) != '' ? $first_input['customLabel'] : gf_apply_filters( array( 'gform_name_first', $form_id ), esc_html__( 'First', 'gravityforms' ), $form_id );
				$last_name_sub_label  = rgar( $last_input, 'customLabel' ) != '' ? $last_input['customLabel'] : gf_apply_filters( array( 'gform_name_last', $form_id ), esc_html__( 'Last', 'gravityforms' ), $form_id );
				if ( $is_sub_label_above ) {
					$first_markup = '';
					$style        = ( $is_admin && rgar( $first_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $first_input, 'isHidden' ) ) {
						$first_markup = "<span id='{$field_id}_3_container' class='name_first gform-grid-col' {$style}>
                                                    <label for='{$field_id}_3' class='gform-field-label gform-field-label--type-sub {$sub_label_class}'>{$first_name_sub_label}</label>
                                                    <input type='text' name='input_{$id}.3' id='{$field_id}_3' value='{$first}' {$first_tabindex} {$disabled_text} {$first_aria_attributes} {$first_placeholder_attribute} />
                                                </span>";
					}

					$last_markup = '';
					$style       = ( $is_admin && rgar( $last_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $last_input, 'isHidden' ) ) {
						$last_markup = "<span id='{$field_id}_6_container' class='name_last gform-grid-col' {$style}>
                                                <label for='{$field_id}_6' class='gform-field-label gform-field-label--type-sub {$sub_label_class}'>" . $last_name_sub_label . "</label>
                                                <input type='text' name='input_{$id}.6' id='{$field_id}_6' value='{$last}' {$last_tabindex} {$disabled_text} {$last_aria_attributes} {$last_placeholder_attribute} />
                                            </span>";
					}
				} else {
					$first_markup = '';
					$style        = ( $is_admin && rgar( $first_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $first_input, 'isHidden' ) ) {
						$first_markup = "<span id='{$field_id}_3_container' class='name_first gform-grid-col' {$style}>
                                                    <input type='text' name='input_{$id}.3' id='{$field_id}_3' value='{$first}' {$first_tabindex} {$disabled_text} {$first_aria_attributes} {$first_placeholder_attribute} />
                                                    <label for='{$field_id}_3' class='gform-field-label gform-field-label--type-sub {$sub_label_class}'>{$first_name_sub_label}</label>
                                               </span>";
					}

					$last_markup = '';
					$style       = ( $is_admin && rgar( $last_input, 'isHidden' ) ) ? "style='display:none;'" : '';
					if ( $is_admin || ! rgar( $last_input, 'isHidden' ) ) {
						$last_markup = "<span id='{$field_id}_6_container' class='name_last gform-grid-col' {$style}>
                                                    <input type='text' name='input_{$id}.6' id='{$field_id}_6' value='{$last}' {$last_tabindex} {$disabled_text} {$last_aria_attributes} {$last_placeholder_attribute} />
                                                    <label for='{$field_id}_6' class='gform-field-label gform-field-label--type-sub {$sub_label_class}'>{$last_name_sub_label}</label>
                                                </span>";
					}
				}

				$css_class = $this->get_css_class();

				return "<div class='ginput_complex{$class_suffix} ginput_container ginput_container--name {$css_class} gform-grid-row' id='{$field_id}'>
                            {$first_markup}
                            {$last_markup}
                            <div class='gf_clear gf_clear_complex'></div>
                        </div>";
		}
	}

	/**
	 * Sets the CSS class to be used by the field input.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @used-by GF_Field_Name::get_field_input()
	 * @uses    GFFormsModel::get_input()
	 *
	 * @return string The CSS class to use for the field.
	 */
	public function get_css_class() {

		$prefix_input = GFFormsModel::get_input( $this, $this->id . '.2' );
		$first_input  = GFFormsModel::get_input( $this, $this->id . '.3' );
		$middle_input = GFFormsModel::get_input( $this, $this->id . '.4' );
		$last_input   = GFFormsModel::get_input( $this, $this->id . '.6' );
		$suffix_input = GFFormsModel::get_input( $this, $this->id . '.8' );

		$css_class = '';
		$visible_input_count = 0;

		if ( $prefix_input && ! rgar( $prefix_input, 'isHidden' ) ) {
			$visible_input_count++;
			$css_class .= 'has_prefix ';
		} else {
			$css_class .= 'no_prefix ';
		}

		if ( $first_input && ! rgar( $first_input, 'isHidden' ) ) {
			$visible_input_count++;
			$css_class .= 'has_first_name ';
		} else {
			$css_class .= 'no_first_name ';
		}

		if ( $middle_input && ! rgar( $middle_input, 'isHidden' ) ) {
			$visible_input_count++;
			$css_class .= 'has_middle_name ';
		} else {
			$css_class .= 'no_middle_name ';
		}

		if ( $last_input && ! rgar( $last_input, 'isHidden' ) ) {
			$visible_input_count++;
			$css_class .= 'has_last_name ';
		} else {
			$css_class .= 'no_last_name ';
		}

		if ( $suffix_input && ! rgar( $suffix_input, 'isHidden' ) ) {
			$visible_input_count++;
			$css_class .= 'has_suffix ';
		} else {
			$css_class .= 'no_suffix ';
		}

		$css_class .= "gf_name_has_{$visible_input_count} ginput_container_name ";

		return trim( $css_class );
	}

	/**
	 * Defines the field markup to be used for the name prefix.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @used-by GF_Field_Name::get_field_input()
	 * @uses    GFCommon::get_input_placeholder_value()
	 * @uses    GFCommon::get_input_placeholder_attribute()
	 *
	 * @param array  $input         The input item choices.
	 * @param int    $id            The ID of the name field.
	 * @param int    $field_id      The field ID of the name field.
	 * @param string $value         The value to be used in the prefix field item.
	 * @param string $disabled_text The text to be used if the prefix field item is disabled.
	 * @param int    $tabindex      The tab index of the prefix field item.
	 *
	 * @return string The field HTML markup.
	 */
	public function get_name_prefix_field( $input, $id, $field_id, $value, $disabled_text, $tabindex ) {

		$autocomplete          = $this->enableAutocomplete ? $this->get_input_autocomplete_attribute( $input ) : '';
		$aria_attributes       = $this->get_aria_attributes( array( $input['id'] => $value ), '2' );
		$describedby_attribute = $this->get_aria_describedby();

		if ( isset( $input['choices'] ) && is_array( $input['choices'] ) ) {
			$placeholder_value = GFCommon::get_input_placeholder_value( $input );
			$options           = "<option value=''>{$placeholder_value}</option>";
			$value_enabled     = rgar( $input, 'enableChoiceValue' );
			foreach ( $input['choices'] as $choice ) {
				$choice_value            = $value_enabled ? $choice['value'] : $choice['text'];
				$is_selected_by_default  = rgar( $choice, 'isSelected' );
				$is_this_choice_selected = empty( $value ) ? $is_selected_by_default : strtolower( $choice_value ) == strtolower( $value );
				$selected                = $is_this_choice_selected ? "selected='selected'" : '';
				$options .= "<option value='{$choice_value}' {$selected}>{$choice['text']}</option>";
			}

			$markup = "<select name='input_{$id}.2' id='{$field_id}_2' {$tabindex} {$disabled_text} {$autocomplete} {$aria_attributes} {$this->maybe_add_aria_describedby( $input, $field_id, $this['formId'] )}>
                          {$options}
                      </select>";

		} else {
			$placeholder_attribute = GFCommon::get_input_placeholder_attribute( $input );

			$markup = "<input type='text' name='input_{$id}.2' id='{$field_id}_2' value='{$value}' {$tabindex} {$disabled_text} {$placeholder_attribute} {$autocomplete} {$this->maybe_add_aria_describedby( $input, $field_id, $this['formId'] )}/>";
		}

		return $markup;
	}

	/**
	 * Gets the field value to be displayed on the entry detail page.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @used-by GFCommon::get_lead_field_display()
	 * @uses    GF_Field_Name::$id
	 *
	 * @param array|string $value    The value of the field input.
	 * @param string       $currency Not used.
	 * @param bool         $use_text Not used.
	 * @param string       $format   The format to output the value. Defaults to 'html'.
	 * @param string       $media    Not used.
	 *
	 * @return array|string The value to be displayed on the entry detail page.
	 */
	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {

		if ( is_array( $value ) ) {
			$prefix = trim( rgget( $this->id . '.2', $value ) );
			$first  = trim( rgget( $this->id . '.3', $value ) );
			$middle = trim( rgget( $this->id . '.4', $value ) );
			$last   = trim( rgget( $this->id . '.6', $value ) );
			$suffix = trim( rgget( $this->id . '.8', $value ) );

			$name = $prefix;
			$name .= ! empty( $name ) && ! empty( $first ) ? " $first" : $first;
			$name .= ! empty( $name ) && ! empty( $middle ) ? " $middle" : $middle;
			$name .= ! empty( $name ) && ! empty( $last ) ? " $last" : $last;
			$name .= ! empty( $name ) && ! empty( $suffix ) ? " $suffix" : $suffix;

			$return = $name;
		} else {
			$return = $value;
		}

		if ( $format === 'html' ) {
			$return = esc_html( $return );
		}
		return $return;
	}

	/**
	 * Sanitizes the field settings choices.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @used-by GFFormDetail::add_field()
	 * @used-by GFFormsModel::sanitize_settings()
	 * @uses    GF_Field::sanitize_settings()
	 * @uses    GF_Field::sanitize_settings_choices()
	 *
	 * @return void
	 */
	public function sanitize_settings() {
		parent::sanitize_settings();
		if ( is_array( $this->inputs ) ) {
			foreach ( $this->inputs as &$input ) {
				if ( isset ( $input['choices'] ) && is_array( $input['choices'] ) ) {
					$input['choices'] = $this->sanitize_settings_choices( $input['choices'] );
				}
			}
		}
	}

	/**
	 * Gets the field value to be used when exporting.
	 *
	 * @since  Unknown
	 * @access public
	 *
	 * @used-by GFExport::start_export()
	 * @used-by GFAddOn::get_field_value()
	 * @used-by GFAddOn::get_full_name()
	 *
	 * @param array  $entry    The Entry Object.
	 * @param string $input_id The input ID to format. Defaults to empty string. If not set, uses t
	 * @param bool   $use_text Not used.
	 * @param bool   $is_csv   Not used.
	 *
	 * @return string The field value.
	 */
	public function get_value_export( $entry, $input_id = '', $use_text = false, $is_csv = false ) {
		if ( empty( $input_id ) ) {
			$input_id = $this->id;
		}

		if ( absint( $input_id ) == $input_id ) {
			// If field is simple (one input), simply return full content.
			$name = rgar( $entry, $input_id );
			if ( ! empty( $name ) ) {
				return $name;
			}

			// Complex field (multiple inputs). Join all pieces and create name.
			$prefix = trim( rgar( $entry, $input_id . '.2' ) );
			$first  = trim( rgar( $entry, $input_id . '.3' ) );
			$middle = trim( rgar( $entry, $input_id . '.4' ) );
			$last   = trim( rgar( $entry, $input_id . '.6' ) );
			$suffix = trim( rgar( $entry, $input_id . '.8' ) );

			$name = $prefix;
			$name .= ! empty( $name ) && ! empty( $first ) ? ' ' . $first : $first;
			$name .= ! empty( $name ) && ! empty( $middle ) ? ' ' . $middle : $middle;
			$name .= ! empty( $name ) && ! empty( $last ) ? ' ' . $last : $last;
			$name .= ! empty( $name ) && ! empty( $suffix ) ? ' ' . $suffix : $suffix;

			return $name;
		} else {

			return rgar( $entry, $input_id );
		}
	}

	/**
	 * Removes the "for" attribute in the field label. Inputs are only allowed one label (a11y) and the inputs already have labels.
	 *
	 * @since  2.4
	 * @access public
	 *
	 * @param array $form The Form Object currently being processed.
	 *
	 * @return string
	 */
	public function get_first_input_id( $form ) {
		return '';
	}

	// # FIELD FILTER UI HELPERS ---------------------------------------------------------------------------------------

	/**
	 * Returns the sub-filters for the current field.
	 *
	 * @since 2.4
	 *
	 * @return array
	 */
	public function get_filter_sub_filters() {
		$sub_filters = array();

		if ( $this->nameFormat == 'simple' ) {
			return $sub_filters;
		}

		$inputs = $this->inputs;

		foreach ( (array) $inputs as $input ) {
			if ( rgar( $input, 'isHidden' ) ) {
				continue;
			}

			$sub_filters[] = array(
				'key'             => rgar( $input, 'id' ),
				'text'            => rgar( $input, 'customLabel', rgar( $input, 'label' ) ),
				'preventMultiple' => false,
				'operators'       => $this->get_filter_operators(),
			);
		}

		return $sub_filters;
	}

	/**
	 * Returns the filter operators for the current field.
	 *
	 * @since 2.4
	 *
	 * @return array
	 */
	public function get_filter_operators() {
		$operators   = parent::get_filter_operators();
		$operators[] = 'contains';

		return $operators;
	}
}

// Registers the Name field with the field framework.
GF_Fields::register( new GF_Field_Name() );
