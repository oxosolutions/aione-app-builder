<?php

/**
 *
 * Returns HTML formatted output for elements and handles form submission.
 *
 *
 *
 * @version 1.0
 */
class Enlimbo_Forms_Aione
{

    /**
     * @var string
     */
    private $_id;

    /**
     * @var array
     */
    private $_errors = array();

    /**
     * @var array
     */
    private $_elements = array();

    /**
     * Failed elements.
     * 
     * @var type 
     */
    private $_elements_not_valid = array();

    /**
     * @var array
     */
    private $_count = array();

    /**
     * @var string
     */
    public $css_class = 'aione-form';

    /**
     * Auto handler
     *
     * Renders.
     *
     * @param array $element
     * @return HTML formatted output
     */
    public function autoHandle( $id, $form )
    {
        // Auto-add wpnonce field
        $form['_wpnonce'] = array(
            '#type' => 'markup',
            '#markup' => wp_nonce_field( $id, '_wpnonce_aione', true, false )
        );

        $this->_id = $id;
        $this->_elements = $form;

        do_action( 'aione_form_autohandle', $id, $form, $this );
        do_action( 'aione_form_autohandle_' . $id, $form, $this );

        // get submitted data
        if ( $this->isSubmitted() ) {

            do_action( 'aione_form_autohandle_submit', $id, $form, $this );
            do_action( 'aione_form_autohandle_submit_' . $id, $form, $this );

            // check if errors (validation)
            $this->validate( $this->_elements );

            do_action( 'aione_form_autohandle_validate', $id, $form, $this );
            do_action( 'aione_form_autohandle_validate_' . $id, $form, $this );

            // callback
            if ( empty( $this->_errors ) ) {

                if ( isset( $form['#form']['callback'] ) ) {
                    if ( is_array( $form['#form']['callback'] ) ) {
                        foreach ( $form['#form']['callback'] as $callback ) {
                            if ( is_callable( $callback ) ) {
                                call_user_func( $callback, $this );
                            }
                        }
                    } else {
                        if ( is_callable( $form['#form']['callback'] ) ) {
                            call_user_func( $form['#form']['callback'], $this );
                        }
                    }
                }
                // Maybe triggered by callback function
                if ( empty( $this->_errors ) ) {
                    // redirect
                    do_action( 'aione_form_autohandle_redirection', $id, $form,
                            $this );
                    do_action( 'aione_form_autohandle_redirection_' . $id, $form,
                            $this );
                    if( ! headers_sent() ) {
                        if ( !isset( $form['#form']['redirection'] ) ) {
                            header( 'Location: ' . $_SERVER['REQUEST_URI'] );
                        } else if ( $form['#form']['redirection'] != false ) {
                            header( 'Location: ' . $form['#form']['redirection'] );
                        }
                    }
                }
            }
        }
    }

    /**
     * Checks if form is submitted.
     * 
     * @param type $id
     * @return type 
     */
    public function isSubmitted( $id = '' )
    {
        if ( empty( $id ) ) {
            $id = $this->_id;
        }
        return (
            isset( $_REQUEST['_wpnonce_aione'] )
            && wp_verify_nonce( $_REQUEST['_wpnonce_aione'], $id )
        );
    }

    /**
     * Loops over elements and validates them.
     * 
     * @param type $elements 
     */
    public function validate( &$elements )
    {
        require_once AIONE_DIR_PATH . 'admin/form/validate.php';
        foreach ( $elements as $key => &$element ) {
            if ( !isset( $element['#type'] )
                    || !$this->_isValidType( $element['#type'] ) ) {
                continue;
            }
            if ( $element['#type'] != 'fieldset' ) {
                if ( isset( $element['#name'] )
                        && !in_array( $element['#type'],
                                array('submit', 'reset', 'button') ) ) {
                    // Set submitted data
                    if ( !in_array( $element['#type'], array('checkboxes') )
                            && empty( $element['#forced_value'] ) ) {
                        $element['#value'] = $this->getSubmittedData( $element );
                    } else if ( !empty( $element['#options'] )
                            && empty( $element['#forced_value'] ) ) {
                        foreach ( $element['#options'] as $option_key => $option ) {
                            $option['#type'] = 'checkbox';
                            $element['#options'][$option_key]['#value'] = $this->getSubmittedData( $option );
                        }
                    }
                }
                // Validate
                if ( isset( $element['#validate'] ) ) {
                    $this->validateElement( $element );
                }
            } else if ( isset( $element['#type'] )
                    && $element['#type'] == 'fieldset' ) {
                $this->validate( $element );
            } else if ( is_array( $element ) ) {
                $this->validate( $element );
            }
        }
    }

