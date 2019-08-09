<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set("Asia/Bangkok");

class Manage extends CI_Controller
{
    private $my_password = 'q1a2z3q1';
    private $output_data = [];
    private $password = '';
    public function __construct()
    {
        parent::__construct();
        $this->load->library('encryption');
        $this->load->library('form_validation');
        $this->load->model('Database_model');
        $this->load->helper('form');
        $this->load->library('parser');
        $this->load->helper('url');
    }
    
    public function index()
    {
        $config = [
            [
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'required',
                'errors' => [
                    'required' => 'Please Enter %s',
                ]
            ]
        ];
        $this->form_validation->set_rules($config);
        if ($this->form_validation->run() == false) {
            $this->load->view('manage/header');
            $this->load->view('manage/login');
            $this->load->view('manage/footer');
        } else {
            if ($this->input->post()) {
                $password = $this->input->post('password');

                if ($password == $this->my_password) {
                    $password = base64_encode($this->encryptAES($password));
                    redirect('/manage/user/?password='.$password.'');
                } else {
                    $this->form_validation->set_rules('password', 'Password', 'callback_password_check');
                    $this->form_validation->set_message('password_check', '{field} Incorrect');
                    $this->form_validation->run('password_check');
                    $this->load->view('manage/header');
                    $this->load->view('manage/login');
                    $this->load->view('manage/footer');
                }
            }
        }
    }
    
    public function user()
    {
        if ($this->input->get()) {
            $password = $this->input->get('password');
            $this->password = $password;
            $password = $this->decryptAES(base64_decode($password));
            if ($password == $this->my_password) {
                $this->setOutputPostUrl();
                $this->output_data['user_list'] = json_encode($this->database_model->selectAllUser(), true);
                $this->output_data['user_list'] = str_replace(["\r\n","\r","\n","\\r","\\n","\\r\\n"], "<br/>", $this->output_data['user_list']);

                $this->load->view('manage/user/header');
                $this->parser->parse('manage/user/sidebar', $this->output_data);
                $this->parser->parse('manage/user/user', $this->output_data);
                $this->load->view('manage/user/footer');
            } else {
                redirect('/manage');
            }
        } else {
            redirect('/manage');
        }
    }

    public function updateUser()
    {
        if ($this->input->post()) {
            $data = [];
            $data['id'] = $this->input->post('id');
            $data['user'] = $this->input->post('user');
            $data['platform'] = $this->input->post('platform');
            $data['gem'] = $this->input->post('gem');
            $data['device_id'] = $this->input->post('device_id');
            $data['secret_key'] = $this->input->post('secret_key');
            $data['device_id_ap'] = $this->input->post('device_id_ap');
            $data['secret_key_ap'] = $this->input->post('secret_key_ap');
            $data['owner'] = $this->input->post('owner');
            $data['bot_day'] = $this->input->post('bot_day');
            $data['date_buy_bot'] = $this->input->post('date_buy_bot');

            $output = $this->database_model->updateUser($data);

            if ($output) {
                $output = '{"msg":true}';
                $this->output
                ->set_header('HTTP/1.1 200 OK')
                ->set_content_type('application/json', 'utf-8')
                ->set_output($output);
            } else {
                $output = '{"msg":false}';
                $this->output
                ->set_header('HTTP/1.1 200 OK')
                ->set_content_type('application/json', 'utf-8')
                ->set_output($output);
            }
        }
    }

    public function addUser()
    {
        if ($this->input->post()) {
            $data = [];
            $data['user'] = $this->input->post('user');
            $data['owner'] = $this->input->post('owner');
            $data['gem'] = 0;
            $data['bot_day'] = 0;
            $data['date_buy_bot'] = date("Y-m-d H:i:s");
            $data['platform'] = $this->input->post('platform');
            $data['device_id'] = $this->input->post('device_id');
            $data['secret_key'] = $this->input->post('secret_key');

            $output = $this->database_model->addUser($data);

            if ($output) {
                $output = '{"msg":true, "id":'.$output.'}';
                $this->output
                ->set_header('HTTP/1.1 200 OK')
                ->set_content_type('application/json', 'utf-8')
                ->set_output($output);
            } else {
                $output = '{"msg":false}';
                $this->output
                ->set_header('HTTP/1.1 200 OK')
                ->set_content_type('application/json', 'utf-8')
                ->set_output($output);
            }
        }
    }

    public function deleteUser()
    {
        if ($this->input->post()) {
            $data = [];
            $data['id'] = $this->input->post('id');

            $output = $this->database_model->deleteUser($data);

            if ($output) {
                $output = '{"msg":true}';
                $this->output
                ->set_header('HTTP/1.1 200 OK')
                ->set_content_type('application/json', 'utf-8')
                ->set_output($output);
            } else {
                $output = '{"msg":false}';
                $this->output
                ->set_header('HTTP/1.1 200 OK')
                ->set_content_type('application/json', 'utf-8')
                ->set_output($output);
            }
        }
    }

    public function setOutputPostUrl()
    {
        $this->output_data['post_updateUser'] = base_url('manage/updateUser/');
        $this->output_data['post_addUser'] = base_url('manage/addUser/');
        $this->output_data['post_deleteUser'] = base_url('manage/deleteUser/');

        $this->output_data['manage_url'] = base_url('/manage/user/?password='.$this->password);
        $this->output_data['reroll_url'] = base_url('/manage/reroll/?password='.$this->password);
        $this->output_data['logout_url'] = base_url('/manage');
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

/* End of file Manage.php */
