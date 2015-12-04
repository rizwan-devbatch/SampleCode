<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Usession extends CI_Session {
	public $User_Session_Data, $Admin_Session_Data = array();
	public $user_logged_in, $admin_logged_in = FALSE;

	public function __construct()
	{ 
		parent::__construct();
		$this->is_logged_in();
		$this->is_admin_logged_in();
	}

	public function is_logged_in()
	{
		$this->User_Session_Data = $this->userdata('user_logged_in');
		$this->user_logged_in = ($this->User_Session_Data['id'] && array_key_exists('user_session_type', $this->User_Session_Data) && $this->User_Session_Data['user_session_type'] == 'user') ? TRUE : FALSE;
	}

	function check_session()
	{ 
		if (!$this->user_logged_in) {
			redirect('');
		}
	}

	public function is_admin_logged_in()
	{
		$this->Admin_Session_Data = $this->userdata('admin_logged_in');                
		$this->admin_logged_in = ($this->Admin_Session_Data['id'] && array_key_exists('admin_session_type', $this->Admin_Session_Data) && $this->Admin_Session_Data['admin_session_type'] == 'admin') ? TRUE : FALSE;
	}

	function check_admin_session()
	{
		if (! $this->admin_logged_in) {
			redirect('admin');
		}
	}
}
?>