    /**
     * Validates element.
     * 
     * @param type $element 
     */
    public function validateElement( &$element )
    {
        $check = Aione_Validate::check( $element['#validate'], $element['#value'] );
        if ( isset( $check['error'] ) ) {
            $this->_errors = true;
            $element['#error'] = $check['message'];
            if ( isset( $element['aione-id'] ) ) {
                $this->_elements_not_valid[$element['aione-id']] = $element;
            }
        }
    }
    
    /**
     * Returns not valid elements.
     * 
     * @return type
     */
    public function get_not_valid() {
        return $this->_elements_not_valid;
    }

    /**
     * Checks if there are errors.
     * 
     * @return type 
     */
    public function isError()
    {
        return $this->_errors;
    }

    /**
     * Sets errors to true.
     */
    public function triggerError()
    {
        $this->_errors = true;
    }

    /**
     * Renders form.
     */
    public function renderForm()
    {
        // loop over elements and render them
        return $this->renderElements( $this->_elements );
    }

    /**
     * Counts element types.
     * 
     * @param type $type
     * @return type 
     */
    private function _count( $type ) {
        if ( !isset( $this->_count[$type] ) ) {
            $this->_count[$type] = 0;
        }
        $this->_count[$type] += 1;
        return $this->_count[$type];
    }

    /**
     * Check if element is of valid type
     *
     * @param string $type
     * @return boolean
     */
    private function _isValidType( $type )
    {
        return in_array(
            $type,
            array(
                'button',
                'checkbox',
                'checkboxes',
                'fieldset',
                'file',
                'hidden',
                'markup',
                'radio',
                'radios',
                'reset',
                'select',
                'submit',
                'textarea',
                'textfield',
                'thumbnail',
                'notice',
            )
        );
    }

    /**
     * Renders elements.
     * 
     * @param type $elements
     * @return type 
     */
    public function renderElements( $elements )
    {
        $output = '';
        foreach ( $elements as $key => $element ) {
            if ( !isset( $element['#type'] )
                    || !$this->_isValidType( $element['#type'] ) ) {
                continue;
            }
            if ( $element['#type'] != 'fieldset' ) {
                $output .= $this->renderElement( $element );
            } else if ( isset( $element['#type'] )
                    && $element['#type'] == 'fieldset' ) {
                $buffer = $this->renderElements( $element );
                $output .= $this->fieldset( $element, 'wrap', $buffer );
            } else if ( is_array( $element ) ) {
                $output .= $this->renderElements( $element );
            }
        }
        return $output;
    }

    /**
     * Renders element.
     *
     * Depending on element type, it calls class methods.
     *
     * @param array $element
     * @return HTML formatted output
     */
    public function renderElement( $element )
    {
        $method = $element['#type'];
        if ( !isset( $element['#name'] ) && !in_array($element['#type'], array('notice', 'markup') )) {
            if ( !isset( $element['#attributes']['name'] ) ) {
                return '#name or #attributes[\'name\'] required!';
            } else {
                $element['#name'] = $element['#attributes']['name'];
            }
        }
        if ( is_callable( array($this, $method) ) ) {
            if ( !isset( $element['#id'] ) ) {
                if ( isset( $element['#attributes']['id'] ) ) {
                    $element['#id'] = $element['#attributes']['id'];
                } elseif ( isset($element['#name']) ) {
                    $element['#id'] = sprintf('aione-%s-%s', $element['#type'], md5($element['#name']));
                } else {
                    $element['#id'] = $element['#type'] . '-' . $this->_count( $element['#type'] );
                }
            }
            if ( isset( $this->_errors[$element['#id']] ) ) {
                $element['#error'] = $this->_errors[$element['#id']];
            }
            // Add JS validation
            if ( !empty( $element['#validate'] ) ) {
                aione_form_add_js_validation( $element );
            }
            return $this->{$method}( $element );
        }
    }

    /**
     * Sets other element attributes.
     *
     * @param array $element
     * @return string
     */
    private function _setElementAttributes( $element )
    {
        /**
         * sanitize #attributes type
         */
        if (
            !isset( $element['#attributes'] )
            || !is_array($element['#attributes'])
        ) {
            $element['#attributes'] = array();
        }
        $attributes = '';
        $error_class = isset( $element['#error'] ) ? ' ' . $this->css_class . '-error ' . $this->css_class . '-' . $element['#type'] . '-error ' . ' form-' . $element['#type'] . '-error ' . $element['#type'] . '-error form-error ' : '';
        $class = $this->css_class . '-' . $element['#type'] . ' form-' . $element['#type'] . ' ' . $element['#type'];
        foreach ( $element['#attributes'] as $attribute => $value ) {
            // Prevent undesired elements
            if ( in_array( $attribute, array('id', 'name') ) ) {
                continue;
            }
            // Append class values
            if ( $attribute == 'class' ) {
                $value = $value . ' ' . $class . $error_class;
            }
            // Set return string
            $attributes .= ' ' . $attribute . '="' . $value . '"';
        }
        if ( !isset( $element['#attributes']['class'] ) ) {
            $attributes .= ' class="' . $class . $error_class . '"';
        }
        /**
         * disable if is setup #disable
         */
        if (
            !in_array('disabled', $element['#attributes'])
            && isset( $element['#disable'] )
            && $element['#disable']
        ) {
            $attributes .= ' disabled="disabled"';
        }
        /**
         * disable if is setup #disable
         */
        if (
            !in_array('readonly', $element['#attributes'])
            && isset( $element['#disable'] )
            && $element['#disable']
        ) {
            $attributes .= ' readonly="readonly"';
        }
        return $attributes;
    }

