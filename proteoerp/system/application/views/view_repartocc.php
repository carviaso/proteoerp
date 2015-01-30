<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
?>
<script language="javascript" type="text/javascript">
$(function(){

});

function selcli(cliente,numero,tipo,pago){
	$.get("<?php echo site_url('finanzas/smov/ccli'); ?>/"+cliente+"/create", function(data) {
		$("#fsclisel").html(data);

		$("[id^='tr_itccli_']").each(function(){
			nom=this.id;
			pos=this.id.lastIndexOf('_');
			if(pos>0){
				ind = this.id.substring(pos+1);

				nnumero= $("#numero_"+ind).val();
				ntipo  = $("#tipo_doc_"+ind).val();

				if(nnumero==numero && ntipo==tipo){
					$('#abono_'+ind).focus();
					if(pago=='CH'){
						$('#tipo_0').val('CH');
						$('#sfpafecha_0').val('<?php echo dbdate_to_human(date('Y-m-d')); ?>');
						$('#num_ref_0').focus();
					}
				}else{
					//$('#tr_itccli_'+ind).remove();
				}
			}
		});

		//$("[id^='tr_itccli_']").not("#tr_itccli_"+smovid).remove();
	});
}
</script>
<div style='text-align:center;background-color:#E4E4E4' class='ui-corner-all'>
<?php if(count($cheque)>0){ ?>
 <b>Cheques</b>: <?php foreach($cheque as $num){ echo ' '.$num; }?>
<?php
}
if(count($mixto)>0){ ?>
 <b>Mixtos</b>:  <?php foreach($mixto  as $num){ echo ' '.$num; } ?>
<?php } ?>
</div>
<div id='fsclisel'>

</div>
