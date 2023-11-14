<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthenticateTokenHook
{
  public function authenticate()
 {
   // load ci instance
   $ci = &get_instance();
   $ci->load->helper('cookie');
   if(isset($ci->is_token_verify_hookable)){
    if($ci->is_token_verify_hookable){
     $headers = $ci->input->request_headers();
     if(!isset($headers['idcard_auth'])) {
      $headers['idcard_auth']=null;
     }
    //  echo $headers['Authorization'];
    //   exit;
    $token =get_cookie('idcard_auth')!==null?get_cookie('idcard_auth'):$headers['idcard_auth'];
     if(!isset($token)){
       $ci->output->set_status_header(401);
       $response = ['status' => 401, 'msg' => 'Unauthorized Access!'];
      $ci->output->set_content_type('application/json');
      $ci->output->set_output(json_encode($response,JSON_PRETTY_PRINT   |JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
      exit;
     }
    // $token = $headers['Authorization'];
    try {
    $data = JWT::decode(/*explode(' ',$token)[1]*/$token, new Key($ci->config->item('jwt_key'), 'HS256'));
    if ($data === false) {
      $ci->output->set_status_header(401);
      $response = ['status' => 401, 'msg' => 'Unauthorized Access!@'];
      $ci->output->set_content_type('application/json');
      $ci->output->set_output(json_encode($response,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();;
      exit();
    } else {
      $ci->token_data=$data;
    }
   } catch (Exception $e) {
     $response = ['status' => 401, 'msg' => 'Unauthorized Access!'];
     $ci->output->set_content_type('application/json');
     $ci->output->set_output(json_encode($response,JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();;
    exit;
   }
 }
}
}
}