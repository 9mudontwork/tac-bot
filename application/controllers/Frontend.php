<?php

defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set("Asia/Bangkok");

class Frontend extends CI_Controller
{
    private $id;
    private $field_user;
    private $output_data = [];
    private $expireBot;

    public function __construct()
    {
        parent::__construct();
        $this->load->library('encryption');
        $this->load->model('Database_model');
        $this->load->library('parser');

        $this->checkUserInfo();
    }

    public function checkUserInfo()
    {
        if ($this->input->get('id', false) != '') {
            $encryptId = $this->input->get('id', false);
            $this->id = base64_decode($this->input->get('id', false));
            $this->id = $this->decryptAES($this->id);
            $this->database_model->updateLastLogin($this->id);

            $id = ['id' => $this->id];
            $this->field_user = $this->database_model->selectUser($id);

            if ($this->field_user) {
                foreach ($this->field_user as $key => $value) {
                    $this->output_data[$key] = $this->checkData($value);
                }
                
                $this->menuLink($encryptId);
            }

            $this->expireBot = $this->checkExpireBot($this->output_data['date_buy_bot'], $this->output_data['bot_day']);
            $this->output_data['expire'] = date("d-M-Y H:i:s", strtotime("+".$this->output_data['bot_day']." day", strtotime($this->output_data['date_buy_bot'])));
        }
    }

    public function encodeOutputData()
    {
        $this->output_data['id'] = base64_encode($this->encryptAES($this->output_data['id']));
        $this->output_data['device_id'] = base64_encode($this->encryptAES($this->output_data['device_id']));
        $this->output_data['secret_key'] = base64_encode($this->encryptAES($this->output_data['secret_key']));
    }

    public function encodeToken()
    {
        if ($this->output_data['token'] != '') {
            $this->output_data['token'] = base64_encode($this->encryptAES($this->output_data['token']));
        }
    }

    public function setOutputPostUrl()
    {
        $this->output_data['post_getToken'] = base_url('tac_api/getToken/');
        $this->output_data['post_getTokenAP'] = base_url('tac_api/getTokenAP/');
        $this->output_data['post_getInfo'] = base_url('tac_api/getInfo/');

        $this->output_data['post_doInjection'] = base_url('tac_api/doInjection/');
        $this->output_data['post_checkGemInMail'] = base_url('tac_api/checkGemInMail/');
        $this->output_data['post_getGemInMail'] = base_url('tac_api/getGemInMail/');
    }

    //
    // ─── ACCOUNT ────────────────────────────────────────────────────────────────────
    //

    public function account()
    {
        $this->setOutputPostUrl();
        $this->encodeOutputData();

        if ($this->expireBot) {
            $this->parser->parse('frontend/header', $this->output_data);
            $this->parser->parse('frontend/sidebar', $this->output_data);
            $this->parser->parse('frontend/container/user_info', $this->output_data);
            $this->load->view('frontend/footer');
        }
    }

    //
    // ─── INJECTION ──────────────────────────────────────────────────────────────────
    //

    public function injection()
    {
        $action = $this->input->get('action');
        $param = [
            'gem' => 'gem',
        ];

        $this->setOutputPostUrl();
        $this->encodeOutputData();
        $this->encodeToken();

        if ($this->expireBot && $this->output_data['gem'] == 1) {
            $this->parser->parse('frontend/header', $this->output_data);
            $this->parser->parse('frontend/sidebar', $this->output_data);
            $this->parser->parse("frontend/injection/{$param[$action]}", $this->output_data);
            $this->load->view('frontend/footer');
        } else {
            $this->parser->parse('frontend/header', $this->output_data);
            $this->parser->parse('frontend/sidebar', $this->output_data);
            $this->load->view('frontend/container/expired');
            $this->load->view('frontend/footer');
        }
    }

    //
    // ─── QUEST ──────────────────────────────────────────────────────────────────────
    //


    public function quest()
    {
        $action = $this->input->get('action');
        $action_param = [
            'normal' => 'normal',
            'hard' => 'hard',
            'multiplayer' => 'multiplayer',
            'event' => 'event',
        ];

        $this->setOutputPostUrl();
        $this->encodeOutputData();
        $this->encodeToken();
        

        if ($this->expireBot) {
            $this->parser->parse('frontend/header', $this->output_data);
            $this->parser->parse('frontend/sidebar', $this->output_data);
                                        
            $this->output_data['post_doQuestMultiplayer'] = site_url('tac_api/doQuestMultiplayer/');
            $this->parser->parse("frontend/quest/{$action_param[$action]}", $this->output_data);
                                        
            $this->load->view('frontend/footer');
        }
    }

    //
    // ─── MISSION ────────────────────────────────────────────────────────────────────
    //