    /**
     * Sets render elements.
     *
     * @param array $element
     */
    private function _setRender( $element )
    {
        if ( !isset( $element['#id'] ) ) {
            if ( isset( $element['#attributes']['id'] ) ) {
                $element['#id'] = $element['#attributes']['id'];
            } else {
                $element['#id'] = 'form-' . md5( serialize( $element ) ) . '-'
                        . $this->_count( $element['#type'] );
            }
        }
        $element['_attributes_string'] = $this->_setElementAttributes( $element );
        $element['_render'] = array();
        $element['_render']['prefix'] = isset( $element['#prefix'] ) ? $element['#prefix'] . "\r\n" : '';
        $element['_render']['suffix'] = isset( $element['#suffix'] ) ? $element['#suffix'] . "\r\n" : '';
        $element['_render']['before'] = isset( $element['#before'] ) ? $element['#before'] . "\r\n" : '';
        $element['_render']['after'] = isset( $element['#after'] ) ? $element['#after'] . "\r\n" : '';
        /**
         * label
         */
        $element['_render']['label'] = $lebel = '';
        if (isset($element['#label'])) {
            $label = $element['#label'];
        } else if (isset($element['#title'])) {
            $label = $element['#title'];
        }
        if ( !empty($label) ) {
            /**
             * add tooltip
             */
            if ( isset( $element['#attributes']['tooltip'] ) ) {
                $label .= sprintf(
                    ' <i class="js-aione-tooltip aione-tooltip dashicons dashicons-editor-help" data-tooltip="%s"></i>',
                    esc_attr($element['#attributes']['tooltip'])
                );
            }
            $element['_render']['label'] = sprintf(
                '<label class="%s-label %s-%s-label" for="%s">%s</label>',
                esc_attr($this->css_class),
                esc_attr($this->css_class),
                esc_attr($element['#type']),
                esc_attr($element['#id']),
                stripslashes( $label )
            );
        }
        /**
         * title
         */
        $element['_render']['title'] = $this->_setElementTitle( $element );
        $element['_render']['description'] = !empty( $element['#description'] ) ? $this->_setElementDescription( $element ) : '';
        $element['_render']['error'] = $this->renderError( $element ) . "\r\n";

        return $element;
    }

    /**
     * Applies pattern to output.
     *
     * Pass element property #pattern to get custom renedered element.
     *
     * @param array $pattern
     *      Accepts: <prefix><suffix><label><title><desription><error>
     * @param array $element
     */
    private function _pattern( $pattern, $element )
    {
        foreach ( $element['_render'] as $key => $value ) {
            $pattern = str_replace( '<' . strtoupper( $key ) . '>', $value, $pattern );
        }
        /**
         * clear unreplaced placeholders
         */
        $placeholders = array(
            'AFTER',
            'BEFORE',
            'DESCRIPTION',
            'ELEMENT',
            'ERROR',
            'LABEL',
            'PREFIX',
            'SUFFIX',
            'TITLE',
        );
        $re = sprintf('/<(%s)>/', implode('|', $placeholders));
        return preg_replace( $re, '', $pattern);
    }

    /**
     * Wrapps element in <div></div>.
     *
     * @param arrat $element
     * @param string $output
     * @return string
     */
    private function _wrapElement( $element, $output )
    {
        if ( empty( $element['#inline'] ) ) {
            $wrapped = '<div id="' . $element['#id'] . '-wrapper"'
                    . ' class="form-item form-item-' . $element['#type'] . ' '
                    . $this->css_class . '-item '
                    . $this->css_class . '-item-' . $element['#type']
                    . '">' . $output . '</div>';
            return $wrapped;
        }
        return $output;
    }

    /**
     * Returns HTML formatted output for element's title.
     *
     * @param string $element
     * @return string
     */
    private function _setElementTitle( $element )
    {
        $output = '';
        if ( isset( $element['#title'] ) ) {
            $output .= '<div class="title '
                    . $this->css_class . '-title '
                    . $this->css_class . '-title-' . $element['#type'] . ' '
                    . 'title-' . $element['#type'] . '">'
                    . stripslashes( $element['#title'] )
                    . "</div>\r\n";
        }
        return $output;
    }

