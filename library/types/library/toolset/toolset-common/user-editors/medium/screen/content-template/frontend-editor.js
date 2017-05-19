/**
 * Frontend script for the Content Template editor for Beaver Builder.
 * This controls the changes in the post preview as well as redirection on exit.
 *
 * @summary Frontend Content Template editor manager for Beaver Builder compatibility,.
 *
 * @since 2.3.0
 * @requires jquery.js
 */

/* globals jQuery, toolset_user_editors */

var ToolsetCommon			= ToolsetCommon || {};
ToolsetCommon.UserEditor	= ToolsetCommon.UserEditor || {};

ToolsetCommon.UserEditor.BeaverBuilderFrontendEditor = function( $ ) {

	var self = this;
	
	self.extraContainer = $( '.js-toolset-editors-frontend-editor-extra' );
	
    self.previewPostSelector = $( '#wpv-ct-preview-post' );
	
	self.setExitUrl		= function() {
		FLBuilder._exitUrl = toolset_user_editors.mediumUrl;
	};
	
	$( window ).load( self.setExitUrl );
	$( document ).on( 'click', '.fl-builder-save-actions .fl-builder-publish-button', self.setExitUrl );
	$( document ).on( 'click', '.fl-builder-save-actions .fl-builder-discard-button', self.setExitUrl );
	$( document ).on( 'click', '.fl-builder-save-actions .fl-builder-cancel-button', self.setExitUrl );
	
	self.previewPostSelector.on( 'change', function() {
        $.ajax( {
            type: 'post',
            dataType: 'json',
            url: ajaxurl,
            data: {
                action: 'set_preview_post',
                ct_id: toolset_user_editors.mediumId,
                preview_post_id: this.value,
                nonce: toolset_user_editors.nonce
            },
            complete: function() {
                FLBuilder._updateLayout();
            }
        } );
    } );
	
	self.init = function() {
		self.extraContainer.insertBefore( '.fl-builder-bar-actions' );
		self.extraContainer.show();
		FLBuilder._updateLayout();
    };
	
	self.init();
	
};

jQuery( document ).ready( function( $ ) {
	ToolsetCommon.UserEditor.BeaverBuilderFrontendEditorInstance = new ToolsetCommon.UserEditor.BeaverBuilderFrontendEditor( $ );
});