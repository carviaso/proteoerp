<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Validation extends CI_Validation{

	var $_dataobject;


	/**
	 * Unique
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function unique($str){
		return ( $this->_dataobject->is_unique($this->_current_field, $str) );
	}

	/**
	 * captcha
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	 */
	function captcha($str){
		return ( strtolower($_SESSION['captcha']) == strtolower($str) );
	}

	function alpha_dash_slash($str){
		$this->set_message('alpha_dash_slash', 'El campo %s contiene caracteres no permitidos');
		return (!preg_match('@^([-/a-z0-9_-])+$@i', $str))? false : true;
	}

	function valid_email($email){
		$email=trim($email);
		if(strlen($email)>0)
			return parent::valid_email($email);
		else
			return true;
	}

	function chci($rifci){
		if($this->CI->datasis->traevalor('VELCED')=='N'){
			return true;
		}
		if (preg_match("/((^[VEJG][0-9]+[[:blank:]]*$)|(^[P][A-Z0-9]+[[:blank:]]*$))|(^[[:blank:]]*$)/", $rifci)>0){
			return true;
		}else {
			$this->set_message('chci', "El campo <b>%s</b> debe tener el siguiente formato V=Venezolano(a), E=Extranjero(a), G=Gobierno, P=Pasaporte o J=Juridico Como primer caracter seguido del n&uacute;mero de documento. Ej: V123456, J5555555, P56H454");
			return false;
		}
	}

	function chrif($rif){
		if($this->CI->datasis->traevalor('VELCED')=='N'){
			return true;
		}
		if (preg_match("/(^[VEJG][0-9]{9}[[:blank:]]*$)|(^[[:blank:]]*$)/", $rif)>0){
			return true;
		}else {
			$this->set_message('chrif', "El campo <b>%s</b> debe tener el siguiente formato V=Venezolano(a), G=Gobierno, J=Juridico Como primer caracter seguido del n&uacute;mero de documento. Ej: V123456789, J123456789");
			return false;
		}
	}

	function existecaub($cuenta){
		$cuenta  =trim($cuenta);
		if(strlen($cuenta)==0) return true;
		$dbcuenta=$this->CI->db->escape($cuenta);
		$mSQL = "SELECT COUNT(*) AS cana FROM caub WHERE ubica=${dbcuenta}";
		$this->set_message('existecaub', 'El almacen introducido en el campo %s no es v&aacute;lido');

		$query = $this->CI->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row();
			if($row->cana>0) return true; else return false;
		}else{
			return false;
		}
	}

	function existevend($cuenta){
		$cuenta  =trim($cuenta);
		if(strlen($cuenta)==0) return true;
		$dbcuenta=$this->CI->db->escape($cuenta);
		$mSQL = "SELECT COUNT(*) AS cana FROM vend WHERE vendedor=${dbcuenta}";
		$this->set_message('existevend', 'El vendedor introducido en el campo %s no es v&aacute;lido');

		$query = $this->CI->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row();
			if($row->cana>0) return true; else return false;
		}else{
			return false;
		}
	}

	function existepers($pers){
		$dbpers=$this->CI->db->escape($pers);
		$mSQL = "SELECT COUNT(*) AS cana FROM pers WHERE codigo=${dbpers}";
		$this->set_message('existepers', 'El trabajador introducido en el campo %s no es v&aacute;lido');

		$query = $this->CI->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row();
			if($row->cana>0) return true; else return false;
		}else{
			return false;
		}
	}

	function existesucu($cuenta){
		$cuenta  =trim($cuenta);
		if(strlen($cuenta)==0) return true;
		$dbcuenta=$this->CI->db->escape($cuenta);
		$mSQL = "SELECT COUNT(*) AS cana FROM sucu WHERE codigo=${dbcuenta}";
		$this->set_message('existesucu', 'La sucursal introducido en el campo %s no es v&aacute;lida');

		$query = $this->CI->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row();
			if($row->cana>0) return true; else return false;
		}else{
			return false;
		}
	}


	function existescaj($cuenta){
		$cuenta  =trim($cuenta);
		if(strlen($cuenta)==0) return true;
		$dbcuenta=$this->CI->db->escape($cuenta);
		$mSQL = "SELECT COUNT(*) AS cana FROM scaj WHERE cajero=${dbcuenta}";
		$this->set_message('existescaj', 'El cajero introducido en el campo %s no es v&aacute;lido');

		$query = $this->CI->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row();
			if($row->cana>0) return true; else return false;
		}else{
			return false;
		}
	}

	function existecpla($cuenta){
		$cuenta  =trim($cuenta);
		if(strlen($cuenta)==0) return true;
		$dbcuenta=$this->CI->db->escape($cuenta);
		$mSQL = "SELECT COUNT(*) AS cana FROM cpla WHERE codigo=${dbcuenta}";
		$this->set_message('existecpla', 'La cuenta contable introducida en el campo %s no es v&aacute;lida');

		$query = $this->CI->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row();
			if($row->cana>0) return true; else return false;
		}else{
			return false;
		}
	}

	function existefac($numero){
		$numero  =trim($numero);
		if(strlen($numero)==0) return true;
		$dbnumero=$this->CI->db->escape($numero);
		$mSQL = "SELECT COUNT(*) AS cana FROM sfac WHERE tipo_doc='F' AND numero=${dbnumero}";
		$this->set_message('existefac', 'La factura introducida en el campo %s no es existe');

		$query = $this->CI->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row();
			if($row->cana>0) return true; else return false;
		}else{
			return false;
		}
	}

	function existescli($scli){
		$dbscli= $this->CI->db->escape($scli);
		$mSQL  = "SELECT COUNT(*) AS cana FROM scli WHERE cliente=${dbscli}";
		$this->set_message('existescli', 'El cliente propuesto en el campo %s no existe');

		$query = $this->CI->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row();
			if( $row->cana>0) return true; else return false;
		}else{
			return false;
		}
	}

	function existeban($ban){
		$dbban= $this->CI->db->escape($ban);
		$mSQL  = "SELECT COUNT(*) AS cana FROM banc WHERE codbanc=${dbban}";
		$this->set_message('existeban', 'El banco propuesto en el campo %s no existe');

		$query = $this->CI->db->query($mSQL);
		if($query->num_rows() > 0){
			$row = $query->row();
			if( $row->cana>0) return true; else return false;
		}else{
			return false;
		}
	}

	function existesinv($codigo){
		$dbcod= $this->CI->db->escape($codigo);
		$mSQL  = "SELECT COUNT(*) AS cana FROM sinv WHERE codigo=${dbcod}";
		$this->set_message('existesinv', 'El producto propuesto en el campo %s no existe');

		$query = $this->CI->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row();
			if( $row->cana>0) return true; else return false;
		}else{
			return false;
		}
	}

	function existebotr($codigo){
		$dbcod= $this->CI->db->escape($codigo);
		$mSQL  = "SELECT COUNT(*) AS cana FROM botr WHERE codigo=${dbcod}";
		$this->set_message('existebotr', 'El codigo propuesto en el campo %s no existe');

		$query = $this->CI->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row();
			if( $row->cana>0) return true; else return false;
		}else{
			return false;
		}
	}

	function existegrupo($codigo){
		$dbcod= $this->CI->db->escape($codigo);
		$mSQL  = "SELECT COUNT(*) AS cana FROM grup WHERE grupo=${dbcod}";
		$this->set_message('existegrupo', 'El grupo propuesto en el campo %s no existe');

		$query = $this->CI->db->query($mSQL);
		if($query->num_rows()>0){
			$row = $query->row();
			if($row->cana>0) return true; else return false;
		}else{
			return false;
		}
	}

	function existesprv($sprv){
		$dbsprv= $this->CI->db->escape($sprv);
		$mSQL  = "SELECT COUNT(*) AS cana FROM sprv WHERE proveed=${dbsprv}";
		$this->set_message('existesprv', 'El proveedor propuesto en el campo %s no existe');

		$query = $this->CI->db->query($mSQL);
		if ($query->num_rows() > 0){
			$row = $query->row();
			if( $row->cana>0) return true; else return false;
		}else{
			return false;
		}
	}

	function cajerostatus($scaj){
		$dbscaj=$this->CI->db->escape($scaj);
		$mSQL  = "SELECT fechac,status FROM scaj WHERE cajero=${dbscaj}";
		$query = $this->CI->db->query($mSQL);
		if($query->num_rows() > 0){
			$this->set_message('cajerostatus', 'El cajero ya fue cerrado para esta fecha');
			$row = $query->row();
			if($row->status=='C'){
				$tmc=timestampFromDBDate($row->fechac); //momento de cierre
				$tmt=mktime(0, 0, 0);

				if($tmt>$tmc){ //Chequea si lo puede abrir
					$data = array('fechaa' => date('Ymd'), 'horaa' => date('H:i:s'), 'status' => 'A');
					$mSQL_2 = $this->CI->db->update_string('scaj', $data, "cajero=${dbscaj}");
					$rt=$this->CI->db->simple_query($mSQL_2);
					return $rt;
				}else{
					return false;
				}
			}else{
				return true;
			}
		}else{
			$this->set_message('cajerostatus', 'Cajero inexistente');
			return false;
		}
	}

	function porcent($porcen){
		if ($porcen<=100 AND $porcen>=0) return true;
		$this->set_message('porcent', 'El valor del campo <b>%s</b> debe estar entre 0 y 100');
		return false;
	}

	function enum($val,$posibles){
		$this->set_message('enum', 'El campo %s contiene un valor inv&aacute;lido');
		$posi=explode(',',$posibles);
		return in_array($val,$posi);
	}

	function positive($val){
		$this->set_message('positive', 'El campo %s debe contener un valor positivo');
		return ($val>=0)? true : false;
	}

	function nocero($val){
		$this->set_message('nocero', 'El campo %s debe contener un valor distinto a cero');
		return ($val!=0)? true : false;
	}

	function mayorcero($val){
		$this->set_message('mayorcero', 'El campo %s debe contener un valor mayor que cero');
		return ($val>0)? true : false;
	}

	function mac($mac){
		$pattern='/[0-9AaBbCcDdEeFf]{2}(:[0-9AaBbCcDdEeFf]{2}){5}/';
		if(preg_match($pattern,$mac)>0){
			return true;
		}else{
			return false;
		}
	}

	function hora($hora){
		if (preg_match("/(^([01][0-9]|2[0-3])(:[0-5][0-9]){1,2}[[:blank:]]*$)|(^[[:blank:]]*$)/", $hora)>0){
			return TRUE;
		}else {
			$this->set_message('hora', "El dato introducido ('$hora') en el campo <b>%s</b> parece no corresponder con el formato [00-23]:[00-59]:[00-59]");
			return FALSE;
		}
	}

	//Para validar fecha con condi_required
	function chitfecha($validar,$format=null,$fname=null){
		if(empty($validar)) return true;
		return $this->chfecha($validar,$format,'chitfecha');
	}

	function chfecha($validar,$format=null,$fname=null){
		$formato= (empty($format))? RAPYD_DATE_FORMAT: $format;
		$fnombre= (empty($fname)) ? 'chfecha' : $fname;
		$formato=preg_quote($formato,'/');

		$search[] = "d"; $replace[] = "(0[1-9]|[1-2][0-9]|3[0-1])";
		$search[] = "j"; $replace[] = "([1-9]|[1-2][0-9]|3[0-1])";
		$search[] = "m"; $replace[] = "(0[1-9]|1[0-2])";
		$search[] = "n"; $replace[] = "([1-9]|1[0-2])";
		$search[] = "Y"; $replace[] = "([0-9]{4})";
		$search[] = "y"; $replace[] = "([0-9]{2})";
		$search[] = "H"; $replace[] = "([0-1][0-9]|2[0-4])";
		$search[] = "i"; $replace[] = "(0[0-9]|[1-5][0-9]|60)";
		$search[] = "s"; $replace[] = "(0[0-9]|[1-5][0-9]|60)";
		$pattern = str_replace($search, $replace, $formato);
		$pattern = '/'.$pattern.'/';
		$replace = $search = array();

		if(preg_match($pattern,$validar)>0){
			$search[] = "j"; $replace[] = "(?P<i>\d+)";
			$search[] = "d"; $replace[] = "(?P<i>\d+)";
			$search[] = "m"; $replace[] = "(?P<e>\d+)";
			$search[] = "n"; $replace[] = "(?P<e>\d+)";
			$search[] = "Y"; $replace[] = "(?P<a>\d+)";
			$search[] = "y"; $replace[] = "(?P<a>\d+)";

			$pattern = str_replace($search, $replace, $formato);
			$pattern = '/'.$pattern.'/';

			preg_match($pattern,$validar,$matches);

			$dia =(isset($matches['i']))? $matches['i'] : 1;
			$mes =(isset($matches['e']))? $matches['e'] : 1;
			$anio=(isset($matches['a']))? $matches['a'] : 1;

			if(!checkdate($mes,$dia,$anio)){
				$this->set_message($fnombre, "La fecha introducida en el campo <b>%s</b> no es v&aacute;lida");
				return false;
			}
		}else{
			$this->set_message($fnombre, "La fecha introducida en el campo <b>%s</b> no coincide con el formato");
			return false;
		}
		return true;
	}

	/**
	 * Corre las validaciones
	 *
	 * Fue modifica con respecto a la original para soportar
	 * campos requeridos condicionales.
	 * @access	public
	 * @return	bool
	 */
	function run(){
		// Do we even have any data to process?  Mm?
		if (count($_POST) == 0 OR count($this->_rules) == 0){
			return FALSE;
		}

		// Load the language file containing error messages
		$this->CI->lang->load('validation');

		// Cycle through the rules and test for errors
		foreach ($this->_rules as $field => $rules){
			//Explode out the rules!
			$ex = explode('|', $rules);

			// Is the field required?  If not, if the field is blank  we'll move on to the next test
			if ( ! in_array('required', $ex, TRUE)){
				if ( ! isset($_POST[$field]) OR $_POST[$field] == ''){
					$clave=array_search('condi_required',$ex);
					if($clave !== false ) unset($ex[$clave]); else continue;
					//if( ! in_array('condi_required', $ex, TRUE)) continue;
				}
			}

			/*
			 * Are we dealing with an "isset" rule?
			 *
			 * Before going further, we'll see if one of the rules
			 * is to check whether the item is set (typically this
			 * applies only to checkboxes).  If so, we'll
			 * test for it here since there's not reason to go
			 * further
			 */
			if ( ! isset($_POST[$field])){
				if (in_array('isset', $ex, TRUE) OR in_array('required', $ex)){
					if ( ! isset($this->_error_messages['isset'])){
						if (FALSE === ($line = $this->CI->lang->line('isset'))){
							$line = 'The field was not set';
						}
					}else{
						$line = $this->_error_messages['isset'];
					}

					// Build the error message
					$mfield = ( ! isset($this->_fields[$field])) ? $field : $this->_fields[$field];
					$message = sprintf($line, $mfield);

					// Set the error variable.  Example: $this->username_error
					$error = $field.'_error';
					$this->$error = $this->_error_prefix.$message.$this->_error_suffix;
					$this->_error_array[] = $message;
				}
				continue;
			}

			/*
			 * Set the current field
			 *
			 * The various prepping functions need to know the
			 * current field name so they can do this:
			 *
			 * $_POST[$this->_current_field] == 'bla bla';
			 */
			$this->_current_field = $field;

			// Cycle through the rules!
			foreach ($ex As $rule){
				// Is the rule a callback?
				$callback = FALSE;
				if (substr($rule, 0, 9) == 'callback_'){
					$rule = substr($rule, 9);
					$callback = TRUE;
				}

				// Strip the parameter (if exists) from the rule
				// Rules can contain a parameter: max_length[5]
				$param = FALSE;
				if (preg_match("/(.*?)\[(.*?)\]/", $rule, $match)){
					$rule	= $match[1];
					$param	= $match[2];
				}

				// Call the function that corresponds to the rule
				if ($callback === TRUE){
					if ( ! method_exists($this->CI, $rule))
					{
						continue;
					}

					$result = $this->CI->$rule($_POST[$field], $param);

					// If the field isn't required and we just processed a callback we'll move on...
					if ( ! in_array('required', $ex, TRUE) AND $result !== FALSE){
						continue 2;
					}
				}else{
					if ( ! method_exists($this, $rule)){
						/*
						 * Run the native PHP function if called for
						 *
						 * If our own wrapper function doesn't exist we see
						 * if a native PHP function does. Users can use
						 * any native PHP function call that has one param.
						 */
						if (function_exists($rule)){
							$_POST[$field] = $rule($_POST[$field]);
							$this->$field = $_POST[$field];
						}

						continue;
					}
					$result = $this->$rule($_POST[$field], $param);
				}

				// Did the rule test negatively?  If so, grab the error.
				if ($result === FALSE){
					if ( ! isset($this->_error_messages[$rule])){
						if (FALSE === ($line = $this->CI->lang->line($rule))){
							$line = 'Unable to access an error message corresponding to your field name.';
						}
					}else{
						$line = $this->_error_messages[$rule];
					}

					// Build the error message
					$mfield = ( ! isset($this->_fields[$field])) ? $field : $this->_fields[$field];
					$mparam = ( ! isset($this->_fields[$param])) ? $param : $this->_fields[$param];
					$message = sprintf($line, $mfield, $mparam);

					// Set the error variable.  Example: $this->username_error
					$error = $field.'_error';
					$this->$error = $this->_error_prefix.$message.$this->_error_suffix;

					// Add the error to the error array
					$this->_error_array[] = $message;
					continue 2;
				}
			}
		}

		$total_errors = count($this->_error_array);

		/*
		 * Recompile the class variables
		 *
		 * If any prepping functions were called the $_POST data
		 * might now be different then the corresponding class
		 * variables so we'll set them anew.
		 */
		if ($total_errors > 0){
			$this->_safe_form_data = TRUE;
		}

		$this->set_fields();

		// Did we end up with any errors?
		if ($total_errors == 0){
			return TRUE;
		}

		// Generate the error string
		foreach ($this->_error_array as $val){
			$this->error_string .= $this->_error_prefix.$val.$this->_error_suffix."\n";
		}

		return FALSE;
	}
}
