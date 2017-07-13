var last_update_received = 0;
function chatroom_check_updates() {
	jQuery.post(
		ajaxurl,
		{
			action: 'check_updates',
			chatroom_slug: chatroom_slug,
			last_update_id: last_update_id
		},
		function (response) {
			chats = jQuery.parseJSON( response );
			if ( chats !== null ) {
				for ( i = 0; i < chats.length; i++ ) {
					if ( jQuery('div.chat-container div.chat-message-'+chats[i].id).length )
						continue;
					jQuery('div.chat-container').html( jQuery('div.chat-container').html() + chatroom_strip_slashes(chats[i].html) );
					last_update_id = chats[i].id;
					jQuery('div.chat-container').animate({ scrollTop: jQuery('div.chat-container')[0].scrollHeight - jQuery('div.chat-container').height() }, 100);
				}
			}
		}
	);
	setTimeout( "chatroom_check_updates()", 1000 );
}

function chatroom_strip_slashes(str) {
    return (str + '').replace(/\\(.?)/g, function (s, n1) {
        switch (n1) {
        case '\\':
            return '\\';
        case '0':
            return '\u0000';
        case '':
            return '';
        default:
            return n1;
        }
    });
}

jQuery(document).ready( function() {
	last_update_id = 0;
	chatroom_check_updates();
	user_join_room();
	current_user_room();
	user_kicked();
	jQuery( 'textarea.chat-text-entry' ).keypress( function( event ) {
		if ( event.charCode == 13 || event.keyCode == 13 ) {
			chatroom_send_message();
			return false;
		}
	});
});

function chatroom_send_message() {
	message = jQuery( 'textarea.chat-text-entry' ).val();
	jQuery( 'textarea.chat-text-entry' ).val('');
	jQuery.post(
		ajaxurl,
		{
			action: 'send_message',
			chatroom_slug: chatroom_slug,
			message: message
		},
		function (response) {
		}
	);

}

function user_join_room() {
	jQuery.post(
		ajaxurl,
		{
			action: 'join_room',
			roomId: chatroom_id,
			userId: user_id,
		},
		function (response) {
		}
	);
	setTimeout( "user_join_room()", 60000 );
}

function current_user_room() {
	jQuery.post(
		ajaxurl,
		{
			action: 'current_user',
			roomId: chatroom_id,
			userId: user_id,
		},
		function (response) {
			jQuery('#current-user-table').html(response);
		}
	);
	setTimeout( "current_user_room()", 5000 );
}

function user_kicked() {
	jQuery.post(
		ajaxurl,
		{
			action: 'kicked_from',
			roomId: chatroom_id,
			userId: user_id,
		},
		function (response) {
			jQuery('#message').html(response);
		}
	);
	setTimeout( "user_kicked()", 5000 );
}