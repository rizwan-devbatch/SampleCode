<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if(! function_exists('sendEmail')){
	
	function sendEmail($email_to=NULL,$subject=NULL,$message = NULL) {	
		$CI =& get_instance();
		$config = Array(
		  'protocol' => 'smtp',
		  'smtp_host' => 'ssl://smtp.googlemail.com',
		  'smtp_port' => 465,
		  'smtp_user' => 'Sportan@sportan.com', // change it to yours
		  'smtp_pass' => 'Sportan510', // change it to yours
		  'mailtype' => 'html',
		  'charset' => 'iso-8859-1',
		  'wordwrap' => TRUE
		);
		
		//$config['mailtype'] = 'html';
		$CI->load->library('email',$config);
		$CI->email->set_newline("\r\n");
		$CI->email->from('info@ci_template.com', 'CI Template'); // change it to yours
		$CI->email->to($email_to);// change it to yours
		$CI->email->subject($subject);
		$CI->email->message($message);
		if($CI->email->send()){
			return array('status'=>TRUE,'errors'=>array());
		} else {
			//return FALSE;
		    $errors = $CI->email->print_debugger();
                    return array('status'=>FALSE,'errors'=>$errors);
		}		
		
	}
	
}
if(! function_exists('getForgetPasswordEmailMessage')){
    function getForgetPasswordEmailMessage($emailAddress,$changePasswordToken){
	$html = '<html>
                    <head>
                      <title>Reset Your Password</title>
                    </head>
                    <body>
                    <div style="background:#ccc;text-align:center;font-family:Helvetica,Arial,sans-serif;font-size:1em;color:#666">
                            <table style="width:600px;margin:10px auto 0px auto;border-top:10px solid #3b566b;border-collapse:collapse">
                                    <tbody><tr>
                                        <td colspan="8" style="background-color:#ffffff;text-align:left;padding:0 20px 20px">
                                                    <h1 style="color:#000000;font-weight:100">Forgot your Ci Template password?</h1>
                                                    <h3 style="padding-bottom:20px;margin-bottom:0px;border-bottom:1px solid #cccccc;font-weight:200">No worries :)</h3>
                                        </td>
                                    </tr>
                                    <tr style="text-align:left">
                                            <td colspan="8" style="background-color:#ffffff;padding:0 20px">
                                                    <p>Hi there!</p>
                                                    <p>
                                                            Your email: <span style="color:#000000"><a href="mailto:'.$emailAddress.'" target="_blank">'.$emailAddress.'</a></span>
                                                    </p>
                                                    <p style="color:#000000;font-weight:normal;background:#fdface;padding:20px">
                                                            To reset your password Copy token and send it to reset password request: <span style="color:#3b566b;font-weight:600">'.$changePasswordToken.'</span>
                                                    </p>
                                                    <p>
                                                            <br><a href="mailto:www.devbatch.com?subject=Question+about+Ci" style="color:#3b566b;font-weight:600" target="_blank">Email us</a> if you have any questions.
                                                    </p>

                                            </td>
                                    </tr>
                                    <tr style="text-align:right">
                                            <td colspan="8" style="background-color:#ffffff;padding:20px">
                                                    <p>
                                                            Cheers and Happy Ci Template!
                                                            <br>- The Ci Template Team
                                                    </p>
                                            </td>
                                    </tr>

                            </tbody>
                    </table>
                    <span class="HOEnZb adL"><font color="#888888">
                    </font></span>
                    </div>					  
                    </body>
            </html>';
            return $html;
}
}

// Push Notifications

if(! function_exists('send_notification')){
	
	function send_notification($deviceType,$registatoin_ids, $message) {
	    
	    if(isset($deviceType) && $deviceType == '0'){
			sendNotificationToAndroidUsers($registatoin_ids,$message);
		}
		
		if(isset($deviceType) && $deviceType == '1'){
			sendNotificationToIOSUsers($registatoin_ids,$message);
		}
	    
	}
	
}

function sendNotificationToAndroidUsers($registatoin_ids,$message){
	 // Set POST variables
        $url = 'https://android.googleapis.com/gcm/send';
        //$registatoin_ids = 'APA91bER3nFjE6bg68Vso7mMmT8NWKcQUyPnc-E2zIQoi_ZN2lNUYKGrJboA6jWxy485PjTzOgEXoJjhd4fRyx9YUPRgQmeQMVvyPiPaSLLKA0q0Bn7tWPo';
        $fields = array(
            'registration_ids' => array($registatoin_ids),
            'data' => array("message" => $message)
        );

        $headers = array(
            'Authorization: key=' . GOOGLE_API_KEY,
            'Content-Type: application/json'
        );
        // Open connection
        $ch = curl_init();
        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt( $ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        // Execute post
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        // Close connection
        curl_close($ch); 
        
       	return $result;
}

function sendNotificationToIOSUsers($registatoin_ids,$message){
        $registatoin_ids = '7223c913446663169f26bfb2c88f28b1a5a0b585ddb5af3e833b852f41734189';
        $pem ='';
        $url = '';	
        $passphrase = '';	

        if (defined('ENVIRONMENT'))
        {
                switch (ENVIRONMENT)
                {
                        case 'development':
                                $pem ='./assets/includes/apns-dev-cert.pem';
                                $url = 'ssl://gateway.sandbox.push.apple.com:2195';
                        break;

                        case 'testing':
                        case 'production':
                                $pem ='./assets/includes/apns-prod-cert.pem';
                                $url = 'ssl://gateway.push.apple.com:2195';
                        break;

                        default:
                                $pem ='./assets/includes/apns-dev-cert.pem';
                                $url = 'ssl://gateway.sandbox.push.apple.com:2195';
                }
        }

         // Put your private key's passphrase here:
         $ctx = stream_context_create();
         stream_context_set_option($ctx, 'ssl', 'local_cert', $pem);
         stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

         $fp = stream_socket_client($url, 
             $err, 
             $errstr, 
             60, 
             STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, 
             $ctx);

         if (!$fp)
          return;
         // exit("Failed to connect : $err $errstr" . PHP_EOL);

         //echo 'Connected to APNS' . PHP_EOL;

         // Create the payload body
         $body['aps'] = array(
             'badge' => 1,
             'alert' => $message,
             'sound' => 'default'
         );

         $payload = json_encode($body);

         // Build the binary notification
         $msg = chr(0) . pack('n', 32) . pack('H*', $registatoin_ids) . pack('n', strlen($payload)) . $payload;
         //var_dump($msg);	
         // Send it to the server
         $result = fwrite($fp, $msg, strlen($msg));         
         if (!$result)
             return 'Message not delivered' . PHP_EOL;
         else
             return 'Message successfully delivered '.$message. PHP_EOL;

         // Close the connection to the server
         fclose($fp);
}    

?>
