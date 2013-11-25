//function roundNumber(num, dec) {
//	var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
//	return result;
//}

//****************************************************
//dec: 0=decimal, 1 = unidades, 2 =decenas, 3=centenas
//****************************************************
function roundSup(num, dec) {
	num=num-0.01;
	num=roundNumber(num,2);

	if(dec==0)
		result = Math.ceil(num);
	else{
		result = num.toString();
		elemento=result.length-3-dec;
		previo="";
		for (i=elemento;i<result.length;i++)
		 previo=previo+result[i];
		factor=5*(Math.pow(10,dec)/10);
		previo=parseFloat(previo);

		if(previo<=factor)
			diferencia=factor-previo;
		else
			diferencia=(2*factor)-previo;

		result=num+diferencia
	}
	return result;
}

function cost(pertenece){
	if(pertenece=='M'){
		var fcalc =$("#fcalc").val();
		var costo=parseFloat($("#costo").val());
	}else{
		var fcalc =$("#formcal").val();
		var costo=parseFloat($("#pond").val());
	}

	var ultimo=parseFloat($("#ultimo").val());

	if(fcalc=="P"){
		ccosto=costo;
	}else if(fcalc=="U"){
		ccosto=ultimo;
	}else if(fcalc=="S"){
		ccosto=parseFloat($("#standard").val());
	}else{
		if (ultimo>costo)
			ccosto=ultimo;
		else
			ccosto=costo;
	}
	return ccosto;
}

//Calcula los precios respetando el margen
function calculos(pertenece){
 	if (pertenece=='M') v=6; else v=5;
	var iva  = parseFloat($("#iva").val());
	var costo= cost(pertenece);
	for(i=1;i<v;i++){
		margen=parseFloat($("#margen"+i).val());
		if(margen>=100){ margen=99.99; }
		if(margen=='' || margen==null){
			nbase=nprecio=0;
		}else{
			nmargen = roundNumber(margen,2);
			nbase   = roundNumber(costo*100/(100-margen),2);
			nprecio = roundNumber(nbase*((iva+100)/100),2);
		}
		$("#base" + i).val(nbase);
		$("#precio" + i).val(nprecio);
	}
}

//Calcula el margen respetando el precio
function cambioprecio(pertenece){
	var i=0;
	var costo=cost(pertenece);
	var iva=parseFloat($("#iva").val());
	if(pertenece=='M') v=6; else v=5;
	for(i=1;i<v;i++){
		precio=parseFloat($("#precio"+i).val());
		base=precio*100/(100+iva);
		if(base!=0){
			nbase=roundNumber(base,2);
			margen=100-(costo*100)/nbase;
			nmargen=roundNumber(margen,2);
		}else{
			nmargen=nbase=0;
		}
		$("#base"+i).val(nbase);
		$("#margen" + i).val(nmargen);
	}
}

//Calcula el margen respetando la base
function cambiobase(pertenece){
	var i=0;
	var costo=cost(pertenece);
	var iva=parseFloat($("#iva").val());
	if(pertenece=='M') v=6; else v=5;
	for(i=1;i<v;i++){
		base=parseFloat($("#base"+i).val());
		if(base!=0){
			precio=(base*(iva+100)/100);
			nprecio=roundNumber(precio,2);

			margen=100-(costo*100)/base;
			nmargen=roundNumber(margen,2);
			document.getElementById("margen"+i).value = nmargen;
		}else{
			nmargen=nprecio=0;
		}
		$("#margen" + i).val(nmargen);
		$("#precio"+i).val(nprecio);
	}
}

