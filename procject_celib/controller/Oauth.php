<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//api
class Oauth  extends MY_Controller{


	public function index()
    {
        header('Content-Type: application/json; charset=utf-8');
        $data=null;
        echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    public function google()
    {
        header('Content-Type: application/json; charset=utf-8');
        $get_params = $this->get_params($_SERVER['QUERY_STRING']);
        var_dump($get_params);

        log_message("debug","***Oauth.google >> IN >> ".json_encode($get_params));

        $data=null;
        echo json_encode($data, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }


}
