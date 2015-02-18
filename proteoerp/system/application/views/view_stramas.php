<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
?>
<script language="javascript" type="text/javascript">
var itstra_cont=0;

var htm ="<tr id='tr_itstra_<#i#>'>";
    htm =htm+"<td valign='center' style='background:#F5FFFA;text-align:center'><a style='text-decoration:none' href='#' title='Eliminar' onclick='del_itstra(\"<#i#>\");return false;'><span style='color:red;font-wigth:bold;text-decoration:none;font-family:verdana;font-size:1.3em'>X</span></a></td>";
    htm =htm+"<td><input type='text' name='codigo_<#i#>' id='codigo_<#i#>'><p id='descrip_val_<#i#>' style='margin:0;padding:0;border:0'></p></td>";
    htm =htm+"<td><span id='existen_val_<#i#>'></span><input type='hidden' name='existen_<#i#>' id='existen_<#i#>'><input type='hidden' name='descrip_<#i#>' id='descrip_<#i#>'></td>";
	htm =htm+"</tr>";

var htc = "<td title='<#title#>' style='text-align:center;'><input type='text' style='text-align:right' size='5' name='<#idnom#>' id='<#idnom#>'  onfocus='focusexist(this,<#i#>)'></td>";

function focusexist(obj,id){
	var can     = id.toString();
	var existen = Number($('#existen_'+can).val());
	if(existen>0){
		var sumexis = totalrow(can);
		var diff    = existen-sumexis;
		var val     = obj.value;
		if(diff>=0){
			if(val==''){
				obj.value   = diff;
			}
			$(obj).select();
			//$(obj).css('background','transparent');
			$(obj).css('background','white');
		}else{
			$(obj).css('background','red');
			$(obj).one( 'focusout', function (rel){ $('#'+obj.id).css('background','transparent'); } );
		}
	}
}

function totalrow(can){
	total = 0;
	$("#caubcol col").each(function(){
		if(this.title!=''){
			idnom  = this.title+"_"+can;
			valor  = Number($('#'+idnom).val());
			total  = total+valor;
		}
	});
	return total;
}

