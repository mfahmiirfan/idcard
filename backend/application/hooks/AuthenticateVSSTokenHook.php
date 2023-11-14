<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthenticateVSSTokenHook
{
  public function authenticate()
 {
   // load ci instance
   $ci = &get_instance();
   $ci->load->helper('cookie');
   if(isset($ci->methods_vss_token_verify_hookable)){
    if(in_array($ci->router->fetch_method(),$ci->methods_vss_token_verify_hookable)){
     $headers = $ci->input->request_headers();
    //  echo $headers['Authorization'];
    //   exit;
    $token =get_cookie('VSSTokenId');
     if(!isset($token)){
       $ci->output->set_status_header(401);
       $response = ['status' => 401, 'msg' => 'Unauthorized Access!'];
      $ci->output->set_content_type('application/json');
      $ci->output->set_output(json_encode($response,JSON_PRETTY_PRINT   |JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->_display();
      exit;
     }
    // $token = $headers['Authorization'];
    try {
    $data = JWT::decode(/*explode(' ',$token)[1]*/$token, new Key($ci->config->item('vss_jwt_key'), 'HS256'));
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