;( function( $ ) {

    $( document ).on( 'click', '[data-' + toolset_admin_notices.triggerNoticeDismissible + ']', function() {
        var notice = $( this ).parent( '.toolset-notice-wp' );
        var ajaxRequestData = {};
        ajaxRequestData['action'] = toolset_admin_notices.action;
        ajaxRequestData[toolset_admin_notices.varnameNonce] = toolset_admin_notices.nonce;
        ajaxRequestData[toolset_admin_notices.varnameAction] = toolset_admin_notices.triggerNoticeDismissible;
        ajaxRequestData[toolset_admin_notices.varnameNoticeId] = $( this ).data( toolset_admin_notices.triggerNoticeDismissible );

        $.ajax( {
            url: ajaxurl,
            method: 'POST',
            data: ajaxRequestData
        } ).done( function( ajaxResponseData ) {
            notice.fadeTo( 100, 0, function() {
                notice.slideUp( 100, function() {
                    notice.remove();
                });
            });
        } );
    } );

} ( jQuery ) );