    /**
     * Returns HTML formatted output for element's description.
     *
     * @param array $element
     * @return string
     */
    private function _setElementDescription( $element )
    {
        $element['#description'] = stripslashes( $element['#description'] );
        $output = "\r\n"
                . '<p class="description '
                . $this->css_class . '-description '
                . $this->css_class . '-description-' . $element['#type'] . ' '
                . 'description-' . $element['#type'] . '">'
                . $element['#description'] . "</p>\r\n";
        return $output;
    }

    /**
     * Returns HTML formatted element's error message.
     *
     * Pass #supress_errors in #form element to avoid error rendering.
     *
     * @param array $element
     * @return string
     */
    public function renderError( $element )
    {
        if ( !isset( $element['#error'] ) ) {
            return '';
        }
        $output = '<div class="form-error '
                . $this->css_class . '-error '
                . $this->css_class . '-form-error '
                . $this->css_class . '-' . $element['#type'] . '-error '
                . $element['#type'] . '-error form-error-label'
                . '">' . $element['#error'] . '</div>'
                . "\r\n";
        return $output;
    }

    /**
     * Returns HTML formatted output for fieldset.
     *
     * @param array $element
     * @param string $action open|close|wrap
     * @param string $wrap_content HTML formatted output of child elements
     * @return string
     */
    public function fieldset( $element, $action = 'open', $wrap_content = '' )
    {
        $collapsible_open = '<div class="fieldset-wrapper">';
        $collapsible_close = '</div>';
        $legend_class = '';
        if ( !isset( $element['#id'] ) ) {
            $element['#id'] = 'fieldset-' . $this->_count( 'fieldset' );
        }
        if ( !isset( $element['_attributes_string'] ) ) {
            $element['_attributes_string'] = $this->_setElementAttributes( $element );
        }
        if ( (isset( $element['#collapsible'] ) && $element['#collapsible'])
                || (isset( $element['#collapsed'] ) && $element['#collapsed']) ) {
            $collapsible_open = '<div class="collapsible fieldset-wrapper">';
            $collapsible_close = '</div>';
            $legend_class = ' class="legend-expanded"';
        }
        if ( isset( $element['#collapsed'] ) && $element['#collapsed'] ) {
            $collapsible_open = str_replace( 'class="', 'class="collapsed ',
                    $collapsible_open );
            $legend_class = ' class="legend-collapsed"';
        }
        $output = '';
        switch ( $action ) {
            case 'close':
                $output .= $collapsible_close . "</fieldset>\r\n";
                $output .= isset( $element['#suffix'] ) ? $element['#suffix']
                        . "\r\n" : '';
                $output .= "\n\r";
                break;

            case 'open':
                $output .= $collapsible_open;
                $output .= isset( $element['#prefix'] ) ? $element['#prefix']
                        . "\r\n" : '';
                $output .= '<fieldset' . $element['_attributes_string']
                        . ' id="' . $element['#id'] . '">' . "\r\n";
                $output .= isset( $element['#title'] ) ? '<legend'
                        . $legend_class . '>'
                        . stripslashes( $element['#title'] )
                        . "</legend>\r\n" : '';
                $output .=
                        !empty( $element['#description'] ) ? $this->_setElementDescription( $element ) : '';
                $output .= "\n\r";
                break;

            case 'wrap':
                if ( !empty( $wrap_content ) ) {
                    $output .= isset( $element['#prefix'] ) ? $element['#prefix'] : '';
                    $output .= '<fieldset' . $element['_attributes_string']
                            . ' id="' . $element['#id'] . '">' . "\r\n";
                    $output .= '<legend' . $legend_class . '>'
                            . stripslashes( $element['#title'] )
                            . "</legend>\r\n"
                            . $collapsible_open;
                    $output .=!empty( $element['#description'] ) ? $this->_setElementDescription( $element ) : '';
                    $output .= $wrap_content . $collapsible_close
                            . "</fieldset>\r\n";
                    $output .=
                            isset( $element['#suffix'] ) ? $element['#suffix'] : '';
                    $output .= "\n\r";
                }
                break;
        }
        return $output;
    }

