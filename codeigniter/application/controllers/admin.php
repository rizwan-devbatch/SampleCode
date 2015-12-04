<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends My_Controller {

	function __construct()
	{
		parent::__construct();

		$this->_init();
	}

	private function _init()
	{
               
		$this->output->set_template('admin');
                $this->load->model(array('admin_model','role_model'));
	}

	public function index()
	{
		if ($this->my_usession->admin_logged_in) {
			redirect('admin/dashboard');
		} else {
			redirect('admin/signin');
		}
	}
        
        public function users()
	{
            $data['Users'] = $this->admin_model->Find(array('email !=' => $this->my_usession->Admin_Session_Data['email']));
            $this->load->view('admin/users', $data);
	}
        
        public function create()
	{
            $this->_add_edit('create', '');
	}
        
        public function edit($id)
	{
		$this->_add_edit('edit', $id);
	}
        private function _add_edit($action, $id = '')
	{
		$data['action'] = $action;

		if ($id != '') {
			if ($data['User'] = $this->admin_model->Find(array('id' => $id), 1)) {
                            $data['Roles'] = $this->role_model->Find();
			}
		} else {
			$data['User'] = array(
                            'name' => '',
                            'email' => '',
                            'password' => '',
                            'role_id' => ''
                        );
                        $data['Roles'] = $this->role_model->Find();
		}
		$data['errors'] = array();
                if ($data['User']) {
                    if ($this->input->post()) { 
                        $this->load->helper('email');
                        if ($this->input->post('name') == '') {
                                $data['errors']['name'] = 'Please enter name.';
                        } elseif(count($this->admin_model->Find(array('name' => $this->input->post('name'),'id !=' => $data['User']['id'])))){
                            $data['errors']['name'] = 'name already exist.';
                        } elseif ($this->input->post('email') == '') {
                            $data['errors']['email'] = 'Oops, you forgot your e-mail.';
                        } elseif (!valid_email($this->input->post('email'))) {
                            $data['errors']['email'] = 'Please enter a valid email address.';			
                        } elseif(count($this->admin_model->Find(array('email' => $this->input->post('email'),'id !=' => $data['User']['id'])))){
                            $data['errors']['email'] = 'email already exist.';
                        }
                        if (count($data['errors'])) {
                                $data['User'] = $this->input->post();
                        } else {
                            $User = array(
                                'name' => $this->input->post('name'),
                                'email' => $this->input->post('email'),
                                'password' => $this->input->post('password'),
                                'role_id' => $this->input->post('role_id')
                            );
                                                        
                            if ($data['action'] == 'create') {
                                $data['User'] = $this->admin_model->Save('add', $User);                                    
                            } else {
                                if ($this->admin_model->Save('update', $User, array('id' => $data['User']['id']))) {
                                    $data['User'] = $this->admin_model->FindById($data['User']['id']);
                                }
                            }                            
                        }
                    }
                    $this->load->view('admin/_form', $data);
                }else {
			//show_404();
                    echo 'Page you are looking for does not exist.';
		}
        }
	public function signup()
	{
		$this->load->library(array('form_validation'));		

		if ($this->input->post()) {
			if ($this->form_validation->run() == FALSE) {
				//echo 'error';
			} else {
				if ($Admin = $this->admin_model->Save('add', $this->input->post())) {
					echo 'Success';
				}
			}
		} else {
			$data['Admin'] = array(
				'name' => '',
				'email' => '',
			);
			$this->load->view('admin/signup', $data);
		}	
	}

	public function signin()
	{		
		if ($this->my_usession->admin_logged_in) {
			redirect('admin/dashboard');
		}
		$this->load->library(array('form_validation'));

		$data['errors'] = array();
		if ($this->input->post()) {
			$data['user']['email'] = $this->input->post('email');
			$this->load->helper('email');

			if ($this->input->post('email') == '') {
				$data['errors']['email'] = 'Missing e-mail.';
			} elseif (!valid_email($this->input->post('email'))) {
				$data['errors']['email'] = 'Please enter a valid email address.';
			}
			if ($this->input->post('password') == '') {
				$data['errors']['password'] = 'Missing password.';
			}
			if (count($data['errors'])) {
				//
			} elseif ($Admin = $this->admin_model->Find(array('email' => $this->input->post('email')), 1)) {
				if ($this->input->post('password') == $Admin['password']) {
					$this->_init_admin_session($Admin);
					redirect('admin/dashboard');
				} else {
					$data['errors']['password'] = 'Password does not match.';
				}
			} else {
				$data['errors']['email'] = 'Invalid email.';
			}
		} else {
			$data['user']['email'] = '';	
		}
		$this->output->set_template('admin-login');
		$this->load->view('admin/signin', $data);
	}

	function signout()
	{
		$this->session->unset_userdata('admin_logged_in');
		
		redirect('admin/index');
	}

	function dashboard()
	{
		if (!$this->my_usession->admin_logged_in) {
			redirect('admin/signin');
		}
		$this->load->view('admin/dashboard', $data);
	}
}