jQuery(document).ready(function(jQuery) {
    jQuery('.upload_gallery_button').click(function(event){
        var current_gallery = jQuery( this ).closest( 'label' );
 
        if ( event.currentTarget.id === 'clear-gallery' ) {
            //remove value from input
            current_gallery.find( '.gallery_values' ).val( '' ).trigger( 'change' );
 
            //remove preview images
            current_gallery.find( '.gallery-screenshot' ).html( '' );
            return;
        }
 
        // Make sure the media gallery API exists
        if ( typeof wp === 'undefined' || !wp.media || !wp.media.gallery ) {
            return;
        }
        event.preventDefault();
 
        // Activate the media editor
        var val = current_gallery.find( '.gallery_values' ).val();
        var final;
 
        if ( !val ) {
            final = '[ gallery ids="0" ]';
        } else {
            final = '[ gallery ids="' + val + '" ]';
        }
        var frame = wp.media.gallery.edit( final );
 
        frame.state( 'gallery-edit' ).on(
            'update', function( selection ) {
 
                //clear screenshot div so we can append new selected images
                current_gallery.find( '.gallery-screenshot' ).html( '' );
 
                var element, preview_html = '', preview_img;
                var ids = selection.models.map(
                    function( e ) {
                        element = e.toJSON();
                        preview_img = typeof element.sizes.thumbnail !== 'undefined' ? element.sizes.thumbnail.url : element.url;
                        preview_html = "<div class='screen-thumb'><img src='" + preview_img + "'/></div>";
                        current_gallery.find( '.gallery-screenshot' ).append( preview_html );
                        return e.id;
                    }
                );
 
                current_gallery.find( '.gallery_values' ).val( ids.join( ',' ) ).trigger( 'change' );
            }
        );
        return false;
    });
});