    /**
     * Returns HTML formatted output for checkbox element.
     *
     * @param array $element
     * @return string
     */
    public function checkbox( $element )
    {
        $element['#type'] = 'checkbox';
        $element = $this->_setRender( $element );
        $element['_render']['element'] = '<input type="checkbox" id="'
                . $element['#id'] . '" name="'
                . $element['#name'] . '" value="';
        // Specific: if value is empty force 1 to be rendered
        $element['_render']['element'] .=
                !empty( $element['#value'] ) ? htmlspecialchars( $element['#value'] ) : 1;
        $element['_render']['element'] .= '"' . $element['_attributes_string'];
        $element['_render']['element'] .= ((!$this->isSubmitted()
                && !empty( $element['#default_value'] ))
                || ($this->isSubmitted()
                && !empty( $element['#value'] ))) ? ' checked="checked"' : '';
        // Removed because not checkboxes can be disabled
//        if ( !empty( $element['#attributes']['disabled'] ) || !empty( $element['#disable'] ) ) {
//            $element['_render']['element'] .= ' onclick="javascript:return false; if(this.checked == 1){this.checked=1; return true;}else{this.checked=0; return false;}"';
//        }
        $element['_render']['element'] .= ' />';
        $pattern = isset( $element['#pattern'] ) ? $element['#pattern'] : '<BEFORE><PREFIX><ELEMENT>&nbsp;<LABEL><ERROR><SUFFIX><DESCRIPTION><AFTER>';
        $output = $this->_pattern( $pattern, $element );
        $output = $this->_wrapElement( $element, $output );
        return $output . "\r\n";
    }

    /**
     * Returns HTML formatted output for checkboxes element.
     *
     * Renders more than one checkboxes provided as elements in '#options'
     * array element.
     *
     * @param array $element
     * @return string
     */
    public function checkboxes( $element )
    {
        $element['#type'] = 'checkboxes';
        $element = $this->_setRender( $element );
        $clone = $element;
        $clone['#type'] = 'checkbox';
        $element['_render']['element'] = '';
        if ( isset($element['#options']) && !empty($element['#options'] ) ) {
            foreach ( $element['#options'] as $ID => $value ) {
                if ( !is_array( $value ) ) {
                    $value = array('#title' => $ID, '#value' => $value, '#name' => $element['#name'] . '[]');
                }
                $element['_render']['element'] .= $this->checkbox( $value );
            }
        }
        $pattern = isset( $element['#pattern'] ) ? $element['#pattern'] : '<BEFORE><PREFIX><TITLE><DESCRIPTION><ELEMENT><SUFFIX><AFTER>';
        $output = $this->_pattern( $pattern, $element );
        $output = $this->_wrapElement( $element, $output );
        return $output;
    }

    /**
     * Returns HTML formatted output for radio element.
     *
     * @param array $element
     * @return string
     */
    public function radio( $element )
    {
        if( !isset( $this->_count['radio'] ) )
            $this->_count['radio'] = 0;

        $element['#type'] = 'radio';
        $element = $this->_setRender( $element );
        $element['_render']['element'] = '<input type="radio" id="'
                . $element['#id'] . '" name="'
                . $element['#name'] . '" value="';
        $element['_render']['element'] .= isset( $element['#value'] ) ? htmlspecialchars( $element['#value'] ) : $this->_count['radio'];
        $element['_render']['element'] .= '"';
        $element['_render']['element'] .= $element['_attributes_string'];
        /**
         * checked
         */
        if (
            isset( $element['#value'] )
            && (
                $element['#value'] === $element['#default_value']
                || (
                    is_string($element['#value'])
                    && '0' == $element['#value']
                    && is_numeric($element['#default_value'])
                    && 0 == $element['#default_value']
                )
            )
        ) {
            $element['_render']['element'] .= ' checked="checked"';
        }
        if ( isset( $element['#disable'] ) && $element['#disable'] ) {
            $element['_render']['element'] .= ' disabled="disabled"';
        }
        $element['_render']['element'] .= ' />';
        $pattern = isset( $element['#pattern'] ) ? $element['#pattern'] : '<BEFORE><PREFIX><ELEMENT>&nbsp;<LABEL><ERROR><SUFFIX><DESCRIPTION><AFTER>';
        $output = $this->_pattern( $pattern, $element );
        $output = $this->_wrapElement( $element, $output );
        return $output;
    }

