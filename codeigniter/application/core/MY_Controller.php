<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		$this->check_rememberme();
	}

	protected function check_rememberme()
	{
		if (! $this->my_usession->user_logged_in) {
			$this->load->helper(array('cookie'));

			if ($value = $this->input->cookie('rememberme')) {
				$this->load->model(array('user_model'));
				if ($User = $this->user_model->Find(array('rememberme_code' => $value), 1)) {
					$this->_init_user_session($User);
				}
			}
		}
	}

	protected function _init_user_session($User)
	{
		$this->session->set_userdata('user_logged_in', array(
			'user_session_type' => 'user',
			'id' => $User['id'],
			'user_slug' => $User['user_slug'],
			'email' => $User['email'],
			'name' => $User['name']
		));
	}

	protected function _init_admin_session($Admin)
	{
		$this->session->set_userdata('admin_logged_in', array(
			'admin_session_type' => 'admin',
			'id' => $Admin['id'],
			//'user_slug' => $Admin['admin_slug'],
			'email' => $Admin['email'],
			'name' => $Admin['name']
		));
	}

	protected function alpha_n_space_check($val)
	{
		return preg_replace('(^[a-zA-Z ]*$)', "", $val);
	}

	protected function space_check($val)
	{
		return strstr($val, ' ');
	}
	
	protected function make_date($date)
	{
		$tempArr = explode('-', $date);
		return date('Y-m-d', mktime(0, 0, 0, $tempArr[0], $tempArr[1], $tempArr[2]));
	}
	
	protected function date_check($date)
	{
		if ($date != '') {
			$tempArr = explode('-', $date);
			if (!checkdate($tempArr[0], $tempArr[1], $tempArr[2])) {
				return TRUE;
			} else {
				if (strlen($date) != 10 || count($tempArr) != 3) {
					return TRUE;
				}
			}
		}
		return FALSE;
	}
}