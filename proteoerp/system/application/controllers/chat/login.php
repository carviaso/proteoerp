<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {
	function __construct()
	{
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->helper('form');
	}

	function index()
	{
		$this->load->view('header');
		$this->load->view('login');
		$this->load->view('footer');
	}
	function submit()
	{
		$name = $this->input->post('name');
		if(!empty($name))
		{
			$this->session->set_userdata('name',$name);
			redirect('/chat', 'refresh');
		}
		else
		{
			redirect('/login', 'refresh');
		}
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */