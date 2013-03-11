	var mostrar  = 0;
	var umostrar = 0;
	var mlong    = 0;
	var mlate    = 0;
	
	setInterval (loaduLog, 300000); 
	setInterval (loadLog, 3000); 

	$('#colap').hide();

	$('#submitmsg').click(function(event){
		event.preventDefault();
		var muser     = $('#musuario').val();
		var clientmsg = $('#usermsg' ).val();
		$.post('<?php echo site_url('chat/chat/agregar');?>', {mensaje: clientmsg, usuario: muser});
		$('#usermsg').val('');
	});

	$("#chatmenu").click( function(){
		if ( mostrar == 0 ){
			$('#colap').show();
			mostrar = 1 ;
			loadLog(1);
		} else {
			$('#colap').hide();
			mostrar = 0 ;
		}
	});

	function loadLog(){
		var oldscrollHeight = $('#chatbox').attr('scrollHeight') - 10;
		if ( mostrar == 1 ) {
			$('#status').html('<?php echo img('images/msg.png');?>');
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('/chat/chat/actualizar');?>",
				success: function(msg){
					$('#chatbox').html(msg);
					mlong = msg.length;
					var newscrollHeight = $('#chatbox').attr('scrollHeight') - 10; //La altura del scroll después del pedido
					if(newscrollHeight > oldscrollHeight){
						$('#chatbox').animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll hacia el fondo del div
					}
				}
			});
		} else {
			mlate = mlate + 1;
			if ( mlate > 10 ) {
				mlate = 0;
				$.ajax({
					type: "POST",
					url: "<?php echo site_url('chat/chat/status');?>",
					success: function(msg){
						if( mlong == Number(msg) ) {
							$('#status').html('<?php echo img('images/msg.png');?>');
						} else {
							$('#status').html('<?php echo img('images/msg-alert.png');?>');
						}
					}
				});
			}
		}
	}

	$('#ucolap').hide();

	$("#ultlog").click( function(){
		if ( umostrar == 0 ){
			$('#ucolap').show();
			umostrar = 1 ;
			loaduLog();
		} else {
			$('#ucolap').hide();
			umostrar = 0 ;
		}
	});

	function loaduLog(){
		var oldscrollHeight = $('#ultbox').attr('scrollHeight') - 10;
		if (umostrar == 1) {
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('supervisor/logusu/ultimos');?>",
				success: function(msg){
					$('#ultbox').html(msg);
					var newscrollHeight = $('#ultbox').attr('scrollHeight') - 10; //La altura del scroll después del pedido
					if(newscrollHeight > oldscrollHeight){
						$('#ultbox').animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll hacia el fondo del div
					}
				}
			});
		}
	}