    /**
     * Returns HTML formatted output for radios elements.
     *
     * Radios are provided via #options array.
     * Requires #name value.
     *
     * @param array $element
     * @return string
     */
    public function radios( $element )
    {
        if ( !isset( $element['#name'] ) || empty( $element['#name'] ) ) {
            return FALSE;
        }

        $before = '';
        $after = isset($element['#options-after'])
            ? $element['#options-after']
            : '<br >';
        $list = false;

        if (
            true
            && isset($element['#attributes'])
            && isset($element['#attributes']['display'])
        ) {
            switch( $element['#attributes']['display'] ) {
            case 'ol':
            case 'ul':
                $before = '<li>';
                $after = '</li>';
                $list = true;
            }
        }

        $element['#type'] = 'radios';
        $element = $this->_setRender( $element );
        $element['_render']['element'] = '';
        if ( $list ) {
            $element['_render']['element'] .= sprintf('<%s>', $element['#attributes']['display']);
        }
        foreach ( $element['#options'] as $ID => $value ) {
            $this->_count( 'radio' );
            if ( !is_array( $value ) ) {
                $value = array('#title' => $ID, '#value' => $value);
                $value['#inline'] = true;
                $value['#after'] = $after;
                $value['#before'] = $before;
            }
            $value['#name'] = $element['#name'];
            $value['#default_value'] = isset( $element['#default_value'] ) ? $element['#default_value'] : $value['#value'];
            $value['#disable'] = isset( $element['#disable'] ) ? $element['#disable'] : false;
            $element['_render']['element'] .= $this->radio( $value );
        }
        if ( $list ) {
            $element['_render']['element'] .= sprintf('</%s>', $element['#attributes']['display']);
        }
        $pattern = isset( $element['#pattern'] ) ? $element['#pattern'] : '<BEFORE><PREFIX><TITLE><DESCRIPTION><ELEMENT><SUFFIX><AFTER>';
        $output = $this->_pattern( $pattern, $element );
        $output = $this->_wrapElement( $element, $output );
        return $output;
    }

    /**
     * Returns HTML formatted output for select element.
     *
     * @param array $element
     * @return string
     */
    public function select( $element )
    {
        $element['#type'] = 'select';
        $element = $this->_setRender( $element );
        $element['_render']['element'] = '<select id="' . $element['#id']
                . '" name="' . $element['#name'] . '"'
                . $element['_attributes_string'] . ">\r\n";
        $count = 1;
        foreach ( $element['#options'] as $id => $value ) {
            if ( !is_array( $value ) ) {
                $value = array('#title' => $id, '#value' => $value);
            }
            if ( !isset( $value['#value'] ) ) {
                $value['#value'] = $this->_count['select'] . '-' . $count;
                $count += 1;
            }
            $value['#type'] = 'option';
            $element['_render']['element'] .= '<option value="'
                    . htmlspecialchars( $value['#value'] ) . '"';
            $element['_render']['element'] .= ( isset($element['#default_value']) && $element['#default_value'] == $value['#value']) ? ' selected="selected"' : '';
            $element['_render']['element'] .= $this->_setElementAttributes( $value );
            $element['_render']['element'] .= '>';
            $element['_render']['element'] .= $this->strip( isset( $value['#title'] ) ? $value['#title'] : $value['#value'] );
            $element['_render']['element'] .= "</option>\r\n";
        }
        $element['_render']['element'] .= "</select>\r\n";
        $pattern = isset( $element['#pattern'] ) ? $element['#pattern'] : '<BEFORE><LABEL><DESCRIPTION><ERROR><PREFIX><ELEMENT><SUFFIX><AFTER>';
        $output = $this->_pattern( $pattern, $element );
        $output = $this->_wrapElement( $element, $output );
        return $output;
    }

    /**
     * Returns HTML formatted output for textfield element.
     *
     * @param array $element
     * @return string
     */
    public function textfield( $element )
    {
        $element['#type'] = 'textfield';
        $element = $this->_setRender( $element );
        $element['_render']['element'] = '<input type="text" id="'
                . $element['#id'] . '" name="' . $element['#name'] . '" value="';
        $element['_render']['element'] .= isset( $element['#value'] ) ? htmlspecialchars( stripslashes( $element['#value'] ) ) : '';
        $element['_render']['element'] .= '"' . $element['_attributes_string'];
        if ( isset( $element['#disable'] ) && $element['#disable'] ) {
            $element['_render']['element'] .= ' disabled="disabled"';
        }
        $element['_render']['element'] .= ' />';
        $pattern = isset( $element['#pattern'] ) ? $element['#pattern'] : '<BEFORE><LABEL><ERROR><PREFIX><ELEMENT><SUFFIX><DESCRIPTION><AFTER>';
        $output = $this->_pattern( $pattern, $element );
        $output = $this->_wrapElement( $element, $output );
        return $output . "\r\n";
    }

    /**
     * Returns HTML formatted output for textfield element.
     *
     * @param array $element
     * @return string
     */
    public function password( $element )
    {
        $element['#type'] = 'password';
        $element = $this->_setRender( $element );
        $element['_render']['element'] = '<input type="password" id="'
                . $element['#id'] . '" name="' . $element['#name'] . '" value="';
        $element['_render']['element'] .= isset( $element['#value'] ) ? $element['#value'] : '';
        $element['_render']['element'] .= '"' . $element['_attributes_string'];
        if ( isset( $element['#disable'] ) && $element['#disable'] ) {
            $element['_render']['element'] .= ' disabled="disabled"';
        }
        $element['_render']['element'] .= ' />';
        $pattern = isset( $element['#pattern'] ) ? $element['#pattern'] : '<BEFORE><LABEL><ERROR><PREFIX><ELEMENT><SUFFIX><DESCRIPTION><AFTER>';
        $output = $this->_pattern( $pattern, $element );
        $output = $this->_wrapElement( $element, $output );
        return $output . "\r\n";
    }

