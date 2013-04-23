<?php require_once('dompdf_config.inc.php');

class cidompdf {
	var $paper;
	var $orien;

	function cidompdf(){
		$this->paper = 'letter';
		$this->orien = 'portrait'; //landscape
	}

	function html2pdf($html,$nombre='formato.pdf',$attach=false){
		$dompdf = new DOMPDF();
		if(preg_match('/<!\-\-\@size_paper (?P<x>[0-9\.]+)x(?P<y>[0-9\.]+)\-\->/', $html, $matches)){
			$x = $matches['x']*72/25.4;
			$y = $matches['y']*72/25.4;
			$dompdf->set_paper(array(0,0,$x,$y), $this->orien);
		}else{
			$dompdf->set_paper($this->paper, $this->orien);
		}

		$dompdf->load_html($html);
		$dompdf->render();
		$dompdf->stream($nombre, array('Attachment' => $attach));
	}
}
