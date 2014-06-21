<?php
class Frontdesk extends Controller
{
	function Frontdesk(){
		parent::Controller();
		//$this->load->database('hotel',TRUE);
	}
	function index(){
		
	redirect("hospitalidad/frontdesk/filteredgrid");
	}

	function _fdpanel($ancho=500,$alto=300){
		$ancho_celda=22;
		$alto_celda=22;
		$mSQL='SELECT habit ';
		$fdtop='<div style="overflow-x: hidden;width: '.($ancho-15).'px; align: left" id="fdtop"><table><tr>';
		for($i=0;$i<31;$i++){
			$mSQL.=", sum(IF(fecha_in=ADDDATE(DATE(NOW()), $i), 10, IF(fecha_ou=ADDDATE(DATE(NOW()),$i),30,IF(fecha_in<ADDDATE(DATE(NOW()),$i) AND fecha_ou>=ADDDATE(DATE(NOW()), $i), IF(checkin=0,1+(confirma='S'),50), 0)))) D$i ";
			$date=mktime(0,0,0,date("n"),date("j")+$i,date("Y"));
			$fdtop.= '<td class="mininegro" align=center>'.image('spacer.gif','',array('width'=>$ancho_celda,'height'=>'1')).'<br>'.strftime("%d<br>%m",$date)."</td>\n";
		}
		$fdtop.= '</tr></table></div>';
		$mSQL.=" FROM hres 
			WHERE fecha_in <= ADDDATE(DATE(NOW()), 30) AND fecha_ou >= ADDDATE(DATE(NOW()), 0) AND checkout=0
			GROUP BY habit";
		$frontdesk='<div style="overflow: auto;width: '.$ancho.'px; height: '.$alto.'px;" id="frontdesk" OnScroll="hacer_scroll()"><table bgcolor=#E4E4E4>';
		$fdleft   ='<div style="overflow: hidden;width: 40px; height: '.($alto-15).'px; align: right" id="fdleft"><table>';
		$cursor = $this->db->query($mSQL);
		foreach( $cursor->result() as $row ){
			$frontdesk.='<tr>'; $i=0;
			foreach( $row  as $sal ){
				if ($i==0){
					$fdleft.="<tr><td>".image('spacer.gif','',array('width'=>1,'height'=>$alto_celda,'align'=>'left'))."</td><td class='mininegro'>$sal</td></tr>\n";
				}else{
					$iconos=array('width'=>$ancho_celda,'height'=>$alto_celda,'hspace'=>0 ,'vspace'=>0,'border'=>0);
					$spacer=array('width'=>$ancho_celda,'height'=>1,'hspace'=>0 ,'vspace'=>0,'border'=>0);
					$frontdesk.="<td bgcolor='#FFFFFF' style='height: ".$alto_celda."px; width: ".$ancho_celda."px; '>";
					if     ($sal==0 ) $frontdesk.=image('spacer.gif','Libre'    ,$iconos);
					elseif ($sal==1 ) $frontdesk.=image('RR.png'    ,'Reservado',$iconos);
					elseif ($sal==2 ) $frontdesk.=image('RD.png'    ,'Reservado con Deposito',$iconos);
					elseif ($sal==10) $frontdesk.=image('EE.png'    ,'Entrada',$iconos);
					elseif ($sal==30) $frontdesk.=image('SS.png'    ,'Salida' ,$iconos);
					elseif ($sal==40) $frontdesk.=image('ES.png'    ,'Entrada Salida',$iconos);
					elseif ($sal==50) $frontdesk.=image('OO.png'    ,'Ocupado' ,$iconos);
					$frontdesk.='</td>';
				}$i++;
			}$frontdesk.="</tr>\n";
		}$frontdesk.='</table></div>'; $fdleft.='</table></div>';
		
		$script="<script language='javascript'>
		function hacer_scroll() {
		document.getElementById('fdleft').scrollTop=document.getElementById('frontdesk').scrollTop;
		document.getElementById('fdtop').scrollLeft=document.getElementById('frontdesk').scrollLeft;
		}</script>";
		//echo $mSQL;
		$panel="$script <table>
			<tr>
				<td> </td>
				<td valign='top' align='left' >$fdtop</td>
			</tr>
			<tr>
				<td valign='top' align='right'>$fdleft</td>
				<td valign='top' align='left'>$frontdesk</td>
			</tr>
		</table>";
		return($panel);
	}
}
?>