    /**
     * Returns HTML formatted output for textarea element.
     *
     * @param array $element
     * @return string
     */
    public function textarea( $element )
    {
        $element['#type'] = 'textarea';
        if ( !isset( $element['#attributes']['rows'] ) ) {
            $element['#attributes']['rows'] = 5;
        }
        if ( !isset( $element['#attributes']['cols'] ) ) {
            $element['#attributes']['cols'] = 1;
        }
        $element = $this->_setRender( $element );
        $element['_render']['element'] = '<textarea id="' . $element['#id']
                . '" name="' . $element['#name'] . '"'
                . $element['_attributes_string'] . '>';
        $element['_render']['element'] .= isset( $element['#value'] ) ? htmlspecialchars( stripslashes( $element['#value'] ) ) : '';
        $element['_render']['element'] .= '</textarea>' . "\r\n";
        $pattern = isset( $element['#pattern'] ) ? $element['#pattern'] : '<BEFORE><LABEL><DESCRIPTION><ERROR><PREFIX><ELEMENT><SUFFIX><AFTER>';
        $output = $this->_pattern( $pattern, $element );
        $output = $this->_wrapElement( $element, $output );
        return $output . "\r\n";
    }

    /**
     * Returns HTML formatted output for file upload element.
     *
     * @param array $element
     * @return string
     */
    public function file( $element )
    {
        $element['#type'] = 'file';
        $element = $this->_setRender( $element );
        $element['_render']['element'] = '<input type="file" id="'
                . $element['#id'] . '" name="' . $element['#name'] . '"'
                . $element['_attributes_string'];
        if ( isset( $element['#disable'] ) && $element['#disable'] ) {
            $element['_render']['element'] .= ' disabled="disabled"';
        }
        $element['_render']['element'] .= ' />';
        $pattern = isset( $element['#pattern'] ) ? $element['#pattern'] : '<BEFORE><LABEL><ERROR><PREFIX><ELEMENT><DESCRIPTION><SUFFIX><AFTER>';
        $output = $this->_pattern( $pattern, $element );
        $output = $this->_wrapElement( $element, $output );
        return $output;
    }

    /**
     * Returns HTML formatted output for markup element.
     *
     * @param array $element
     * @return string
     */
    public function markup( $element )
    {
        if ( isset( $element['#pattern'] ) ) {
            $element = $this->_setRender( $element );
            $element['_render']['label'] = isset($element['#title'])? $element['#title']:__('[no title]', 'aione-app-builder');
            $element['_render']['element'] = isset($element['#markup'])? $element['#markup']:'';
            return $this->_pattern( $element['#pattern'], $element );
        }
        if ( isset($element['#markup'] ) ) {
            return $element['#markup'];
        }
        return '';
    }

    /**
     * Returns HTML formatted output for notice element.
     *
     * @param array $element
     * @return string
     */
    public function notice( $element )
    {
        if ( isset($element['#markup'] ) ) {
            $element['#markup'] = sprintf(
                '<div class="notice notice-%s below-h2"><p>%s</p></div>',
                esc_attr(isset($element['#attributes']) && isset($element['#attributes']['type'])? $element['#attributes']['type']:'success'),
                $element['#markup']
            );
            return $this->markup($element);
        }
        return '';
    }

    /**
     * Returns HTML formatted output for hidden element.
     *
     * @param array $element
     * @return string
     */
    public function hidden( $element )
    {
        $element['#type'] = 'hidden';
        $element = $this->_setRender( $element );
        $output = '<input type="hidden" ';
        foreach( array('id', 'name' ) as $key ) {
            $output .= sprintf( '%s="%s" ', $key, $element['#'.$key] );
        }
        $output .= sprintf( 'value="%s" ', isset( $element['#value'] ) ? $element['#value'] : 1 );
        $output .= $element['_attributes_string'];
        $output .= ' />';
        if ( isset( $element['#after'] ) ) {
            $output .= $element['#after'];
        }
        return $output;
    }

    /**
     * Returns HTML formatted output for reset button element.
     *
     * @param array $element
     * @return string
     */
    public function reset( $element )
    {
        return $this->submit( $element, 'reset', 'Reset' );
    }