function redon(pertenece){
	var redondeo =$("#redecen").val();
	var i=0;
	var costo=cost(pertenece);
 	var iva=parseFloat($("#iva").val());
 	if(redondeo!="NO"){
		if(redondeo=="D"){
			for(i=1;i<5;i++){
				precio=parseFloat($("#precio"+i).val());
				base=parseFloat($("#base"+i).val());
				margen=parseFloat($("#margen"+i).val());
				if (precio >10){
					nprecio=roundSup(precio,2);
					//nprecio=Math.ceil(precio);
					nprecio=roundNumber(nprecio,2);
			     	base=nprecio*100/(100+iva);
			     	nbase=roundNumber(base,2);
			     	margen=100-(costo*100/nbase);
			     	nmargen=roundNumber(margen,2);
					$("#base" + i).val(nbase);
					$("#precio" + i).val(nprecio);
					$("#margen" + i).val(nmargen);
				}
			}
		}else if(redondeo=="F"){
			for(i=1;i<5;i++){
				precio=parseFloat($("#precio"+i).val());
				base=parseFloat($("#base"+i).val());
				margen=parseFloat($("#margen"+i).val());
				if (precio!=0){
					nprecio=Math.round(precio);
					nprecio=roundNumber(nprecio,2);
					base=nprecio*100/(100+iva);
					nbase=roundNumber(base,2);
					margen=100-(costo*100/nbase);
					nmargen=roundNumber(margen,2);
					$("#base" + i).val(nbase);
					$("#precio" + i).val(nprecio);
					$("#margen" + i).val(nmargen);
				}
			}
		}else {
			for(i=1;i<5;i++){
				precio=parseFloat($("#precio"+i).val());
				base=parseFloat($("#base"+i).val());
				margen=parseFloat($("#margen"+i).val());
				if (precio >100){
					nprecio= roundSup(precio,3);
					//nprecio=roundNumber(nprecio,2);
					base=nprecio*100/(100+iva);
					nbase=roundNumber(base,2);
					margen=100-(costo*100/nbase);
					nmargen=roundNumber(margen,2);
					$("#base" + i).val(nbase);
					$("#precio" + i).val(nprecio);
					$("#margen" + i).val(nmargen);
				}
			}
		}
	}
}

function redonde(pertenece){
	var redondeo =$("#redondeo").val();
	var i=0;
	var dec=parseInt(redondeo[1]);
	var costo=cost(pertenece);
	var iva=parseFloat($("#iva").val());
	if(redondeo!="NO"){
		 if(redondeo[0]=="P"){
		 	for(i=1;i<6;i++){
		 		precio=parseFloat($("#precio"+i).val());
		 		if (precio!=0){
					nprecio=roundSup(precio, dec);
					nprecio=roundNumber(nprecio,2);
					base=nprecio*100/(100+iva);
					nbase=roundNumber(base,2);
					margen=100-(costo*100/nbase);
					nmargen=roundNumber(margen,2);
					$("#base" + i).val(nbase);
					$("#precio" + i).val(nprecio);
					$("#margen" + i).val(nmargen);
				}
			}
		}else if(redondeo[0]=="B"){
			for(i=1;i<6;i++){
				base=parseFloat($("#base"+i).val());
				if (precio!=0){
					nbase=roundSup(base, dec);
					nbase=roundNumber(nbase,2);
					precio=(nbase*(iva+100)/100);
					nprecio=roundNumber(precio,2);
					margen=100-(costo*100/nbase);
					nmargen=roundNumber(margen,2);
					$("#base" + i).val(nbase);
					$("#precio" + i).val(nprecio);
					$("#margen" + i).val(nmargen);
				}
			}
		}
	}
}

function requeridos(load){
	var ultimo = Number($("#ultimo").val());
	var pond   = Number($("#pond").val());

	switch($("#formcal").val()){
		case 'U':{
			if(ultimo > 0){
				//bloquea_precios(false);
			}else{
				//bloquea_precios(true);
				if(!load)
				alert("Si en Forma de cálculo selecciona ULTIMO, debe de completar el valor del campo ULTIMO con un valor válido");
				$('#ultimo').focus();
				$('#ultimo').select();
			}
			break;
		}
		case 'P':{

			if (pond > 0){
				//bloquea_precios(false);
			}else{
				//bloquea_precios(true);
				if(!load)
				alert("Si en Forma de cálculo selecciona PROMEDIO, debe de completar el valor del campo PROMEDIO con un valor válido");
				$('#pond').focus();
				$('#pond').select();
			}
			break;
		}
		case 'M':{

			if ((ultimo > 0) || (pond > 0)){
				//bloquea_precios(false);
			}else{
				//bloquea_precios(true);
				if(!load)
				alert("Si en Forma de cálculo selecciona MAYOR, debe de completar el valor del campo PROMEDIO ó ULTIMO con un valor válido");
				if(pond > ultimo){
					$('#pond').focus();
					$('#pond').select();
				}else{
					$('#ultimo').focus();
					$('#ultimo').select();
				}
			}
			break;
		}
		default:{
			//bloquea_precios(false);
		}
	}
}

function bloquea_precios(ban){
	if(ban){
		$('input[id^="margen"]').attr('disabled','disabled');
		$('input[id^="base"]'  ).attr('disabled','disabled');
		$('input[id^="precio"]').attr('disabled','disabled');
	}else{
		$('input[id^="margen"]').removeAttr('disabled');
		$('input[id^="base"]'  ).removeAttr('disabled');
		$('input[id^="precio"]').removeAttr('disabled');
	}
}