function add_itstra(){
	var can = itstra_cont.toString();
	var con = (itstra_cont+1).toString();
	var html = htm.replace(/<#i#>/g,can);
	html = html.replace(/<#o#>/g,con);
	$("#_PTPL_").after(html);
	$("#codigo_"+can).focus();
	autocod(can);
	$("#cantidad_"+can).keypress(function(e) {
		if(e.keyCode == 13) {
		    add_itstra();
			return false;
		}
	});
	itstra_cont=itstra_cont+1;

	//Agrega las columnas de los almacenes
	$("#caubcol col").each(function(){
		if(this.title!=''){
			idnom  = this.title+"_"+can;
			cont = htc;
			cont = cont.replace(/<#title#>/g,this.title);
			cont = cont.replace(/<#idnom#>/g,idnom);
			cont = cont.replace(/<#i#>/g,can);
			$('#tr_itstra_'+can).append(cont);
		}
	});
}

function add_caub(caub){
	var cont ='';
	var idnom='';
	var col  ='';
	if(caub.checked){
		$("#itstras tr").each(function(){
			var id  = this.id;
			if(id=='_PTPL_'){
				cont="<th style='background:#E4E4E4;' title='"+caub.value+"'>"+caub.value+"</th>";
			}else{
				pos=id.lastIndexOf('_');
				if(pos>0){
					ind    = id.substring(pos+1);
					idnom  = caub.value+"_"+ind;
					cont   = htc;
					cont   = cont.replace(/<#title#>/g,caub.value);
					cont   = cont.replace(/<#idnom#>/g,idnom);
					cont   = cont.replace(/<#i#>/g,ind);

					col    = "<col style='background:#FFFFE0;' title='"+caub.value+"'>";
				}else{
					cont="";
				}
			}
			$(this).append(cont);
			$("#"+idnom).numeric(".");
		});
		if(col!=''){
			$('#caubcol').append(col);
		}
	}else{
		$("#itstras [title='"+caub.value+"']").remove();
		$("col [title='"+caub.value+"']").remove();
	}
}

//Agrega el autocomplete
function autocod(id){
	$('#codigo_'+id).autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ajax/buscasinvart/N'); ?>",
				type: 'POST',
				dataType: 'json',
				data: {"q":req.term,"alma":$('select[name="envia"]').val()},
				success:
					function(data){
						var sugiere = [];
						$.each(data,
							function(i, val){
								sugiere.push( val );
							}
						);
						add(sugiere);
					},
			})
		},
		open: function(){
			$('.ui-autocomplete').css('width', '400px');
		},
		minLength: 2,
		select: function( event, ui ) {
			$('#codigo_'+id).attr('readonly','readonly');

			$('#codigo_'+id).val(ui.item.codigo);
			$('#descrip_'+id).val(ui.item.descrip);
			$('#descrip_val_'+id).text(ui.item.descrip);
			$('#existen_'+id).val(ui.item.existen);
			$('#existen_val_'+id).text(ui.item.existen);
			//post_modbus(id);
			buscarep(id,ui.item.codigo);
			setTimeout(function(){ $('#codigo_'+id).removeAttr('readonly'); }, 1500);
		}
	}).data( "ui-autocomplete" )._renderItem = function( ul, item ){
		return $( "<li>" )
		.append( "<a><table style='width:100%;border-collapse:collapse;padding:0px;'><tr><td colspan='6' style='font-size:12px;color:#0B0B61;'><b>" + item.descrip + "</b></td></tr><tr><td>Codigo:</td><td>" + item.codigo + "</td><td>Precio: </td><td><b>" + item.base1 + "</b></td><td>Existencia:</td><td style='text-align:right'><b>" + item.existen + "</b></td><td></td></tr></table></a>" )
		.appendTo( ul );
	};
}

function buscarep(id,codigo){
    codigo=codigo.trim();
    var arr=$('input[name^="codigo_"]');
    jQuery.each(arr, function() {
        nom=this.name
        pos=this.name.lastIndexOf('_');
        if(pos>0){
            if(this.value!=''){
                ind     = this.name.substring(pos+1);
                if(ind!=id){
                    itcodigo= this.value.trim();
                    if(itcodigo==codigo){
                        alert('El codigo introducido ya esta repetido ('+codigo+')');
                        $('#codigo_'+ind).focus();
                        $('#codigo_'+ind).select();
                        $('#tr_itstra_'+id).css("background-color", "#FFFF28");
                    }
                }
            }
        }
    });
}

function del_itstra(id){
	id = id.toString();
	$('#tr_itstra_'+id).remove();
}

function valida(){
	var rt=true;
	$("#itstras tr").each(function(){
		var id  = this.id;
		if(id!='_PTPL_'){
			pos=id.lastIndexOf('_');
			if(pos>0){
				ind    = id.substring(pos+1);
				exist  = Number($("#existen_"+ind).val());
				tota   = totalrow(ind);
				if(tota>exist){
					rt=false;
					$('#existen_val_'+ind).css('color','red');
				}else{
					$('#existen_val_'+ind).css('color','black');
				}
			}
		}
	});
	return rt;
}

$(function(){
	add_itstra();
});
</script>

<form id='df1' action='<?php echo site_url('inventario/stra/masspros/insert');?>'>
<div>
<?php
$check   = array();
$options = array(''=>'Seleccionar');
$query = $this->db->query("SELECT TRIM(ubica) AS ubica,ubides FROM caub WHERE gasto='N' AND invfis='N' ORDER BY ubides");
if ($query->num_rows() > 0){
	foreach ($query->result() as $i=>$row){
		$color= ($i%2==0)? '#9ACD32':'#FAF0E6';
		$options[$row->ubica]=trim($row->ubides);
		$check[] = '<nobr style="background-color:'.$color.';">'.$row->ubica.' '.form_checkbox('caub[]', $row->ubica, false,'onclick="add_caub(this)" id="'.addslashes('c'.$row->ubica).'"').'</nobr>';
	}
}

echo 'Almac&eacute;n que envia: '.form_dropdown('envia', $options,trim($this->datasis->traevalor('ALMACEN')));
echo '<p style="font-size:0.8em">'.implode(' ',$check).'</p>';
?>
<table style='' id='itstras'>
	<colgroup id='caubcol'>
		<col span='3'>
	</colgroup>
	<tr id='_PTPL_'>
		<th style='background:#F5FFFA;'><a href='#' id='addlink' onclick='add_itstra()' title='Agregar otro producto'><?php echo img(array('src' =>"images/agrega4.png", 'height' => 18, 'alt'=>'Agregar otro producto', 'title' => 'Agregar otro producto', 'border'=>'0')); ?></a></th>
		<th style='background:#E4E4E4;'>Producto</th>
		<th style='background:#E4E4E4;'>Disp.</th>
	</tr>
</table>
</div>
</form>
