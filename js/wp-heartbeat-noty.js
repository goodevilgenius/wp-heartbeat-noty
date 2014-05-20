jQuery(document).ready( function($) {
	
	var blabla;
	
	// Bum bum
    $(document).on( 'heartbeat-tick.my_tick', function( e, data ) {
        
        // To understand better how it works just uncomment following lines and give a look at browser console
        noty('tick tock');
		console.log(data);
        
        if ( !data['message'] )
        	return;

		$.each( data['message'], function( index, notification ) {
			
			if ( index != blabla ){
				var configs = {text: '<h1>' + notification['title'] +'</h1><p>' + notification['content'] + '</p>', type: notification['type'] };
				$.extend(configs, notification);
			
				var n = noty( configs );
				
			}
			blabla = index;
			
		} ) ;
        

    });
    
});
