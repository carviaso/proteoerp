function camclave(){
	$.get('<?php echo site_url('supervisor/usuarios/ccclave/'); ?>' ,
	function(data){
		$.prompt(data,{
			buttons: { Guardar: true, Cancelar: false },
			focus: 1,
			submit: function(e,v,m,f){
				if ( v == true ){
					if ( f.us_clave1 == f.us_clave ){
						$.ajax({
							type: "POST",
							url: '<?php echo site_url('supervisor/usuarios/ccclaveg');?>',	
							data: $("#fclave").serialize()
						}).done( function(msg){ 
							alert(msg);
						});
						//$('#fclave').submit();
				} else {
						m.children('#error').html("ERROR: Claves Diferentes!!! intente de nuevo...");
						return false;
					}
				}
			}
		});
	})
};


