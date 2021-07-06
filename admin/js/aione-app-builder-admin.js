(function( $ ) {
	'use strict';

	if( typeof typesStatusBasicJsScript === 'undefined' ) {
	    // TODO we need to get rid of multiple script ids on wp_register_script for basic.js
	    var typesStatusBasicJsScript = 'loaded';

	    var aioneFormGroupsSupportPostTypeState = new Array();
	    var aioneFormGroupsSupportTaxState = new Array();
	    var aioneFormGroupsSupportTemplatesState = new Array();

	    // TODO document this
	    var aioneFieldsEditorCallback_redirect = null;

	    jQuery( document ).ready( function() {

	        var aioneBody = jQuery( 'body' );

	        /**
	         * check description field
	         */
	        if( jQuery( '.js-aione-toggle-description' ) ) {
	            var $toggle = jQuery( '.js-aione-toggle-description' );
	            if( jQuery( '.js-aione-description', $toggle.parent() ).val() ) {
	                jQuery( '.js-aione-description', $toggle.parent() ).slideDown();
	                $toggle.hide();
	            } else {
	                $toggle.show();
	            }
	            $toggle.on( 'click', function() {
	                jQuery( '.js-aione-description', $toggle.parent() ).slideDown();
	                $toggle.hide();
	                return false;
	            } );
	        }

	        /**
	         * modal advertising
	         */
	        /*
	         if(jQuery.isFunction(jQuery.fn.types_modal_box)) {
	         jQuery('.js-types-show-modal').types_modal_box();
	         }
	         */
	        jQuery( '.aione-notif-description a' ).on( 'click', function() {
	            jQuery( this ).attr( 'target', '_blank' );
	        } );
	        //user suggestion
	        if( jQuery.isFunction( jQuery.suggest ) ) {
	            jQuery( '.input' ).suggest( "admin-ajax.php?action=aione_types_suggest_user&tax=post_tag", {
	                multiple: false,
	                multipleSep: ","
	            } );
	        }
	        // Only for adding group
	        jQuery( '.aione-fields-add-ajax-link' ).click( function() {
	            jQuery.ajax( {
	                url: jQuery( this ).attr( 'href' ),
	                cache: false,
	                beforeSend: function() {
	                    jQuery( '#aione-fields-under-title' ).hide();
	                    jQuery( '#aione-ajax-response' ).addClass( 'aione-ajax-loading' );
	                },
	                success: function( data ) {
	                    jQuery( '#aione-ajax-response' ).removeClass( 'aione-ajax-loading' );
	                    jQuery( '#aione-fields-sortable' ).append( data );
	                    jQuery( '#aione-fields-sortable .ui-draggable:last' ).find( 'input:first' ).focus().select();
	                    var scrollToHeight = jQuery( '#aione-fields-sortable .ui-draggable:last' ).offset();
	                    window.scrollTo( 0, scrollToHeight.top );
	                    /**
	                     * bind logic button if it is possible
	                     */
	                    if( 'function' == typeof(aioneConditionalLogiButtonsBindClick) ) {
	                        aioneConditionalLogiButtonsBindClick();
	                    }
	                }
	            } );
	            return false;
	        } );

	        aioneBody.on( 'click', '.aione-form-fieldset legend', function() {
	            jQuery( this ).parent().children( ".collapsible" ).slideToggle( "fast", function() {
	                var toggle = '';
	                if( jQuery( this ).is( ":visible" ) ) {
	                    jQuery( this ).parent().children( "legend" ).removeClass( "legend-collapsed" ).addClass( "legend-expanded" );
	                    toggle = 'open';
	                } else {
	                    jQuery( this ).parent().children( "legend" ).removeClass( "legend-expanded" ).addClass( "legend-collapsed" );
	                    toggle = 'close';
	                }
	                // Save collapsed state
	                // Get fieldset id
	                var collapsed = jQuery( this ).parent().attr( 'id' );

	                // For group form save fieldset toggle per group
	                if( jQuery( this ).parents( 'form' ).hasClass( 'aione-fields-form' ) ) {
	                    // Get group id
	                    var group_id = false;
	                    if( jQuery( 'input[name="group_id"]' ).length > 0 ) {
	                        group_id = jQuery( 'input[name="group_id"]' ).val();
	                    } else {
	                        group_id = -1;
	                    }
	                    jQuery.ajax( {
	                        url: ajaxurl,
	                        cache: false,
	                        type: 'get',
	                        data: 'action=aione_ajax&aione_action=group_form_collapsed&id=' + collapsed + '&toggle=' + toggle + '&group_id=' + group_id + '&_wpnonce=' + aione_nonce_toggle_group
	                    } );
	                } else {
	                    jQuery.ajax( {
	                        url: ajaxurl,
	                        cache: false,
	                        type: 'get',
	                        data: 'action=aione_ajax&aione_action=form_fieldset_toggle&id=' + collapsed + '&toggle=' + toggle + '&_wpnonce' + aione_nonce_toggle_fieldset
	                    } );
	                }
	            } );
	        } );
	        aioneBody.on( 'keyup', '.aione-form-groups-radio-update-title-display-value', function() {
	            jQuery( '#' + jQuery( this ).attr( 'id' ) + '-display-value' ).prev( 'label' ).html( jQuery( this ).val() );
	        } );
	        jQuery( '.form-error' ).parents( '.collapsed' ).slideDown();
	        aioneBody.on( 'focus', '.aione-form input', function() {
	            jQuery( this ).parents( '.collapsed' ).slideDown();
	        } );

	        // Delete AJAX added element
	        aioneBody.on( 'click', '.aione-form-fields-delete', function() {
	            if( jQuery( this ).attr( 'href' ) == 'javascript:void(0);' ) {
	                jQuery( this ).parent().fadeOut( function() {
	                    jQuery( this ).remove();
	                } );
	            }
	        } );

	        /*
	         * Generic AJAX call (link). Parameters can be used.
	         */
	        aioneBody.on( 'click', '.aione-ajax-link', function() {
	        	
	            var callback = aioneGetParameterByName( 'aione_ajax_callback', jQuery( this ).attr( 'href' ) );
	            var action = aioneGetParameterByName( 'action', jQuery( this ).attr( 'href' ) );
	            var update = aioneGetParameterByName( 'aione_ajax_update', jQuery( this ).attr( 'href' ) );
	            var updateAdd = aioneGetParameterByName( 'aione_ajax_update_add', jQuery( this ).attr( 'href' ) );
	            var warning = aioneGetParameterByName( 'aione_warning', jQuery( this ).attr( 'href' ) );
	            var thisObject = jQuery( this );
	            var thisObjectTR = jQuery( this ).closest( 'tr' );
	            if( warning != false ) {
	                var answer = confirm( warning );
	                if( answer == false ) {
	                    return false;
	                }
	            }
	            jQuery.ajax( {
	                url: jQuery( this ).attr( 'href' ),
	                type: 'get',
	                dataType: 'json',
	                cache: false,
	                action: action,
	                beforeSend: function() {
	                    if( update != false ) {
	                        jQuery( '#' + update ).html( '' ).show().addClass( 'aione-ajax-loading-small' );
	                    }
	                },
	                success: function( data ) {
	             
	                    if( data != null ) {
	                        if( typeof data.output != 'undefined' ) {
	                            if( update != false ) {
	                                jQuery( '#' + update ).removeClass( 'aione-ajax-loading-small' ).html( data.output );
	                            }
	                            if( updateAdd != false ) {
	                                if( data.output.length < 1 ) {
	                                    jQuery( '#' + updateAdd ).fadeOut();
	                                }
	                                jQuery( '#' + updateAdd ).append( data.output );
	                            }
	                        }
	                        if( typeof data.status != 'undefined' ) {
	                            if( 'inactive' == data.status ) {
	                                thisObjectTR.addClass( 'status-inactive' );
	                            } else {
	                                thisObjectTR.removeClass( 'status-inactive' );
	                            }
	                        }
	                        if( typeof data.status_label != 'undefined' ) {
	                            jQuery( 'td.status', thisObjectTR ).html( data.status_label );
	                        }
	                        if(
	                            typeof data.execute != 'undefined'
	                            && (
	                                typeof data.aione_nonce_ajax_callback != 'undefined'	                              
	                            )
	                        ) {
	                            switch( data.execute ) {
	                                case 'redirect':
	                                    window.location = data.aione_redirect;
	                                    break;
	                                case 'reload':
	                                    location.reload();
	                                    break;
	                                case 'append':
	                                    if(
	                                        typeof data.append_target != 'undefined'
	                                        && typeof data.append_value != 'undefined'
	                                    ) {
	                                        jQuery( data.append_target ).append( data.append_value );
	                                    }
	                                    break;
	                            }
	                        }
	                    }
	                    if( callback != false ) {
	                        eval( callback + '(data, thisObject)' );
	                    }
	                }
	            } );
	            aioneLoadingButtonStop();
	            return false;
	        } );

	        jQuery( '.aione-form-groups-support-post-type' ).each( function() {
	            if( jQuery( this ).is( ':checked' ) ) {
	                window.aioneFormGroupsSupportPostTypeState.push( jQuery( this ).attr( 'id' ) );
	            }
	        } );

	        jQuery( '.aione-form-groups-support-tax' ).each( function() {
	            if( jQuery( this ).is( ':checked' ) ) {
	                window.aioneFormGroupsSupportTaxState.push( jQuery( this ).attr( 'id' ) );
	            }
	        } );

	        jQuery( '.aione-form-groups-support-templates input' ).each( function() {
	            if( jQuery( this ).is( ':checked' ) ) {
	                window.aioneFormGroupsSupportTemplatesState.push( jQuery( this ).attr( 'id' ) );
	            }
	        } );

	        // Add scroll to user created fieldset if necessary
	        if( jQuery( '#aione-form-groups-user-fields' ).length > 0 ) {
	            var aioneFormGroupsUserCreatedFieldsHeight = Math.round( jQuery( '#aione-form-groups-user-fields' ).height() );
	            var aioneScreenHeight = Math.round( jQuery( window ).height() );
	            var aioneFormGroupsUserCreatedFieldsOffset = jQuery( '#aione-form-groups-user-fields' ).offset();
	            /**
	             * use jScrollPane only when have enough space
	             */
	            if( aioneScreenHeight - aioneFormGroupsUserCreatedFieldsOffset.top > 100 ) {
	                if( aioneFormGroupsUserCreatedFieldsHeight + aioneFormGroupsUserCreatedFieldsOffset.top > aioneScreenHeight ) {
	                    var aioneFormGroupsUserCreatedFieldsHeightResize = Math.round( aioneScreenHeight - aioneFormGroupsUserCreatedFieldsOffset.top - 40 );
	                    jQuery( '#aione-form-groups-user-fields' ).height( aioneFormGroupsUserCreatedFieldsHeightResize );
	                    jQuery( '#aione-form-groups-user-fields .fieldset-wrapper' ).height( aioneFormGroupsUserCreatedFieldsHeightResize - 15 );
	                    jQuery( '#aione-form-groups-user-fields .fieldset-wrapper' ).jScrollPane();
	                }
	                jQuery( '.aione-form-fields-align-right' ).css( 'position', 'fixed' );
	            } else {
	                jQuery( '#aione-form-groups-user-fields' ).closest( '.aione-form-fields-align-right' ).css( 'position', 'absolute' );
	            }
	        }

	        // Types form
	        jQuery( 'input[name="ct[public]"]' ).change( function() {
	            if( jQuery( this ).val() == 'public' ) {
	                jQuery( '#aione-form-visiblity-toggle' ).slideDown();
	            } else {
	                jQuery( '#aione-form-visiblity-toggle' ).slideUp();
	            }
	            jQuery( 'input[name="ct[public]"]:checked' ).each( function() {
	                jQuery( '#post-visibility-display' ).html(
	                    jQuery( '.title', jQuery( 'label[for=' + jQuery( this ).attr( 'id' ) + ']' ) ).html()
	                );
	            } );
	        } );
	        jQuery( 'input[name="ct[rewrite][custom]"]' ).change( function() {
	            if( jQuery( this ).val() == 'custom' ) {
	                jQuery( '#aione-form-rewrite-toggle' ).slideDown();
	            } else {
	                jQuery( '#aione-form-rewrite-toggle' ).slideUp();
	            }
	        } );
	        jQuery( '.aione-tax-form input[name="ct[rewrite][enabled]"]' ).change( function() {
	            if( jQuery( this ).is( ':checked' ) ) {
	                jQuery( '#aione-form-rewrite-toggle' ).slideDown();
	            } else {
	                jQuery( '#aione-form-rewrite-toggle' ).slideUp();
	            }
	        } );
	        /**
	         * meta_box_cb
	         */
	        jQuery( '.aione-tax-form input[name="ct[meta_box_cb][disabled]"]' ).change( function() {
	            if( jQuery( this ).is( ':checked' ) ) {
	                jQuery( '#aione-form-meta_box_cb-toggle' ).slideUp();
	            } else {
	                jQuery( '#aione-form-meta_box_cb-toggle' ).slideDown();
	            }
	        } );
	        jQuery( 'input[name="ct[show_in_menu]"]' ).change( function() {
	            if( jQuery( this ).is( ':checked' ) ) {
	                jQuery( '#aione-form-showinmenu-toggle' ).slideDown();
	            } else {
	                jQuery( '#aione-form-showinmenu-toggle' ).slideUp();
	            }
	        } );
	        jQuery( 'input[name="ct[has_archive]"]' ).change( function() {
	            if( jQuery( this ).is( ':checked' ) ) {
	                jQuery( '#aione-form-has_archive-toggle' ).slideDown();
	            } else {
	                jQuery( '#aione-form-has_archive-toggle' ).slideUp();
	            }
	        } );
	        jQuery( 'input[name="ct[query_var_enabled]"]' ).change( function() {
	            if( jQuery( this ).is( ':checked' ) ) {
	                jQuery( '#aione-form-queryvar-toggle' ).slideDown();
	            } else {
	                jQuery( '#aione-form-queryvar-toggle' ).slideUp();
	            }
	        } );

	        jQuery( '.aione-groups-form-ajax-update-custom_taxonomies-ok, .aione-groups-form-ajax-update-custom_post_types-ok, .aione-groups-form-ajax-update-templates-ok' ).click( function() {
	            var count = 0;
	            if( jQuery( '.aione-groups-form-ajax-update-custom_taxonomies-ok' ).parent().find( "input:checked" ).length > 0 ) {
	                count += 1;
	            }
	            if( jQuery( '.aione-groups-form-ajax-update-custom_post_types-ok' ).parent().find( "input:checked" ).length > 0 ) {
	                count += 1;
	            }
	            if( jQuery( '.aione-groups-form-ajax-update-templates-ok' ).parent().find( "input:checked" ).length > 0 ) {
	                count += 1;
	            }
	            if( count > 1 ) {
	                jQuery( '#aione-fields-form-filters-association-form' ).show();
	            } else {
	                jQuery( '#aione-fields-form-filters-association-form' ).hide();
	            }
	            aioneFieldsFormFiltersSummary();
	        } );

	        // Loading submit button
	        jQuery( '.aione-tax-form, .aione-form' ).submit( function() {
	            aioneLoadingButton();
	        } );

	        aioneBindAutoCreateSlugs();

	        DisableDuplicatedFields();

	    } );
		
		jQuery( document ).ready( function( $ ) {
			if($('.js-aione-template-slugize-source').val() != ''){
				var slug = jQuery( '.js-aione-template-slugize-source' ).val();
				var validSlug = aione_slugize( slug );
				$('.template-slug').text(validSlug);
		        $('#template_slug').val(validSlug);
			}
		 });

		jQuery( document ).on( 'keypress keyup keydown blur', '.js-aione-template-slugize-source', function() {
	        var slug = jQuery( this ).val();
	        console.log(slug);
	        if( '' !== slug ) {
	            var validSlug = aione_slugize( slug );
	            $('.template-slug').text(validSlug);
		        $('#template_slug').val(validSlug);		                	
	        }
	    } );


	    function DisableDuplicatedFields() {
	        var added_fields = [];

	        jQuery( 'div[data-wpt-id]' ).each( function() {
	            var $container = this;
	            if( jQuery.inArray( jQuery( $container ).attr( 'data-wpt-id' ), added_fields ) !== -1 ) {

	                var message = aione_basic.field_already_in_use = aione_basic.field_already_in_use.replace( '##DATA-FIELD-ID##', jQuery( $container ).attr( 'data-wpt-id' ) );
	                var single_element = jQuery( $container ).children().find( '[data-wpt-id]' );

	                if( single_element.attr( 'type' ) === 'radio' ) {
	                    single_element.each( function( element ) {
	                        if( jQuery( this ).attr( 'checked' ) === "checked" ) {
	                            jQuery( 'input[data-wpt-id="' + jQuery( this ).attr( 'data-wpt-id' ) + '"][value=' + jQuery( this ).val() + ']:first' ).attr( 'checked', true );
	                            return false;
	                        }
	                    } );
	                }
	                jQuery( $container ).children().find( '[data-wpt-id]' ).attr( "disabled", true );


	                // for term edit page || term listing page
	                if( jQuery( 'body.term-php' ).length || jQuery( 'body.edit-tags-php' ).length ) {
	                    var formField = jQuery( $container ).closest( '.form-field' );

	                    // get group
	                    var group = formField.prev();

	                    // remove duplicated field
	                    formField.remove();

	                    // if group has no more fields
	                    if( !group.hasClass( 'form-field' ) && !group.next().hasClass( 'form-field' ) ) {
	                        // remove headline
	                        group.remove();
	                    }

	                    // for user profile page
	                } else if( jQuery( 'body.profile-php' ).length ) {
	                    var formField = jQuery( $container ).closest( '.aione-profile-field-line' );

	                    // get group
	                    var group = formField.closest( '.aione-group-area' );

	                    // remove duplicated field
	                    formField.remove();

	                    var groupFields = group.children( '.aione-profile-field-line' );
	                    if( groupFields.length ) {
	                        if( !groupFields.html().trim() ) {
	                            group.hide();
	                        }
	                    }

	                    // for posts/pages/CPT
	                } else {
	                    var current_parent_div = jQuery( $container ).parent( '.inside' );
	                    $container.remove();
	                    if( current_parent_div.html() ) {
	                        if( !current_parent_div.html().trim() ) {
	                            current_parent_div.parent( '.postbox' ).hide();
	                        }
	                    }
	                }


	            } else {
	                added_fields.push( jQuery( $container ).attr( 'data-wpt-id' ) );
	            }


	        } );

	        jQuery( '.focus_correct_field' ).click( function() {
	            event.preventDefault();
	            var correct_field = jQuery( this ).data( 'field-slug' );
	            jQuery( '[data-wpt-id="' + correct_field + '"] input:enabled' ).focus().val( jQuery( '[data-wpt-id="' + correct_field + '"] input:enabled' ).val() );
	        } );
	    }


	    /**
	     * auto create slugs on all fields wich needs this:
	     *
	     * - custom post slug
	     * - custom taxonmy slug
	     * - custom field slug
	     * - user meta fields
	     */
	    function aioneBindAutoCreateSlugs() {
	        jQuery( document ).on( 'blur focus click', '.js-aione-slugize', function() {
	            var slug = jQuery( this ).val();
	            if( '' == slug ) {
	                slug = jQuery( '.js-aione-slugize-source', jQuery( this ).closest( '.js-aione-slugize-container' ) ).val();
	            }
	            if( '' != slug ) {
	                var validSlug = aione_slugize( slug );

	                if( validSlug != slug || jQuery( this ).val() == '' ) {
	                    jQuery( this ).val( validSlug.substring( 0, 200 ) );
	                }
	            }
	        } );
	    }

	    /**
	     * Searches for parameter inside string ('arg', 'edit.php?arg=first&arg2=sec')
	     */
	    function aioneGetParameterByName( name, string ) {
	        name = name.replace( /[\[]/, "\\\[" ).replace( /[\]]/, "\\\]" );
	        var regexS = "[\\?&]" + name + "=([^&#]*)";
	        var regex = new RegExp( regexS );
	        var results = regex.exec( string );
	        if( results == null ) {
	            return false;
	        } else {
	            return decodeURIComponent( results[ 1 ].replace( /\+/g, " " ) );
	        }
	    }

	    /**
	     * AJAX delete elements from group form callback.
	     */
	    function aioneFieldsFormDeleteElement( data, element ) {
	        element.parent().fadeOut( function() {
	            element.parent().remove();
	        } );
	    }

	    /**
	     * Set count for options
	     */
	    function aioneFieldsFormCountOptions( obj ) {
	        var count = aioneGetParameterByName( 'count', obj.attr( 'href' ) );
	        count++;
	        obj.attr( 'href', obj.attr( 'href' ).replace( /count=.*/, 'count=' + count ) );
	    }

	// Migrate checkboxes
	    function aioneCbSaveEmptyMigrate( object, field_slug, total, wpnonce, action, metaType ) {
	        jQuery.ajax( {
	            url: ajaxurl + '?action=aione_ajax&aione_action=cb_save_empty_migrate&field=' + field_slug + '&subaction=' + action + '&total=' + total + '&_wpnonce=' + wpnonce + '&meta_type=' + metaType,
	            type: 'get',
	            dataType: 'json',
	            //            data: ,
	            cache: false,
	            beforeSend: function() {
	                object.parent().parent().find( '.aione-cb-save-empty-migrate-response' ).html( '' ).show().addClass( 'aione-ajax-loading-small' );
	            },
	            success: function( data ) {
	                if( data != null ) {
	                    if( typeof data.output != 'undefined' ) {
	                        object.parent().parent().find( '.aione-cb-save-empty-migrate-response' ).removeClass( 'aione-ajax-loading-small' ).html( data.output );
	                    }
	                }
	            }
	        } );
	    }

	    function aioneCbMigrateStep( total, offset, field_slug, wpnonce, metaType ) {
	        jQuery.ajax( {
	            url: ajaxurl + '?action=aione_ajax&aione_action=cb_save_empty_migrate&field=' + field_slug + '&subaction=save&total=' + total + '&offset=' + offset + '&_wpnonce=' + wpnonce + '&meta_type=' + metaType,
	            type: 'get',
	            dataType: 'json',
	            //            data: ,
	            cache: false,
	            beforeSend: function() {
	                //            jQuery('#aione-cb-save-empty-migrate-response-'+field_slug).html(total+'/'+offset);
	            },
	            success: function( data ) {
	                if( data != null ) {
	                    if( typeof data.output != 'undefined' ) {
	                        jQuery( '#aione-cb-save-empty-migrate-response-' + field_slug ).html( data.output );
	                    }
	                }
	            }
	        } );
	    }

	    function aioneCdCheckDateCustomized( object ) {
	        var show = false;
	        object.parents( '.fieldset-wrapper' ).find( '.aione-cd-field option:selected' ).each( function() {
	            if( jQuery( this ).hasClass( 'aione-conditional-select-date' ) ) {
	                show = true;
	            }
	        } );
	        if( show ) {
	            object.parent().find( '.aione-cd-notice-date' ).show();
	        } else {
	            object.parent().find( '.aione-cd-notice-date' ).show();
	        }
	    }

	    /**
	     * Adds spinner graphics and disable button.
	     */
	    function aioneLoadingButton() {
	        jQuery( '.aione-disabled-on-submit' ).attr( 'disabled', 'disabled' ).each( function() {
	            if( 'undefined' == typeof(types_modal) ) {
	                var spinnerId = jQuery( this ).attr( 'id' ) + '-loading';
	                var currentElement = jQuery( this );

	                // Do not add the spinner if it's already present.
	                if( 0 == currentElement.parent().find( '#' + spinnerId ).length ) {
	                    currentElement.before( '<div id="' + spinnerId + '" class="js-aione-spinner spinner is-active">&nbsp;</div>' );
	                }
	            }
	        } );
	    }

	    /**
	     * Counter loading.
	     */
	    function aioneLoadingButtonStop() {
	        jQuery( '.aione-disabled-on-submit' ).removeAttr( 'disabled' );
	        jQuery( '.js-aione-spinner' ).fadeOut();
	        //Fix https://icanlocalize.basecamphq.com/projects/7393061-toolset/todo_items/194177056/comments
	        //type modal didnt disappeared
	        // jQuery('.types_modal_box').remove();
	        // jQuery('.types_block_page').remove();
	    }


	    function aioneFieldsEditorCallback( fieldID, metaType, postID ) {

	        if(
	            typeof WPViews != 'undefined'
	            && typeof WPViews.shortcodes_gui != 'undefined'
	            && typeof WPViews.shortcodes_gui.shortcode_gui_insert != 'undefined'
	        ) {
				/**
				 * @todo avoid this hard dependency by using the toolset-event-manager script
				 *
				 * Views 2.3.0 introduced a toolset hook to get the current shortcodes GUI action:
				 * var action = Toolset.hooks.applyFilters( 'wpv-filter-wpv-shortcodes-gui-get-gui-action', 'insert' );
				 */
				if ( WPViews.shortcodes_gui.shortcode_gui_insert == 'create' ) {
					typesWPViews.interceptEditShortcode( fieldID, metaType, postID, 'admin_bar' );
					return true;
				}
				if ( WPViews.shortcodes_gui.shortcode_gui_insert == 'append' ) {
					typesWPViews.interceptEditShortcode( fieldID, metaType, postID, 'input_append' );
					return true;
				}
	        }

	        var colorboxWidth = 750 + 'px';
	        /* postponed fix for wp core media queries in iframe
	         var colorboxWidth = 100 + '%';
	         */

	        if( !( jQuery.browser.msie && parseInt( jQuery.browser.version ) < 9 ) ) {
	            var documentWidth = jQuery( document ).width();
	            if( documentWidth < 750 ) {
	                colorboxWidth = 600 + 'px';
	            }
	        }

	        var url = ajaxurl + '?action=aione_ajax&aione_action=editor_callback&_typesnonce=' + types.wpnonce + '&field_id=' + fieldID + '&field_type=' + metaType + '&post_id=' + postID;
	        // Check if shortcode passed
	        if( typeof arguments[ 3 ] === 'string' ) {
	            // urlencode() PHP
	            url += '&shortcode=' + arguments[ 3 ];
	        }

	        jQuery.colorbox( {
	            href: url,
	            iframe: true,
	            inline: false,
	            width: colorboxWidth,
	            opacity: 0.7,
	            closeButton: false,
	            className: 'js-aione-colorbox-with-iframe'
	        } );
	    }

	    /**
	     * TODO Document this!
	     * 1.1.5
	     */
	    function aioneFieldsEditorCallback_set_redirect( function_name, params ) {
	        aioneFieldsEditorCallback_redirect = {
	            'function': function_name,
	            'params': params
	        };
	    }

	//Usermeta shortocde addon
	    function aione_showmore( show ) {
	        if( show ) {
	            jQuery( '#specific_user_div' ).css( 'display', 'block' );
	            jQuery( '#display_username_for_author' ).removeAttr( 'checked' );
	        }
	        else {
	            jQuery( '#specific_user_div' ).css( 'display', 'none' );
	            jQuery( '#display_username_for_suser' ).removeAttr( 'checked' );
	        }
	    }

	//Usermeta shortocde addon
	    function hideControls( control_id1, control_id2 ) {
	        control_id1 = '#' + control_id1;
	        control_id2 = '#' + control_id2;
	        jQuery( control_id1 ).css( 'display', 'none' );
	        jQuery( control_id2 ).css( 'display', 'inline' );
	        jQuery( control_id2 ).focus();
	    }

	    /**
	     * slugize
	     */
	    function aione_slugize( val ) {
	        /**
	         * not a string or empty - thank you
	         */
	        if( 'string' != typeof val || '' == val ) {
	            return;
	        }
	        val = removeDiacritics( val.toLowerCase() );
	        val = val.replace( /[^a-z0-9A-Z_]+/g, '-' );
	        val = val.replace( /\-+/g, '-' );
	        val = val.replace( /^\-/g, '' );
	        val = val.replace( /\-$/g, '' );
	        return val;
	    };

	    /**
	     * removeDiacritics
	     */

	    var defaultDiacriticsRemovalMap = [
	        {
	            'base': 'A',
	            'letters': /[\u0041\u24B6\uFF21\u00C0\u00C1\u00C2\u1EA6\u1EA4\u1EAA\u1EA8\u00C3\u0100\u0102\u1EB0\u1EAE\u1EB4\u1EB2\u0226\u01E0\u00C4\u01DE\u1EA2\u00C5\u01FA\u01CD\u0200\u0202\u1EA0\u1EAC\u1EB6\u1E00\u0104\u023A\u2C6F]/g
	        },
	        { 'base': 'AA', 'letters': /[\uA732]/g },
	        { 'base': 'AE', 'letters': /[\u00C6\u01FC\u01E2]/g },
	        { 'base': 'AO', 'letters': /[\uA734]/g },
	        { 'base': 'AU', 'letters': /[\uA736]/g },
	        { 'base': 'AV', 'letters': /[\uA738\uA73A]/g },
	        { 'base': 'AY', 'letters': /[\uA73C]/g },
	        { 'base': 'B', 'letters': /[\u0042\u24B7\uFF22\u1E02\u1E04\u1E06\u0243\u0182\u0181]/g },
	        { 'base': 'C', 'letters': /[\u0043\u24B8\uFF23\u0106\u0108\u010A\u010C\u00C7\u1E08\u0187\u023B\uA73E]/g },
	        {
	            'base': 'D',
	            'letters': /[\u0044\u24B9\uFF24\u1E0A\u010E\u1E0C\u1E10\u1E12\u1E0E\u0110\u018B\u018A\u0189\uA779]/g
	        },
	        { 'base': 'DZ', 'letters': /[\u01F1\u01C4]/g },
	        { 'base': 'Dz', 'letters': /[\u01F2\u01C5]/g },
	        {
	            'base': 'E',
	            'letters': /[\u0045\u24BA\uFF25\u00C8\u00C9\u00CA\u1EC0\u1EBE\u1EC4\u1EC2\u1EBC\u0112\u1E14\u1E16\u0114\u0116\u00CB\u1EBA\u011A\u0204\u0206\u1EB8\u1EC6\u0228\u1E1C\u0118\u1E18\u1E1A\u0190\u018E]/g
	        },
	        { 'base': 'F', 'letters': /[\u0046\u24BB\uFF26\u1E1E\u0191\uA77B]/g },
	        {
	            'base': 'G',
	            'letters': /[\u0047\u24BC\uFF27\u01F4\u011C\u1E20\u011E\u0120\u01E6\u0122\u01E4\u0193\uA7A0\uA77D\uA77E]/g
	        },
	        {
	            'base': 'H',
	            'letters': /[\u0048\u24BD\uFF28\u0124\u1E22\u1E26\u021E\u1E24\u1E28\u1E2A\u0126\u2C67\u2C75\uA78D]/g
	        },
	        {
	            'base': 'I',
	            'letters': /[\u0049\u24BE\uFF29\u00CC\u00CD\u00CE\u0128\u012A\u012C\u0130\u00CF\u1E2E\u1EC8\u01CF\u0208\u020A\u1ECA\u012E\u1E2C\u0197]/g
	        },
	        { 'base': 'J', 'letters': /[\u004A\u24BF\uFF2A\u0134\u0248]/g },
	        {
	            'base': 'K',
	            'letters': /[\u004B\u24C0\uFF2B\u1E30\u01E8\u1E32\u0136\u1E34\u0198\u2C69\uA740\uA742\uA744\uA7A2]/g
	        },
	        {
	            'base': 'L',
	            'letters': /[\u004C\u24C1\uFF2C\u013F\u0139\u013D\u1E36\u1E38\u013B\u1E3C\u1E3A\u0141\u023D\u2C62\u2C60\uA748\uA746\uA780]/g
	        },
	        { 'base': 'LJ', 'letters': /[\u01C7]/g },
	        { 'base': 'Lj', 'letters': /[\u01C8]/g },
	        { 'base': 'M', 'letters': /[\u004D\u24C2\uFF2D\u1E3E\u1E40\u1E42\u2C6E\u019C]/g },
	        {
	            'base': 'N',
	            'letters': /[\u004E\u24C3\uFF2E\u01F8\u0143\u00D1\u1E44\u0147\u1E46\u0145\u1E4A\u1E48\u0220\u019D\uA790\uA7A4]/g
	        },
	        { 'base': 'NJ', 'letters': /[\u01CA]/g },
	        { 'base': 'Nj', 'letters': /[\u01CB]/g },
	        {
	            'base': 'O',
	            'letters': /[\u004F\u24C4\uFF2F\u00D2\u00D3\u00D4\u1ED2\u1ED0\u1ED6\u1ED4\u00D5\u1E4C\u022C\u1E4E\u014C\u1E50\u1E52\u014E\u022E\u0230\u00D6\u022A\u1ECE\u0150\u01D1\u020C\u020E\u01A0\u1EDC\u1EDA\u1EE0\u1EDE\u1EE2\u1ECC\u1ED8\u01EA\u01EC\u00D8\u01FE\u0186\u019F\uA74A\uA74C]/g
	        },
	        { 'base': 'OI', 'letters': /[\u01A2]/g },
	        { 'base': 'OO', 'letters': /[\uA74E]/g },
	        { 'base': 'OU', 'letters': /[\u0222]/g },
	        { 'base': 'P', 'letters': /[\u0050\u24C5\uFF30\u1E54\u1E56\u01A4\u2C63\uA750\uA752\uA754]/g },
	        { 'base': 'Q', 'letters': /[\u0051\u24C6\uFF31\uA756\uA758\u024A]/g },
	        {
	            'base': 'R',
	            'letters': /[\u0052\u24C7\uFF32\u0154\u1E58\u0158\u0210\u0212\u1E5A\u1E5C\u0156\u1E5E\u024C\u2C64\uA75A\uA7A6\uA782]/g
	        },
	        {
	            'base': 'S',
	            'letters': /[\u0053\u24C8\uFF33\u1E9E\u015A\u1E64\u015C\u1E60\u0160\u1E66\u1E62\u1E68\u0218\u015E\u2C7E\uA7A8\uA784]/g
	        },
	        {
	            'base': 'T',
	            'letters': /[\u0054\u24C9\uFF34\u1E6A\u0164\u1E6C\u021A\u0162\u1E70\u1E6E\u0166\u01AC\u01AE\u023E\uA786]/g
	        },
	        { 'base': 'TZ', 'letters': /[\uA728]/g },
	        {
	            'base': 'U',
	            'letters': /[\u0055\u24CA\uFF35\u00D9\u00DA\u00DB\u0168\u1E78\u016A\u1E7A\u016C\u00DC\u01DB\u01D7\u01D5\u01D9\u1EE6\u016E\u0170\u01D3\u0214\u0216\u01AF\u1EEA\u1EE8\u1EEE\u1EEC\u1EF0\u1EE4\u1E72\u0172\u1E76\u1E74\u0244]/g
	        },
	        { 'base': 'V', 'letters': /[\u0056\u24CB\uFF36\u1E7C\u1E7E\u01B2\uA75E\u0245]/g },
	        { 'base': 'VY', 'letters': /[\uA760]/g },
	        { 'base': 'W', 'letters': /[\u0057\u24CC\uFF37\u1E80\u1E82\u0174\u1E86\u1E84\u1E88\u2C72]/g },
	        { 'base': 'X', 'letters': /[\u0058\u24CD\uFF38\u1E8A\u1E8C]/g },
	        {
	            'base': 'Y',
	            'letters': /[\u0059\u24CE\uFF39\u1EF2\u00DD\u0176\u1EF8\u0232\u1E8E\u0178\u1EF6\u1EF4\u01B3\u024E\u1EFE]/g
	        },
	        {
	            'base': 'Z',
	            'letters': /[\u005A\u24CF\uFF3A\u0179\u1E90\u017B\u017D\u1E92\u1E94\u01B5\u0224\u2C7F\u2C6B\uA762]/g
	        },
	        {
	            'base': 'a',
	            'letters': /[\u0061\u24D0\uFF41\u1E9A\u00E0\u00E1\u00E2\u1EA7\u1EA5\u1EAB\u1EA9\u00E3\u0101\u0103\u1EB1\u1EAF\u1EB5\u1EB3\u0227\u01E1\u00E4\u01DF\u1EA3\u00E5\u01FB\u01CE\u0201\u0203\u1EA1\u1EAD\u1EB7\u1E01\u0105\u2C65\u0250]/g
	        },
	        { 'base': 'aa', 'letters': /[\uA733]/g },
	        { 'base': 'ae', 'letters': /[\u00E6\u01FD\u01E3]/g },
	        { 'base': 'ao', 'letters': /[\uA735]/g },
	        { 'base': 'au', 'letters': /[\uA737]/g },
	        { 'base': 'av', 'letters': /[\uA739\uA73B]/g },
	        { 'base': 'ay', 'letters': /[\uA73D]/g },
	        { 'base': 'b', 'letters': /[\u0062\u24D1\uFF42\u1E03\u1E05\u1E07\u0180\u0183\u0253]/g },
	        { 'base': 'c', 'letters': /[\u0063\u24D2\uFF43\u0107\u0109\u010B\u010D\u00E7\u1E09\u0188\u023C\uA73F\u2184]/g },
	        {
	            'base': 'd',
	            'letters': /[\u0064\u24D3\uFF44\u1E0B\u010F\u1E0D\u1E11\u1E13\u1E0F\u0111\u018C\u0256\u0257\uA77A]/g
	        },
	        { 'base': 'dz', 'letters': /[\u01F3\u01C6]/g },
	        {
	            'base': 'e',
	            'letters': /[\u0065\u24D4\uFF45\u00E8\u00E9\u00EA\u1EC1\u1EBF\u1EC5\u1EC3\u1EBD\u0113\u1E15\u1E17\u0115\u0117\u00EB\u1EBB\u011B\u0205\u0207\u1EB9\u1EC7\u0229\u1E1D\u0119\u1E19\u1E1B\u0247\u025B\u01DD]/g
	        },
	        { 'base': 'f', 'letters': /[\u0066\u24D5\uFF46\u1E1F\u0192\uA77C]/g },
	        {
	            'base': 'g',
	            'letters': /[\u0067\u24D6\uFF47\u01F5\u011D\u1E21\u011F\u0121\u01E7\u0123\u01E5\u0260\uA7A1\u1D79\uA77F]/g
	        },
	        {
	            'base': 'h',
	            'letters': /[\u0068\u24D7\uFF48\u0125\u1E23\u1E27\u021F\u1E25\u1E29\u1E2B\u1E96\u0127\u2C68\u2C76\u0265]/g
	        },
	        { 'base': 'hv', 'letters': /[\u0195]/g },
	        {
	            'base': 'i',
	            'letters': /[\u0069\u24D8\uFF49\u00EC\u00ED\u00EE\u0129\u012B\u012D\u00EF\u1E2F\u1EC9\u01D0\u0209\u020B\u1ECB\u012F\u1E2D\u0268\u0131]/g
	        },
	        { 'base': 'j', 'letters': /[\u006A\u24D9\uFF4A\u0135\u01F0\u0249]/g },
	        {
	            'base': 'k',
	            'letters': /[\u006B\u24DA\uFF4B\u1E31\u01E9\u1E33\u0137\u1E35\u0199\u2C6A\uA741\uA743\uA745\uA7A3]/g
	        },
	        {
	            'base': 'l',
	            'letters': /[\u006C\u24DB\uFF4C\u0140\u013A\u013E\u1E37\u1E39\u013C\u1E3D\u1E3B\u017F\u0142\u019A\u026B\u2C61\uA749\uA781\uA747]/g
	        },
	        { 'base': 'lj', 'letters': /[\u01C9]/g },
	        { 'base': 'm', 'letters': /[\u006D\u24DC\uFF4D\u1E3F\u1E41\u1E43\u0271\u026F]/g },
	        {
	            'base': 'n',
	            'letters': /[\u006E\u24DD\uFF4E\u01F9\u0144\u00F1\u1E45\u0148\u1E47\u0146\u1E4B\u1E49\u019E\u0272\u0149\uA791\uA7A5]/g
	        },
	        { 'base': 'nj', 'letters': /[\u01CC]/g },
	        {
	            'base': 'o',
	            'letters': /[\u006F\u24DE\uFF4F\u00F2\u00F3\u00F4\u1ED3\u1ED1\u1ED7\u1ED5\u00F5\u1E4D\u022D\u1E4F\u014D\u1E51\u1E53\u014F\u022F\u0231\u00F6\u022B\u1ECF\u0151\u01D2\u020D\u020F\u01A1\u1EDD\u1EDB\u1EE1\u1EDF\u1EE3\u1ECD\u1ED9\u01EB\u01ED\u00F8\u01FF\u0254\uA74B\uA74D\u0275]/g
	        },
	        { 'base': 'oi', 'letters': /[\u01A3]/g },
	        { 'base': 'ou', 'letters': /[\u0223]/g },
	        { 'base': 'oo', 'letters': /[\uA74F]/g },
	        { 'base': 'p', 'letters': /[\u0070\u24DF\uFF50\u1E55\u1E57\u01A5\u1D7D\uA751\uA753\uA755]/g },
	        { 'base': 'q', 'letters': /[\u0071\u24E0\uFF51\u024B\uA757\uA759]/g },
	        {
	            'base': 'r',
	            'letters': /[\u0072\u24E1\uFF52\u0155\u1E59\u0159\u0211\u0213\u1E5B\u1E5D\u0157\u1E5F\u024D\u027D\uA75B\uA7A7\uA783]/g
	        },
	        {
	            'base': 's',
	            'letters': /[\u0073\u24E2\uFF53\u00DF\u015B\u1E65\u015D\u1E61\u0161\u1E67\u1E63\u1E69\u0219\u015F\u023F\uA7A9\uA785\u1E9B]/g
	        },
	        {
	            'base': 't',
	            'letters': /[\u0074\u24E3\uFF54\u1E6B\u1E97\u0165\u1E6D\u021B\u0163\u1E71\u1E6F\u0167\u01AD\u0288\u2C66\uA787]/g
	        },
	        { 'base': 'tz', 'letters': /[\uA729]/g },
	        {
	            'base': 'u',
	            'letters': /[\u0075\u24E4\uFF55\u00F9\u00FA\u00FB\u0169\u1E79\u016B\u1E7B\u016D\u00FC\u01DC\u01D8\u01D6\u01DA\u1EE7\u016F\u0171\u01D4\u0215\u0217\u01B0\u1EEB\u1EE9\u1EEF\u1EED\u1EF1\u1EE5\u1E73\u0173\u1E77\u1E75\u0289]/g
	        },
	        { 'base': 'v', 'letters': /[\u0076\u24E5\uFF56\u1E7D\u1E7F\u028B\uA75F\u028C]/g },
	        { 'base': 'vy', 'letters': /[\uA761]/g },
	        { 'base': 'w', 'letters': /[\u0077\u24E6\uFF57\u1E81\u1E83\u0175\u1E87\u1E85\u1E98\u1E89\u2C73]/g },
	        { 'base': 'x', 'letters': /[\u0078\u24E7\uFF58\u1E8B\u1E8D]/g },
	        {
	            'base': 'y',
	            'letters': /[\u0079\u24E8\uFF59\u1EF3\u00FD\u0177\u1EF9\u0233\u1E8F\u00FF\u1EF7\u1E99\u1EF5\u01B4\u024F\u1EFF]/g
	        },
	        {
	            'base': 'z',
	            'letters': /[\u007A\u24E9\uFF5A\u017A\u1E91\u017C\u017E\u1E93\u1E95\u01B6\u0225\u0240\u2C6C\uA763]/g
	        }
	    ];
	    var changes;

	    function removeDiacritics( str ) {
	        if( !changes ) {
	            changes = defaultDiacriticsRemovalMap;
	        }
	        for( var i = 0; i < changes.length; i++ ) {
	            str = str.replace( changes[ i ].letters, changes[ i ].base );
	        }
	        return str;
	    }

	    /**
	     * select2 for js-aione-entry-select2
	     */
	    jQuery( document ).ready( function( $ ) {
	        if( 'undefined' == typeof jQuery().select2 ) {
	            return;
	        }
	        $( '.js-aione-entry-select2' ).select2( {
	            id: function( item ) {
	                return item.ID;
	            },
	            allowClear: true,
	            placeholder: $( this ).data( 'placeholder' ),
	            minimumInputLength: 0,
	            formatInputTooShort: $( this ).data( 'input-too-short' ),
	            ajax: {
	                url: ajaxurl,
	                dataType: 'json',
	                quietMillis: 250,
	                data: function( term, page ) {
	                    return {
	                        action: 'aione_ajax',
	                        page: page || 1,
	                        post_type: $( this ).data( 'post-type' ),
	                        s: term, // search term
	                        aione_action: 'aione_entry_search',
	                        _wpnonce: $( this ).data( 'nonce-search' ),
	                    };
	                },
	                results: function( data, page ) {
	                    var more = (page * data.posts_per_page) < data.total_count;
	                    return { results: data.items, more: more };
	                },
	                cache: true
	            },
	            initSelection: function( element, callback ) {
	                var id = $( element ).val();
	                if( 0 < parseInt( id ) ) {
	                    $( element ).select2( "data", { ID: 0, post_title: $( element ).data( 'loading' ) } );
	                    $( element ).select2( "enable", false );
	                    $.ajax( {
	                        url: ajaxurl,
	                        dataType: "json",
	                        data: {
	                            action: 'aione_ajax',
	                            p: id,
	                            aione_action: 'aione_entry_entry',
	                            _wpnonce: element.data( 'nonce-entry' ),
	                        }
	                    } ).done( function( data ) {
	                        $( element ).select2( "enable", true );
	                        callback( data );
	                    } );
	                }
	            },
	            formatResult: function( item ) {
	                return '<div data-id="' + item.ID + '" class="item">' + item.post_title + '</div>';
	            },
	            formatSelection: function( item ) {
	                //var target = $('#aione_pr_belongs_'+item.parent_id+'_'+item.post_type);
	                //target.val(item.ID);
	                return item.post_title;
	            },
	        } );
	    } );


	    (function( $ ) {
	        $( document ).on( 'ready', function() {

	            /* Trigger Infos */
	            $( '[data-aione-toggle-trigger]' ).on( 'click', function( e ) {
	                e.preventDefault();

	                var trigger = $( this ),
	                    element = $( '[data-aione-toggle=' + trigger.data( 'aione-toggle-trigger' ) + ']' );

	                if( element.length ) {
	                    element.toggle();
	                    trigger.blur();

	                    if( trigger.hasClass( 'aione-arrow-right' )
	                        || trigger.hasClass( 'aione-arrow-down' ) ) {
	                        trigger.toggleClass( 'aione-arrow-right aione-arrow-down' );
	                    }
	                }
	            } );

	            /* CPT "Admin Menu position after:" select */
	            var cptPositionAfterSelect = $( 'select.js-aione-menu-position-after' );

	            if( cptPositionAfterSelect.length ) {
	                var cptPosition = cptPositionAfterSelect.data( 'aione-menu-position' );

	                // reselect option, if a position is selected but "--- not set ---" is active
	                // needed for backward compatibility and also if related post type was moved
	                if( cptPosition != '' && cptPositionAfterSelect.find( 'option:first' ).is( ':selected' ) ) {
	                    // old format - only integer
	                    if( typeof cptPosition === 'number' && cptPosition % 1 == 0 ) {
	                        $( 'option[value^=' + cptPosition + ']:first', cptPositionAfterSelect ).attr( 'selected', 'selected' );
	                        // new format
	                    } else {
	                        cptPosition = cptPosition.split( '--aione-add-menu-after--' );
	                        $( 'option[value*="' + cptPosition[ 1 ] + '"]:first', cptPositionAfterSelect ).attr( 'selected', 'selected' );
	                    }
	                }

	            }
	        } );


	    })( jQuery );

	}




	/**
     * setup title
     */
     jQuery( document ).ready( function( $ ) {
	    var labelPostType = $('#post-body-content .js-aione-slugize-source').val() != ''
	        ? $('#post-body-content .js-aione-slugize-source').val()
	        : $('#post-body-content .js-aione-slugize-source' ).data('anonymous-component');
	
	    $('.js-aione-singular').html( labelPostType );
	    $('#post-body-content').on('keyup input cut paste', '.js-aione-slugize-source', function() {
	        $('.js-aione-singular').html($(this).val());
	    });
	 });    
    /*
     * 
     * Submit form trigger
     */
    $('.aione-form').submit(function(){

        /**
         * do not check builtin post types
         */
        if ( '_builtin' == jQuery('.aione-form-submit', jQuery(this)).data('post_type_is_builtin') ) {
            return true;
        }
        /*
         * Check if singular and plural are same
         */
        if ( jQuery('#name-singular').val().length > 0 ) {
            if ( jQuery('#name-singular').val().toLowerCase() == jQuery('#name-plural').val().toLowerCase()) {
                if (jQuery('#aione_warning_same_as_slug input[type=checkbox]').is(':checked')) {
                    return true;
                }
                jQuery('#aione_warning_same_as_slug').fadeOut();
                alert(jQuery('#name-plural').data('aione_warning_same_as_slug'));
                jQuery('#name-plural').after(
                    '<div class="aione-error message updated" id="aione_warning_same_as_slug"><p>'
                    + jQuery('#name-plural').data('aione_warning_same_as_slug')
                    + '</p><p><input type="checkbox" name="ct[labels][ignore]" />'
                    + jQuery('#name-plural').data('aione_warning_same_as_slug_ignore')
                    + '</p></div>'
                    ).focus().bind('click', function(){
                        jQuery('#aione_warning_same_as_slug').fadeOut();
                    });
                aioneLoadingButtonStop();
                jQuery('html, body').animate({
                    scrollTop: 0
                }, 500);
                return false;
            }
            jQuery(this).removeClass('js-aione-do-not-show-modal');
        }

        /**
         * check for reserved names and already used slugs
         */
        return jQuery( this ).aioneProveSlug();
    });
    
    /**
     * choose icon
     */
    $( document ).on( 'click', '.js-aione-choose-icon', function() {
        var $thiz = $(this); 
        // show a spinner or something via css
        var dialog = $('<div style="display:none;height:450px;" class="aione-dashicons"><span class="spinner"></span>'+$thiz.data('aione-message-loading')+'</div>').appendTo('body');
        // open the dialog
        dialog.dialog({
            // add a close listener to prevent adding multiple divs to the document
            close: function(event, ui) {
                // remove div with all data and events
                dialog.remove();
            },
            dialogClass: 'aione-choose-icon aione-ui-dialog',
            modal: true,
            minWidth: 800,
            maxHeight: .9*$(window).height(),
            title: $thiz.data('aione-title'),
            position: { my: "center top+50", at: "center top", of: window },

        });
        // load remote content
        dialog.load(
            ajaxurl, 
            {
                action: 'aione_edit_post_get_icons_list',
                _wpnonce: $thiz.data('aione-nonce'),
                slug: $thiz.data('aione-value'),
                "aione-post-type": $thiz.data('aione-post-type'),
            },
            function (responseText, textStatus, XMLHttpRequest) {
            	console.log(responseText);
            	console.log(textStatus);
            	console.log(XMLHttpRequest);
                $(dialog).on('keyup input cut paste', '.js-aione-search', function() {
                    if ( '' == $(this).val() ) {
                        $('li', dialog).show();
                    } else {
                        var re = new RegExp($(this).val(), "i");
                        $('li', dialog).each(function(){
                            if ( !$(this).data('aione-icon').match(re) ) {
                                $(this).hide();
                            } else {
                                $(this).show();
                            }
                        });
                    }
                });
                $(dialog).on('click', 'a', function() {
                    var $icon = $(this).data('aione-icon');
                    $('#aione-icon').val($icon);
                    $thiz.data('aione-value', $icon);
                    var classes = 'aione-menu-image dashicons-before dashicons-'+$icon;
                    $('div.aione-menu-image').removeClass().addClass(classes);
                    dialog.dialog( "close" );
                    return false;
                });
            }
            );
        //prevent the browser to follow the link
        return false;
    });
    /**
     * post types
     */
    $(document).on( 'change', '.js-aione-relationship-checkbox', function() {
        var $value = $(this).data('aione-value');
        var $type = $(this).data('aione-type');

        if ( $(this).is(':checked') ) {
            $(this).parent().addClass('active');
            $('.js-aione-relationship-checkbox').each(function(){
                if ( $value == $(this).data('aione-value') && $type != $(this).data('aione-type') ) {
                    $(this).attr('disabled', 'disabled').parent().addClass('disabled');
                    $(this).closest('li').attr('title', $(this).data('aione-message-disabled'));
                }
            });
        } else {
            $(this).parent().removeClass('active');
            $('.js-aione-relationship-checkbox').each(function(){
                if ( $value == $(this).data('aione-value') ){
                    $(this).removeAttr('disabled').parent().removeClass('disabled');
                    $(this).closest('li').removeAttr('title');
                }
            });
        }
    });
    $('#relationship :disabled').each(function(){
        $(this).closest('li').attr( 'title', $(this).data('aione-message-disabled'));
    });
    /**
     * choose fields
     */
    $( document ).on( 'click', '.js-aione-edit-child-post-fields', function() {
        var $thiz = $(this);
        // show a spinner or something via css
        var dialog = $('<div style="display:none;height:450px;"><span class="spinner"></span>'+$thiz.data('aione-message-loading')+'</div>').appendTo('body');
        /**
         * params for dialog
         */
        var dialog_data = {
            // add a close listener to prevent adding multiple divs to the document
            close: function(event, ui) {
                // remove div with all data and events
                dialog.remove();
            },
            dialogClass: 'aione-child-post-fields-dialog aione-ui-dialog',
            modal: true,
            minWidth: 800,
            maxHeight: .9*$(window).height(),
            title: $thiz.data('aione-title'),
            position: { my: "center top+50", at: "center top", of: window },
            buttons: [{
                text: $thiz.data('aione-buttons-apply'),
                click: function() {
                    $.ajax({
                        url: ajaxurl,
                        method: "POST",
                        data: {
                            action: 'aione_edit_post_save_child_fields',
                            _wpnonce: $('#aione-fields-save-nonce').val(),
                            parent: $('#aione-parent').val(),
                            child: $('#aione-child').val(),
                            current: $(':input', dialog).serialize()
                        }
                    })
                    /**
                     * close dialog
                     */
                    $( this ).dialog( "close" );
                },
                class: 'button-primary'
            }, {
                text: $thiz.data('aione-buttons-cancel'),
                click: function() {
                    $( this ).dialog( "close" );
                },
                class: 'aione-ui-dialog-cancel'
            }]
        };
        /**
         * remove button apply
         */
        if ( 'new' == $thiz.data('aione-save-status') ) {
            dialog_data.buttons.shift();
            dialog_data.buttons[0].class = 'button-primary';
        }
        /**
         * open the dialog
         */
        dialog.dialog(dialog_data);
        // load remote content
        dialog.load(
            ajaxurl, 
            {
                action: 'aione_edit_post_get_child_fields_screen',
                _wpnonce: $thiz.data('aione-nonce'),
                parent: $thiz.data('aione-parent'),
                child: $thiz.data('aione-child'),
            },
            function (responseText, textStatus, XMLHttpRequest) {
                $(dialog).on('change', '.aione-form-radio', function() {
                    if ('specific' == $(this).val()) {
                        $('#aione-specific').slideDown();
                    } else {
                        $('#aione-specific').slideUp();
                    }
                });
            }
        );
        //prevent the browser to follow the link
        return false;
    });
    /**
     * update groups with type
     */
    $('#field_groups').on('change', '.js-aione-custom-fields-group', function(){ });

    /**
     * load column box
     */

    function aione_edit_post_get_child_fields_box_message_helper() {
        var $container = $('#aione-custom-field-message');
        if ( $('.js-aione-custom-field-order-container li').length ) {
            $container.html($container.data('aione-message-drag'));
        } else {
            $container.html('');
        }
    }

    var initGroupFields = 1;
    function aione_edit_post_get_child_fields_box() {
        var currentGroups = [],
            currentFields = [],
            target = $('#custom_fields .aione-box');

        if ( 0 == target.length )
            return;

        // current groups
        $('#field_groups .js-aione-custom-fields-group:checked').each(function(){
            currentGroups.push( $( this ).data( 'aione-group-id' ) );
        });

        // current fields (get them from sortables to have the right order)
        $( '.js-aione-custom-field-order-container li[id^="aione-custom-field"]' ).each( function() {
            currentFields.push( $( this ).attr( 'id' ).replace( 'aione-custom-field-', '' ) );

        } );

        target.load(
            ajaxurl,
            {
                action: 'aione_edit_post_get_fields_box',
                _wpnonce: target.data('aione-nonce'),
                id: target.data('aione-id'),
                type: target.data('aione-type'),
                current_groups: currentGroups,
                current_fields: currentFields,
                init: initGroupFields
            },
            function (responseText, textStatus, XMLHttpRequest) {
                initGroupFields = 0;
                $('#custom_fields .inside .aione-custom-field-group-container').masonry({
                    itemSelector: '.js-aione-custom-field-group',
                    columnWidth: 250
                });
                $("#custom_fields .aione-custom-field-order ul").sortable();
                $('.js-aione-custom-field-group-container').on('change', 'input', function() {
                    var $key = $(this).data('aione-key');
                    if ( $(this).is(':checked')) {
                        // only append field to sortable if it does not already exists
                        if( !$( '#custom_fields .aione-custom-field-order ul' ).find( '#aione-custom-field-'+$key ).length ) {
                            $('#custom_fields .aione-custom-field-order ul').append(
                                '<li class="menu-item-handle ui-sortable-handle" id="aione-custom-field-'+$key+'"><input type="hidden" name="ct[custom_fields]['+$key+']" value="1">'+ $('label', $(this).parent()).html()+ '</li>');
                        }

                        // check all other inputs with the same name
                        $( '[data-aione-key=' + $(this).data( 'aione-key' ) ).each( function() {
                            $( this ).attr( 'checked', 'checked' );
                        })
                    } else {
                        $('#aione-custom-field-'+$key).remove();

                        // uncheck all other inputs with the same name
                        $( '[data-aione-key=' + $(this).data( 'aione-key' ) ).each( function() {
                            $( this ).removeAttr( 'checked' );
                        })
                    }
                    aione_edit_post_get_child_fields_box_message_helper();
                });
                aione_edit_post_get_child_fields_box_message_helper();
            }
        );
    }
    aione_edit_post_get_child_fields_box();
    $('#field_groups').on( 'change', '.js-aione-custom-fields-group', function(){
        aione_edit_post_get_child_fields_box();
        return false;
    });


    jQuery( document ).ready( function( $ ) {
    	jQuery('#aione_reset_submit').click(function(){
			if ( 'reset' === jQuery('#aione_reset_confirm').val() ) {
				var message =  'This action is not reversable. Clicking OK will reset your database back to the defaults. Click Cancel to abort.',
					reset   = confirm( message );
				if ( reset ) {
					jQuery('#aione_reset_form').submit();
				} else {
					jQuery('#aione_reset').val('false');
					return false;
				}
			} else {
				alert( 'Invalid confirmation word. Please type the word reset in the confirmation field.' );
				return false;
			}
		} );
    });

	jQuery( document ).ready( function() {
    	jQuery('input[type="radio"]').click(function(){
    		if($(this).attr("value") == 'single') {
				jQuery('#types_structured_data_header').css('display', 'none');
				jQuery('#types_structured_data_footer').css('display', 'none');
			}
			else {
				jQuery('#types_structured_data_header').css('display', 'block');
				jQuery('#types_structured_data_footer').css('display', 'block');
			}
		} );
    });

    

})( jQuery );

