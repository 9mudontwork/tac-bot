<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Secure {

  private $ci;
  public function __construct()
  {
    $this->ci = get_instance();
    $this->ci->load->library('session');
  }

  public function check_login()
  {
    $class_method = $this->ci->router->fetch_class();
    if($class_method == 'sysadmin' OR $class_method == 'crawler_namanga' OR $class_method == 'crawler_mangaseed')
    {
      $user = $this->ci->session->userdata('username');
      $logged_in = $this->ci->session->userdata('logged_in');
      if($user AND $logged_in == TRUE)
      {
        if($user != 'Admin')
        {
          redirect();
        }
      }
      else
      {
        redirect('auth/login');
        exit();
      }
    }
  }
}
