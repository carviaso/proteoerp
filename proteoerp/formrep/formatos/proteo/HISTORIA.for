<?php
$maxlin=33; //Maximo de lineas de items.

if(count($parametros) < 0) show_error('Faltan parametros');
$id = $parametros[0];
$ultima = 0;
if (count($parametros) == 2 ) $ultima = $parametros[1];

$this->db->select("*");
$this->db->from('medhisto');
$this->db->where('id', $id);

$mSQL_1 = $this->db->get();
if($mSQL_1->num_rows()==0) show_error('Registro no encontrado');
$row       = $mSQL_1->row();
$numero    = $row->numero;
$ingreso   = dbdate_to_human($row->ingreso);
$nombre    = $row->nombre;
$papellido = $row->papellido;
$sapellido = $row->sapellido;
$nacional  = $row->nacional;
$cedula    = $row->cedula;
$sexo      = $row->sexo;
$nacio     = dbdate_to_human($row->nacio);
$estado    = $row->estado;
$ciudad    = $row->ciudad;
$ecivil    = $row->ecivil;
$ocupacion = $row->ocupacion;
$direccion = $row->direccion;
$telefono  = $row->telefono;
$referido  = $row->referido;
$email     = $row->email;
$usuario   = $row->usuario;
$estampa   = $row->estampa;
$hora      = $row->hora;
$edad      = $row->edad;
$id        = $row->id;

$ultima = $this->datasis->dameval("SELECT MAX(fecha) FROM medhvisita WHERE historia=$numero");

$mSQL="SELECT b.indice, b.nombre, a.fecha, a.descripcion
FROM medhvisita a JOIN medhtab b ON a.tabula=b.id
WHERE historia=${numero} AND fecha='${ultima}' ORDER BY b.indice";
$mSQL_2 = $this->db->query($mSQL);
$detalle  = $mSQL_2->result();


?><html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
<title>Comprobante de retenci&oacute;n de IVA <?php echo $numero ?></title>
<link rel="STYLESHEET" href="<?php echo $this->_direccion ?>/assets/default/css/formatos.css" type="text/css" >
</head>
<body>
<script type="text/php">
if ( isset($pdf) ) {

	$font = Font_Metrics::get_font("verdana");
	$size = 6;
	$color = array(0,0,0);
	$text_height = Font_Metrics::get_font_height($font, $size);

	$foot = $pdf->open_object();

	$w = $pdf->get_width();
	$h = $pdf->get_height();

	// Draw a line along the bottom
	$y = $h - $text_height - 24;
	$pdf->line(16, $y, $w - 16, $y, $color, 0.5);

	$pdf->close_object();
	$pdf->add_object($foot, "all");

	$text = "PP {PAGE_NUM} de {PAGE_COUNT}";

	// Center the text
	$width = Font_Metrics::get_text_width("PP 1 de 2", $font, $size);
	$pdf->page_text($w / 2 - $width / 2, $y, $text, $font, $size, $color);
}
</script>
<div id="section_header">
	<table style="width: 100%;" class="header">
		<tr>
			<td width=229 rowspan="2"><img src="<?php echo $this->_direccion ?>/images/logo.jpg" width="147" ></td>
			<td><h1 style="text-align: right"><?php echo $this->datasis->traevalor('TITULO1') ?></h1></td>
		</tr><tr>
			<td style="text-align: right">
			<div class="page" style="font-size: 7pt"><?php echo $this->datasis->traevalor('TITULO2').' '.$this->datasis->traevalor('TITULO3') ?><br>
				<b>RIF: <?php echo $this->datasis->traevalor('RIF') ?></b>
			</div>
			</td>
		</tr>
	</table>

	<div class="page" style="font-size: 7pt">
	<table style="width: 100%;" class="header">
		<tr>
			<td ><h1 style="text-align:left">HISTORIA MEDICA Nro. <?php echo $numero ?></h1></td>
			<td style="text-align:right;font-size:12pt;">FECHA DE INGRESO <b><?php echo $ingreso; ?></b></td>
		</tr>
	</table>
	</div>
</div>
<div id="body">
	<table style="width: 100%;font-size: 10pt;">
		<tr class="even_row" >
			<td style="font-size:0.7em;">01 APELLIDOS</td>
			<td><?php echo $papellido ?></td>
			<td style="font-size:0.7em;">02 NOMBRES</td>
			<td><?php echo $nombre ?></td>
		</tr>
		<tr class="odd_row">
			<td style="font-size:0.7em;">03 CEDULA</td>
			<td><?php echo $nacional.$cedula ?></td>
			<td style="font-size:0.7em;">04 SEXO</td>
			<td>
<?php 
			if ($sexo == 'M') echo 'MASCULINO'; 
			if ($sexo == 'F') echo 'FEMENINO'; 
			if ($sexo == 'O') echo 'OTRO'; 
			?>
</td>
		</tr>
		<tr class="even_row">
			<td style="font-size:0.7em;">05 FECHA DE NACIMIENTO</td>
			<td><?php echo $nacio ?></td>
			<td style="font-size:0.7em;">06 EDAD</td>
			<td><?php echo $this->datasis->edad($row->nacio)  ?></td>
		</tr>
		<tr class="odd_row">
			<td style="font-size:0.7em;">07 LUGAR DE NACIMIENTO</td>
			<td colspan='3'><?php echo $estado ?></td>
		</tr>
		<tr class="even_row">
			<td style="font-size:0.7em;">08 ESTADO CIVIL</td>
			<td><?php 
			if ($ecivil == 'C') echo 'CASADO(A)'; 
			if ($ecivil == 'S') echo 'SOLTERO(A)'; 
			if ($ecivil == 'D') echo 'DIVORCIADO(A)'; 
			if ($ecivil == 'V') echo 'VIUDO(A)'; 
			if ($ecivil == 'R') echo 'RELACION ESTABLE'; 

 ?></td>
			<td style="font-size:0.7em;">09 OCUPACION</td>
			<td><?php echo $ocupacion ?></td>
		</tr>
		<tr class="odd_row">
			<td style="font-size:0.7em;">10 DIRECCION</td>
			<td colspan='3'><?php echo $direccion ?></td>
		</tr>
		<tr class="even_row">
			<td style="font-size:0.7em;">11 TELEFONOS</td>
			<td><?php echo $telefono ?></td>
			<td style="font-size:0.7em;">** REFERIDO POR</td>
			<td><?php echo $referido ?></td>
		</tr>
	</table>

	<div class="page" style="font-size: 7pt">
	<table style="width: 100%;" class="header">
		<tr>
			<td ><h1 style="text-align:left">CONSULTA</h1></td>
			<td style="text-align:right;font-size:12pt;">FECHA <b><?php echo dbdate_to_human($ultima); ?></b></td>
		</tr>
	</table>
	</div>

	<table width='100%'>
<?php
$mod     = $clinea = false;
$npagina = true;
$i       = 0;

foreach ($detalle AS $items){ $i++;
?>
		<tr class="<?php if(!$mod) echo 'even_row'; else  echo 'odd_row'; ?>">
			<td style="text-align: left;"><?php echo $items->indice; ?></td>
			<td style="text-align: left;font-size:0.7em;"><?php echo $items->nombre; ?></td>
			<td style="text-align: left;"><?php echo $items->descripcion; ?></td>
		</tr>
<?php
	if ( $mod ) $mod = false; else $mod = true;
}
?>
	</table>

</div>
</body>
</html>


