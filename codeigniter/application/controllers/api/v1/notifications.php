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

class Notifications extends REST_Controller
{
	function __construct(){
		parent::__construct();
		$this->load->model(array('user_model'));				
	}
        
        function send_post(){            
            $error = '';
            if($this->post('title') == ''){
                $error = 'Please enter notification title';
            } elseif($this->post('message') == ''){
                $error = 'Please enter notification message';
            } elseif($this->post('registeration_ids') == ''){
                $error = 'Please enter atleast one registeration id';
            } elseif($this->post('device_token') == ''){
                $error = 'Please enter notification device token';
            } elseif($this->post('device_type') == ''){
                $error = 'Please send 0 for Android and 1 for IOS';
            }
            if($error){
                $this->response(array('status' => 0,'message' => $error),200);
            } else {
                $registeration_ids = json_decode($this->post('registeration_ids'));
                send_notification($this->post('device_type'), $registeration_ids, $this->post('title')."\n".$this->post('message'));
                $this->response(
                    array(
                            'status' => 1, 
                            'message' => 'Sent', 
                            'result' => ''
                    ), 201
                );
            }            
        }
}