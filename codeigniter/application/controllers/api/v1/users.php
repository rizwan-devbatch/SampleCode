<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Example
 *
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		Phil Sturgeon
 * @link		http://philsturgeon.co.uk/code/
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require_once APPPATH.'/libraries/REST_Controller.php';

class Users extends REST_Controller
{
	function __construct(){
		parent::__construct();
		$this->load->model(array('user_model'));				
	}

    function signup_post(){
        $error = '';
        $this->load->helper('email');
        $this->load->helper('string');
        if ($this->post('first_name') == '' && $this->post('last_name') == '' && $this->post('email') == ''&& $this->post('password') == '') {
                $error = 'Please make sure to fill out all mandatory fields.';
        } else {
            if ($this->post('first_name') == '') {
                $error = 'Please enter your first name.';
            }elseif ($this->post('last_name') == '') {
                    $error = 'Please enter your last name.';
            }elseif ($this->post('email') == '') {
                    $error = 'Oops, you forgot your e-mail.';
            } elseif (!valid_email($this->post('email'))) {
                    $error = 'Please enter a valid email address.';			
            } elseif ($this->user_model->Find(array('email' => $this->post('email')))) {
                    $error = 'Oops, email address already exists.';
            } elseif ($this->post('password') == '') {
                    $error = 'Password field is empty.';
            } elseif (strlen ($this->post('password')) < 8) {
                    $error = 'Sorry, password is too short (min 8 characters';
            }
        }
        if ($error) {
            $this->response(array('status' => 0, 'message' => $error), 200); // 200 being the HTTP response code
        } else {
            if($User = $this->user_model->Save('add',array('first_name' => $this->post('first_name'),'last_name' => $this->post('last_name'),'email' => $this->post('email'),'password' => $this->post('password')))){
                $this->response(
                    array(
                            'status' => 1, 
                            'message' => 'success', 
                            'result' => $User
                    ), 201
                );
            } else {
                $this->response(
                    array(
                            'status' => 0, 
                            'message' => 'failure', 
                            'result' => array()
                    ), 200
                );
            }
        }
    }
    
    function signin_put(){
            $User = array();
            $error = '';
            $this->load->helper('email');
            if ($this->put('email') == ''&& $this->put('password') == '') {
                    $error = 'Please make sure to fill out all mandatory fields.';
            } else {
                if ($this->put('email') == '') {
                        $error = 'Oops, you forgot your e-mail.';
                } elseif (!valid_email($this->put('email'))) {
                        $error = 'Please enter a valid email address.';			
                } elseif ($this->put('password') == '') {
                        $error = 'Password field is empty.';
                }
            }
            if ($error) {
                $this->response(array('status' => 0, 'message' => $error), 200); // 200 being the HTTP response code
            } else {
                if ($User = $this->user_model->Find(array('email' => $this->put('email'),'password' => $this->put('password')),1)) {
                    $this->response(
                        array(
                                'status' => 1, 
                                'message' => 'User found', 
                                'result' => $User
                        ), 201
                    ); 
                } else {
                    $this->response(
                            array(
                                    'status' => 0, 
                                    'message' => 'Email or password does not match', 
                                    'result' => $User
                            ), 200
                    );
                }
            }
            
    }
    
    function forget_password_get(){
        $error = '';
        $this->load->helper('email');
        if ($this->get('email') == '') {
            $error = 'Oops, you forgot your e-mail.';
        } elseif (!valid_email($this->get('email'))) {
            $error = 'Please enter a valid email address.';			
        } 
        if ($error) {
            $this->response(array('status' => 0, 'message' => $error), 200); // 200 being the HTTP response code
        } else {
            $this->load->helper('string');
            $changePasswordToken = random_string('numeric',6);
            $subject = 'Reset Your Password';				
            $message = getForgetPasswordEmailMessage($this->get('email'),$changePasswordToken);
            $sendEmail = sendEmail($this->get('email'),$subject,$message);
            if($sendEmail['status']){
                $this->user_model->Save('update',array('change_password_token' => $changePasswordToken),array('email' => $this->get('email')));
                $this->response(
                    array(
                            'status' => 1, 
                            'message' => 'Please check your email, Follow the instructions to reset your password', 
                            'result' => ''
                    ), 201
                );
            } else {
                $this->response(array('status' => 0, 'message' => $sendEmail['message']), 200);
            }
        }
    }
    
    function change_password_put(){
        $error = '';
        $this->load->helper('email');
        if ($this->put('email') == '') {
            $error = 'Oops, you forgot your e-mail.';
        } elseif (!valid_email($this->put('email'))) {
            $error = 'Please enter a valid email address.';			
        } elseif ($this->put('change_password_token') == '') {
            $error = 'Please enter a change password token sent you in a mail.';			
        } elseif (count($this->user_model->Find(array('change_password_token' => $this->put('change_password_token'),'email' => $this->put('email')))) < 1) {
            $error = 'change password token does not match.';			
        } elseif ($this->put('new_password') == '') {
            $error = 'Password field is empty.';
        } elseif (strlen ($this->put('new_password')) < 8) {
            $error = 'Sorry, password is too short (min 8 characters';
        }
        
        if ($error) {
            $this->response(array('status' => 0, 'message' => $error), 200); // 200 being the HTTP response code
        } else {            
            if($User=  $this->user_model->Save('update',array('password' => $this->put('new_password')),array('email' => $this->put('email')))){
                $User=  $this->user_model->Save('update',array('change_password_token' => ''),array('email' => $this->put('email')));
                $this->response(
                    array(
                            'status' => 1, 
                            'message' => 'Your password has been changed', 
                            'result' => $User
                    ), 201
                );
            } else {
                $this->response(array('status' => 0, 'message' => 'failure'), 200);
            }
        }
    }
    
    function update_profile_post(){
        $error = '';
       
        $this->load->helper('email');
        if ($this->post('email') == '') {
            $error = 'Oops, you forgot your e-mail.';
        } elseif (!valid_email($this->post('email'))) {
            $error = 'Please enter a valid email address.';			
        }
        if ($error) {
            $this->response(array('status' => 0, 'message' => $error), 200); // 200 being the HTTP response code
        } else {
            $update_array = array();
            if($this->post('first_name') !=''){
                $update_array['first_name'] = $this->post('first_name');
            }
            if($this->post('last_name') !=''){
                $update_array['last_name'] = $this->post('last_name');
            }
            if ($_FILES['profile_image']['name']) {
                $ext = end(explode('.', $_FILES['profile_image']['name']));
                if (!$_FILES['profile_image']['error']) {
                    $new_file_name = time() . "." . $ext; //rename file
                    move_uploaded_file($_FILES['profile_image']['tmp_name'], 'assets/images/' . $new_file_name);
                    $update_array['profile_image'] = 'images/' . $new_file_name;
                } else {
                    $this->response(array('status' => 0, 'message' => 'Ooops!  Your upload triggered the following error:  ' . $_FILES['profile_image']['error']), 200);                    
                }
            }
            
            if(count($update_array) > 0){
                if($this->user_model->Save('update',$update_array,array('email' => $this->put('email')))){
                    $this->response(
                        array(
                                'status' => 1, 
                                'message' => 'Your profile has been updated', 
                                'result' => array()
                        ), 201
                    );
                } else {
                    $this->response(array('status' => 0, 'message' => 'Error occured'), 200);
                }
            } else {
                $this->response(array('status' => 0, 'message' => 'Please set atleast one value to update'), 200);
            }            
            
        }
    }
    
   
    
}