    public function mission()
    {
        $action = $this->input->get('action');
        $action_param = [
            'daily' => 'daily',
            'story' => 'story',
            'event' => 'event',
            'title' => 'title',
            'challenge' => 'challenge',
        ];

        $this->setOutputPostUrl();
        $this->encodeOutputData();
        $this->encodeToken();

        if ($this->expireBot) {
            $this->parser->parse('frontend/header', $this->output_data);
            $this->parser->parse('frontend/sidebar', $this->output_data);
                                        
            $this->output_data['post_doMission'] = site_url('tac_api/doMission/');
            $this->parser->parse("frontend/mission/{$action_param[$action]}", $this->output_data);
                                        
            $this->load->view('frontend/footer');
        }
    }

    //
    // ─── SUMMON ─────────────────────────────────────────────────────────────────────
    //

    public function summon()
    {
        $action = $this->input->get('action');
        $action_param = [
            'normal' => 'normal',
            'unit' => 'unit',
            'gear' => 'gear',
        ];

        $this->setOutputPostUrl();
        $this->encodeOutputData();
        $this->encodeToken();
        
        if ($this->expireBot) {
            $this->parser->parse('frontend/header', $this->output_data);
            $this->parser->parse('frontend/sidebar', $this->output_data);
                                        
            $this->output_data['post_doSummon'] = site_url('tac_api/doSummon/');
            $this->parser->parse("frontend/summon/{$action_param[$action]}", $this->output_data);
                                        
            $this->load->view('frontend/footer');
        }
    }

    //
    // ─── MENU LINK ──────────────────────────────────────────────────────────────────
    //
        

    public function menuLink($id)
    {
        $this->output_data['account_menu_link'] = base_url('page/account/')."?id={$id}";
        $this->output_data['gem_injection_menu_link'] = base_url('page/injection/')."?action=gem&id={$id}";
        $this->output_data['exp_injection_menu_link'] = base_url('page/injection/')."?action=exp&id={$id}";
        $this->output_data['apple_injection_menu_link'] = base_url('page/injection/')."?action=apple&id={$id}";
        $this->output_data['gold_injection_menu_link'] = base_url('page/injection/')."?action=gold&id={$id}";
        $this->output_data['ore_injection_menu_link'] = base_url('page/injection/')."?action=ore&id={$id}";
        $this->output_data['pot_injection_menu_link'] = base_url('page/injection/')."?action=pot&id={$id}";
        $this->output_data['shard_injection_menu_link'] = base_url('page/injection/')."?action=shard&id={$id}";
        $this->output_data['ticket_injection_menu_link'] = base_url('page/injection/')."?action=ticket&id={$id}";
        $this->output_data['other_injection_menu_link'] = base_url('page/injection/')."?action=other&id={$id}";

        $this->output_data['quest_normal_menu_link'] = base_url('page/quest/')."?action=normal&id={$id}";
        $this->output_data['quest_hard_menu_link'] = base_url('page/quest/')."?action=hard&id={$id}";
        $this->output_data['quest_multiplayer_menu_link'] = base_url('page/quest/')."?action=multiplayer&id={$id}";
        $this->output_data['quest_event_menu_link'] = base_url('page/quest/')."?action=event&id={$id}";

        $this->output_data['mission_daily_menu_link'] = base_url('page/mission/')."?action=daily&id={$id}";
        $this->output_data['mission_story_menu_link'] = base_url('page/mission/')."?action=story&id={$id}";
        $this->output_data['mission_event_menu_link'] = base_url('page/mission/')."?action=event&id={$id}";
        $this->output_data['mission_title_menu_link'] = base_url('page/mission/')."?action=title&id={$id}";
        $this->output_data['mission_challenge_menu_link'] = base_url('page/mission/')."?action=challenge&id={$id}";

        $this->output_data['summon_normal_menu_link'] = base_url('page/summon/')."?action=normal&id={$id}";
        $this->output_data['summon_unit_menu_link'] = base_url('page/summon/')."?action=unit&id={$id}";
        $this->output_data['summon_gear_menu_link'] = base_url('page/summon/')."?action=gear&id={$id}";
    }

    public function checkData($data)
    {
        return ($data == '') ? '' : $data;
    }

    public function checkExpireBot($date_buy, $available_day)
    {
        $expire = date("Y-m-d H:i:s", strtotime("+".$available_day." day", strtotime($date_buy)));
        $now = date("Y-m-d H:i:s");
        if (strtotime($now) >= strtotime($expire)) {
            $this->parser->parse('frontend/header', $this->output_data);
            $this->parser->parse('frontend/sidebar', $this->output_data);
            $this->load->view('frontend/container/expired');
            $this->load->view('frontend/footer');
            return false;
        } else {
            return $expire;
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

/* End of file Frontend.php */
