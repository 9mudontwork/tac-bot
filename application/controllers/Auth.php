<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('encryption');
        $this->load->library('form_validation');
        $this->load->model('Database_model');
        $this->load->helper('form');
    }

    public function index()
    {
        $config = [
                        [
                            'field' => 'email_login',
                            'label' => 'Email',
                            'rules' => 'required',
                            'errors' => [
                                'required' => 'Please Enter %s',
                            ]
                        ]
                    ];
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() == false) {
            $this->load->view('auth/header');
            $this->load->view('auth/login');
            $this->load->view('auth/footer');
        } else {
            if ($this->input->post()) {
                $email_login = $this->input->post('email_login', true);
    
                $data = ['user' => $email_login];
                    
                $data = $this->database_model->selectUser($data);

                // $id = $data['id'];
    
                if ($data['id']) {
                    $id = base64_encode($this->encryptAES($data['id']));
                    redirect('/page/account/?id='.$id.'');
                } else {
                    $this->form_validation->set_rules('email', 'Email', 'callback_email_check');
                    $this->form_validation->set_message('email_check', '{field} Not Found');
                    $this->form_validation->run('email_check');
                    $this->load->view('auth/header');
                    $this->load->view('auth/login');
                    $this->load->view('auth/footer');
                }
            } else {
                $this->load->view('auth/header');
                $this->load->view('auth/login');
                $this->load->view('auth/footer');
            }
        }
    }

    public function initCryption()
    {
        return $this->encryption->initialize(
            [
                'driver' => 'mcrypt',
                'cipher' => 'aes-256',
                'mode' => 'ctr',
                'key' => 'KAqVb99L31E8kQ0cJr6BL6lb9bw2f1up'
            ]
        );
    }

    public function encryptAES($text)
    {
        $this->initCryption();
        return $this->encryption->encrypt($text);
    }

    public function decryptAES($text)
    {
        $this->initCryption();
        return $this->encryption->decrypt($text);
    }
}

/* End of file Auth.php */