    /**
     * Returns HTML formatted output for button element.
     *
     * @param array $element
     * @return string
     */
    public function button( $element )
    {
        $element['#type'] = __FUNCTION__;
        $element = $this->_setRender( $element );
        $element['_render']['element'] = sprintf(
            '<button type="%s" id="%s" name="%s" %s>',
            esc_attr(__FUNCTION__),
            esc_attr($element['#id']),
            esc_attr($element['#name']),
            $element['_attributes_string']
        );
        $element['_render']['element'] .= isset( $element['#value'] ) ? $element['#value'] : $title;
        $element['_render']['element'] .= '</button>';
        $pattern = isset( $element['#pattern'] ) ? $element['#pattern'] : '<BEFORE><PREFIX><ELEMENT><SUFFIX><AFTER>';
        $output = $this->_pattern( $pattern, $element );
        return $output;
    }

    /**
     * Returns HTML formatted output for thumbnail element.
     *
     * @param array $element
     * @return string
     */
    public function thumbnail( $element )
    {
        /**
         * allowed only on admin
         */
        if ( !is_admin() ) {
            return '';
        }
        global $post;
        if ( is_object($post) ) {
            wp_enqueue_media(array('post' => $post->ID));
        }

        $image = $element['#value']? wp_get_attachment_image($element['#value']):'';
        $element['#attributes'] = array(
            'class' => 'feature-image-id',
        );
        $element['#after'] = sprintf(
            '<div class="wpt-file-preview">%s</div><a href="#" id="%s_media" class="feature-image" data-set="%s" data-remove="%s" data-value="%d" data-wpt-type="image">%s</a>',
            $image,
            $element['#id'],
            __('Set featured image', 'aione-app-builder'),
            __('Remove featured image', 'aione-app-builder'),
            $element['#value']? $element['#value']:0,
            $image? __('Remove featured image', 'aione-app-builder'):__('Set featured image', 'aione-app-builder')
        );
        return $this->hidden($element);
    }

    /**
     * Returns HTML formatted output for submit element.
     *
     * Used by reset and button.
     *
     * @param array $element
     * @param string $type
     * @param string $title
     * @return string
     */
    public function submit( $element, $type = 'submit', $title = 'Submit' )
    {
        $element['#type'] = $type;
        $element = $this->_setRender( $element );
        $element['_render']['element'] = '<input type="' . $type . '" id="'
                . $element['#id'] . '"  name="' . $element['#name'] . '" value="';
        $element['_render']['element'] .= isset( $element['#value'] ) ? $element['#value'] : $title;
        $element['_render']['element'] .= '"' . $element['_attributes_string']
                . ' />';
        $pattern = isset( $element['#pattern'] ) ? $element['#pattern'] : '<BEFORE><PREFIX><ELEMENT><SUFFIX><AFTER>';
        $output = $this->_pattern( $pattern, $element );
        return $output;
    }

    /**
     * Searches and returns submitted data for element.
     * 
     * @param type $element
     * @return type mixed
     */
    public function getSubmittedData( $element )
    {
        $name = $element['#name'];
        if ( strpos( $name, '[' ) === false ) {
            if ( $element['#type'] == 'file' ) {
                return $_FILES[$name]['tmp_name'];
            }
            return isset( $_REQUEST[$name] ) ? sanitize_text_field( $_REQUEST[$name] ) : (in_array( $element['#type'],
                            array('textfield', 'textarea') ) ? '' : 0);
        }

        if ( !function_exists('getSubmittedDataTrim')) {
            function getSubmittedDataTrim($a)
            {
                return trim($a, ']');
            }
        }

        $parts = explode( '[', $name );
        $parts = array_map( 'getSubmittedDataTrim', $parts );
        if ( !isset( $_REQUEST[$parts[0]] ) ) {
            return in_array( $element['#type'], array('textfield', 'textarea') ) ? '' : 0;
        }
        $search = $_REQUEST[$parts[0]];
        for ( $index = 0; $index < count( $parts ); $index++ ) {
            $key = $parts[$index];
            // We're at the end but no data retrieved
            if ( !isset( $parts[$index + 1] ) ) {
                return in_array( $element['#type'],
                                array('textfield', 'textarea') ) ? '' : 0;
            }
            $key_next = $parts[$index + 1];
            if ( $index > 0 ) {
                if ( !isset( $search[$key] ) ) {
                    return in_array( $element['#type'],
                                    array('textfield', 'textarea') ) ? '' : 0;
                } else {
                    $search = $search[$key];
                }
            }
            if ( is_array( $search ) && array_key_exists( $key_next, $search ) ) {
                if ( !is_array( $search[$key_next] ) ) {
                    return $search[$key_next];
                }
            }
        }
        return 0;
    }

    private function strip($value)
    {
        if ( empty( $value ) ) {
            return $value;
        }
        $re = array( "/\\\\'/", '/\\\\"/' );
        $to = array( "'", '"' );
        return esc_attr( preg_replace( $re, $to, $value ) );
    }
}
