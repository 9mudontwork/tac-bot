<?php

defined('BASEPATH') or exit('No direct script access allowed');

class TAC_API extends CI_Controller
{
    private $tac_url = 'https://app.alcww.gumi.sg/';
    private $app_ver = 1722;
    private $asset_ver = '44e27f254378a6f7d23d42ca2efc62ac73f6dbb9_gumi';

    private $user_agent = '';
    private $header = [];
    private $body = '';

    private $platform = '';
    private $device_id = '';
    private $secret_key = '';
    private $token = '';
    private $device_id_ap = '';
    private $secret_key_ap = '';

    private $user_agent_ios = [
                'Mozilla/5.0 (iPad; CPU OS 10_2_1 like Mac OS X) AppleWebKit/602.4.6 (KHTML, like Gecko) Version/10.0 Mobile/14D27 Safari/602.1',
                'Mozilla/5.0 (iPhone; CPU iPhone OS 10_2_1 like Mac OS X) AppleWebKit/602.4.6 (KHTML, like Gecko) Version/10.0 Mobile/14D27 Safari/602.1',
                'Mozilla/5.0 (iPhone; CPU iPhone OS 10_2 like Mac OS X) AppleWebKit/602.3.12 (KHTML, like Gecko) Version/10.0 Mobile/14C92 Safari/602.1',
                'Mozilla/5.0 (iPhone; CPU iPhone OS 10_1_1 like Mac OS X) AppleWebKit/602.2.14 (KHTML, like Gecko) Version/10.0 Mobile/14B100 Safari/602.1',
                'Mozilla/5.0 (iPhone; CPU iPhone OS 10_0_2 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Mobile/14A456 Safari/602.1',
                'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_3 like Mac OS X) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.0 Mobile/14G60 Safari/602.1',
                'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_2 like Mac OS X) AppleWebKit/603.2.4 (KHTML, like Gecko) Version/10.0 Mobile/14F89 Safari/602.1',
                'Mozilla/5.0 (iPad; CPU OS 10_0_2 like Mac OS X) AppleWebKit/602.1.50 (KHTML, like Gecko) Version/10.0 Mobile/14A456 Safari/602.1',
                'Mozilla/5.0 (iPad; CPU OS 10_2 like Mac OS X) AppleWebKit/602.3.12 (KHTML, like Gecko) Version/10.0 Mobile/14C92 Safari/602.1',
                'Mozilla/5.0 (iPad; CPU OS 10_1_1 like Mac OS X) AppleWebKit/602.2.14 (KHTML, like Gecko) Version/10.0 Mobile/14B100 Safari/602.1',
                'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
                'Mozilla/5.0 (iPad; CPU OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1',
        ];
    private $user_agent_android = [
                'Mozilla/5.0 (Linux; U; Android 4.0.4; pt-br; MZ608 Build/7.7.1-141-7-FLEM-UMTS-LA) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Safari/534.30',
                'Mozilla/5.0 (Linux; U; Android 4.2.2; en-us; SM-T217S Build/JDQ39) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Safari/534.30',
                'Mozilla/5.0 (Linux; U; Android 4.1.2; en-us; SCH-I915 Build/JZO54K) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Safari/534.30',
                'Mozilla/5.0 (Linux; U; Android 4.0.3; ko-kr; LG-L160L Build/IML74K) AppleWebkit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
                'Mozilla/5.0 (Linux; U; Android 4.1.2; en-us; SGH-T599N Build/JZO54K) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
                'Mozilla/5.0 (Linux; U; Android 4.4.2; en-us; SCH-I535 Build/KOT49H) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
                'Mozilla/5.0 (Linux; U; Android 4.2.2; en-us; GT-P5210 Build/JDQ39) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Safari/534.30',
                'Mozilla/5.0 (Linux; U; Android 4.1.2; en-us; LGMS769 Build/JZO54K) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
                'Mozilla/5.0 (Linux; U; Android 4.1.2; en-us; SAMSUNG-SGH-I497 Build/JZO54K) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Safari/534.30',

        ];

    public function __construct()
    {
        parent::__construct();
        $this->load->library('encryption');
        $this->load->model('database_model');

        $platform = $this->input->post('platform');
        $this->setUserAgent($platform);
    }

    //................................................................
    //.TTTTTTTTTTT..OOOOOOO....OKKK....KKKKKKEEEEEEEEEE.EENNN...NNNN..
    //.TTTTTTTTTTT.OOOOOOOOOO..OKKK...KKKKK.KEEEEEEEEEE.EENNN...NNNN..
    //.TTTTTTTTTTTOOOOOOOOOOOO.OKKK..KKKKK..KEEEEEEEEEE.EENNNN..NNNN..
    //....TTTT...TOOOO...OOOOO.OKKK.KKKKK...KEEE........EENNNNN.NNNN..
    //....TTTT...TOOO.....OOOOOOKKKKKKKK....KEEE........EENNNNN.NNNN..
    //....TTTT...TOOO......OOOOOKKKKKKK.....KEEEEEEEEEE.EENNNNNNNNNN..
    //....TTTT...TOOO......OOOOOKKKKKKKK....KEEEEEEEEEE.EENNNNNNNNNN..
    //....TTTT...TOOO......OOOOOKKKKKKKKK...KEEEEEEEEEE.EENN.NNNNNNN..
    //....TTTT...TOOO.....OOOOOOKKKK.KKKK...KEEE........EENN.NNNNNNN..
    //....TTTT...TOOOOO..OOOOO.OKKK..KKKKK..KEEE........EENN..NNNNNN..
    //....TTTT....OOOOOOOOOOOO.OKKK...KKKKK.KEEEEEEEEEEEEENN..NNNNNN..
    //....TTTT.....OOOOOOOOOO..OKKK....KKKK.KEEEEEEEEEEEEENN...NNNNN..
    //....TTTT......OOOOOOO....OKKK....KKKKKKEEEEEEEEEEEEENN....NNNN..
    //................................................................

    public function getToken()
    {
        $id = $this->input->post('id');
        $device_id = $this->input->post('device_id');
        $secret_key = $this->input->post('secret_key');

        $id = $this->decryptAES(base64_decode($id));
        $device_id = $this->decryptAES(base64_decode($device_id));
        $secret_key = $this->decryptAES(base64_decode($secret_key));

        $this->setHeader();
        $this->setBody('
							{
									"ticket": '.$this->randomTicket().',
									"access_token": "",
									"param": {
											"device_id": "'.$device_id.'",
											"secret_key": "'.$secret_key.'",
											"idfa": "'.$this->randomTransaction().'"
									}
							}
					');

        $tac_res = $this->composeTo('token');
        $res = json_decode($tac_res, true);

        if (isset($res['body']['access_token'])) {
            $this->database_model->updateToken($id, $res['body']['access_token']);
            $output = $tac_res;
        } else {
            $output = $this->error($res['stat_msg'], 'token');
        }

        $this->output
            ->set_header('HTTP/1.1 200 OK')
            ->set_content_type('application/json', 'utf-8')
            ->set_output($output);
    }

    public function getTokenAPToUpdate($id, $device_id, $secret_key)
    {
        $this->setHeader();
        $this->setBody('
						{
								"ticket": '.$this->randomTicket().',
								"access_token": "",
								"param": {
										"device_id": "'.$device_id.'",
										"secret_key": "'.$secret_key.'",
										"idfa": "'.$this->randomTransaction().'"
								}
						}
				');

        $tac_res = $this->composeTo('token');
        $res = json_decode($tac_res, true);

        if (isset($res['body']['access_token'])) {
            $this->database_model->updateTokenAP($id, $res['body']['access_token']);
            return $tac_res;
        } else {
            return $this->error($res['stat_msg'], 'token');
        }
    }

    public function getTokenAP()
    {
        $id = $this->input->post('id');
        $device_id_ap = $this->input->post('device_id_ap');
        $secret_key_ap = $this->input->post('secret_key_ap');

        $id = $this->decryptAES(base64_decode($id));

        if ($device_id_ap == '' || $secret_key_ap == '') {
            $resDoRegister = $this->doRegister();
            $decodeResDoRegister = json_decode($resDoRegister, true);

            if (isset($decodeResDoRegister['body']['device_id'])) {
                $device_id_ap = $decodeResDoRegister['body']['device_id'];
                $secret_key_ap = $decodeResDoRegister['body']['secret_key'];

                $resGetTokenAP = $this->getTokenAPToUpdate($id, $device_id_ap, $secret_key_ap);
                $decodeResGetTokenAP = json_decode($resGetTokenAP, true);
                // ดึง token ไอดีใหม่

                if (isset($decodeResGetTokenAP['body']['access_token'])) {
                    $token = $decodeResGetTokenAP['body']['access_token'];

                    $resPlayNew = $this->doPlayNew($token, $device_id_ap);
                    $decodeResPlayNew = json_decode($resPlayNew, true);
                    // ยืนยันการเล่นไอดีใหม่

                    if (isset($decodeResPlayNew['body']['player'])) {
                        $output = $resGetTokenAP;
                        $this->database_model->updateDeviceSecretAp($id, $device_id_ap, $secret_key_ap);
                        // สมัครไอดีใหม่พร้อมอัพเดท id,key
                    } else {
                        $this->database_model->updateDeviceSecretAp($id, '', '');
                        $output = $resPlayNew;
                    }
                    // ยืนยันไอดีใหม่สำเร็จ ส่ง res token ออกไป ถ้าไม่ผ่าน เคลีย id,key ใน db ส่ง error ออกไป
                } else {
                    $output = $resGetTokenAP;
                    // error get token
                }
            } else {
                $output = $resDoRegister;
                // error register
            }
        } else {
            $output = $this->getTokenAPToUpdate($id, $device_id_ap, $secret_key_ap);
            // มี id,key อยู่แล้ว get token อัพเดทและส่งออกไป
        }

        $this->output
            ->set_header('HTTP/1.1 200 OK')
            ->set_content_type('application/json', 'utf-8')
            ->set_output($output);
    }

    public function doRegister()
    {
        $udid_idfa = $this->randomTransaction();
        $secret_key = $this->randomTransaction();

        $this->setHeader();

        $this->setBody('
						{
								"ticket": '.$this->randomTicket().',
								"access_token": "",
								"param": {
										"secret_key": "'.$secret_key.'",
										"udid": "'.$udid_idfa.'",
										"idfa": "'.$udid_idfa.'"
								}
						}
				');
        $tac_res = $this->composeTo('register');
        $res = json_decode($tac_res, true);

        if (isset($res['body']['device_id'])) {
            $res['body']['secret_key'] = $secret_key;
            $res['body']['permanent_id'] = $udid_idfa;
            $res = json_encode($res);
            return $res;
        } else {
            return $this->error($res['stat_msg'], 'register');
        }
    }

    public function doPlayNew($token, $permanent_id)
    {
        $header = [
                        'Authorization: gumi '.$token.''
                ];
        $this->setHeader($header);
        $this->setBody('
						{
								"ticket":'.$this->randomTicket().',
								"param":{
								"permanent_id":"'.$permanent_id.'"
								}
						}
				');

        $tac_res = $this->composeTo('playnew');
        $res = json_decode($tac_res, true);
        if (isset($res['body']['player'])) {
            $res = json_encode($res);
            return $res;
        } else {
            return $this->error($res['stat_msg'], 'playnew');
        }
    }

    //
    // ─── GET INFO ───────────────────────────────────────────────────────────────────
    //


    public function getInfo()
    {
        $id = $this->input->post('id');
        $token = $this->input->post('token');

        $id = $this->decryptAES(base64_decode($id));

        $header = [
            'Authorization: gumi '.$token.'',
        ];
        $this->setHeader($header);

        $this->setBody('
            {
                "ticket": '.$this->randomTicket().',
                "param": {
                    "device": "'.$this->randomModelDevice().'"
                }
            }
        ');
        $tac_res = $this->composeTo('login');
        $res = json_decode($tac_res, true);

        if (isset($res['body']['player']['btlid'])) {
            $this->database_model->updateBtlid($id, $res['body']['player']['btlid']);
        }

        if (isset($res['body']['player'])) {
            $output = $tac_res;
        } elseif (isset($res['body']['cuid'])) {
            $output = $this->error('account was suspended');
        } else {
            $output = $this->error($res['stat_msg']);
        }

        $this->output
            ->set_header('HTTP/1.1 200 OK')
            ->set_content_type('application/json', 'utf-8')
            ->set_output($output);
    }

    //..................................................................................................
    //.IIIII.NNNN...NNNN......JJJJ..EEEEEEEEEEE...CCCCCCC....TTTTTTTTTTTIIII...OOOOOOO.....NNNN...NNNN..
    //.IIIII.NNNNN..NNNN......JJJJ..EEEEEEEEEEE..CCCCCCCCC...TTTTTTTTTTTIIII..OOOOOOOOOO...NNNNN..NNNN..
    //.IIIII.NNNNN..NNNN......JJJJ..EEEEEEEEEEE.CCCCCCCCCCC..TTTTTTTTTTTIIII.OOOOOOOOOOOO..NNNNN..NNNN..
    //.IIIII.NNNNNN.NNNN......JJJJ..EEEE........CCCC...CCCCC....TTTT...TIIII.OOOOO..OOOOO..NNNNNN.NNNN..
    //.IIIII.NNNNNN.NNNN......JJJJ..EEEE.......ECCC.....CCC.....TTTT...TIIIIIOOOO....OOOOO.NNNNNN.NNNN..
    //.IIIII.NNNNNNNNNNN......JJJJ..EEEEEEEEEE.ECCC.............TTTT...TIIIIIOOO......OOOO.NNNNNNNNNNN..
    //.IIIII.NNNNNNNNNNN......JJJJ..EEEEEEEEEE.ECCC.............TTTT...TIIIIIOOO......OOOO.NNNNNNNNNNN..
    //.IIIII.NNNNNNNNNNN......JJJJ..EEEEEEEEEE.ECCC.............TTTT...TIIIIIOOO......OOOO.NNNNNNNNNNN..
    //.IIIII.NNNNNNNNNNN.JJJJ.JJJJ..EEEE.......ECCC.....CCC.....TTTT...TIIIIIOOOO....OOOOO.NNNNNNNNNNN..
    //.IIIII.NNNN.NNNNNN.JJJJ.JJJJ..EEEE........CCCC...CCCCC....TTTT...TIIII.OOOOO..OOOOO..NNNN.NNNNNN..
    //.IIIII.NNNN..NNNNN.JJJJJJJJJ..EEEEEEEEEEE.CCCCCCCCCCC.....TTTT...TIIII.OOOOOOOOOOOO..NNNN..NNNNN..
    //.IIIII.NNNN..NNNNN.JJJJJJJJ...EEEEEEEEEEE..CCCCCCCCCC.....TTTT...TIIII..OOOOOOOOOO...NNNN..NNNNN..
    //.IIIII.NNNN...NNNN..JJJJJJ....EEEEEEEEEEE...CCCCCCC.......TTTT...TIIII....OOOOOO.....NNNN...NNNN..
    //..................................................................................................
        
    public function doInjection()
    {
        // $quest_id = $this->input->post('quest_id');
        $quest_id = 'CHALLENGE_05_08';
        $token = $this->input->post('token');
        $multiply = $this->input->post('multiply');

        $token = $this->decryptAES(base64_decode($token));
                
        $header = [
            'Authorization: gumi '.$token.'',
        ];
        $this->setHeader($header);

        $this->setBody('
            {
                "ticket": '.$this->randomTicket().',
                "param": {
                    "bingoprogs": ['.$this->multiBingo($quest_id, $multiply).']
                }
            }
        ');
        
        $resBingo = $this->composeTo('bingo');
        $resBingoDecode = json_decode($resBingo, true);

        if (isset($resBingoDecode['body']['player'])) {
            $output = $resBingo;
        } else {
            $output = $this->error($resBingoDecode['stat_msg']);
            // bingo error
        }
                
        $this->output
            ->set_header('HTTP/1.1 200 OK')
            ->set_content_type('application/json', 'utf-8')
            ->set_output($output);
    }

    public function multiBingo($quest_id, $multiply = 1)
    {
        $data = "";
        date_default_timezone_set("America/Los_Angeles");
        $ymd = date("ymd");
        for ($i = 1; $i <= $multiply; $i++) {
            $check = $i == $multiply ? "" : ",";
            $data .= '{
                        "iname": "'.$quest_id.'",
                        "parent": "CHALLENGE_'.$this->randomParent().'",
                        "pts": [10],
                        "ymd": "'.$ymd.'",
                        "rewarded_at": "'.$ymd.'"
                    }'.$check.'';
        }
        return $data;
    }

    public function checkGemInMail($page = 1)
    {
        $token = $this->input->post('token');
        $page = $this->input->post('page');

        $token = $this->decryptAES(base64_decode($token));
                
        $header = [
            'Authorization: gumi '.$token.'',
        ];
        $this->setHeader($header);

        $page = $page;
        $mailId = [];

        $this->setBody('
                {
                    "ticket": '.$this->randomTicket().',
                    "param": {
                        "page": '.$page.',
                        "isPeriod": 1,
                        "isRead": 0
                    }
                }
            ');

        $resMail = $this->composeTo('mail');
        $resMailDecode = json_decode($resMail, true);

        if (isset($resMailDecode['body']['mails']['list'])) {
            foreach ($resMailDecode['body']['mails']['list'] as $mailList) {
                // $mailId[] = $mailList['gifts'];
                foreach ($mailList['gifts'] as $gift) {
                    if (isset($gift['coin'])) {
                        if ($gift['coin'] == 50) {
                            $mailId[] = $mailList['mid'];
                        }
                    }
                }
            }
        } else {
            $output = $this->error($resMailDecode['stat_msg'], 'check gem in mail');
        }

        // return json_encode($mailId, true);
        $output = json_encode($mailId, true);
        $this->output
            ->set_header('HTTP/1.1 200 OK')
            ->set_content_type('application/json', 'utf-8')
            ->set_output($output);
    }

    public function getGemInMail()
    {
        $token = $this->input->post('token');
        $mail_id = $this->input->post('mail_id');

        $token = $this->decryptAES(base64_decode($token));
                
        $header = [
            'Authorization: gumi '.$token.'',
        ];
        $this->setHeader($header);

        $this->setBody('
                {
                    "ticket": '.$this->randomTicket().',
                    "param": {
                        "mailids": '.$mail_id.',
                        "page": 1,
                        "period": 1
                    }
                }
            ');

        $output = $this->composeTo('read');
        $this->output
            ->set_header('HTTP/1.1 200 OK')
            ->set_content_type('application/json', 'utf-8')
            ->set_output($output);
    }

    //.................................................................
    //.....QQQQQQQ....UUUU...UUUU..EEEEEEEEEEE..SSSSSSS....TTTTTTTTTT..
    //...QQQQQQQQQQ...UUUU...UUUU..EEEEEEEEEEE.SSSSSSSSS...TTTTTTTTTT..
    //..QQQQQQQQQQQQ..UUUU...UUUU..EEEEEEEEEEE.SSSSSSSSSS..TTTTTTTTTT..
    //..QQQQQ..QQQQQ..UUUU...UUUU..EEEE.......ESSSS..SSSS.....TTTT.....
    //.QQQQQ.....QQQQ.UUUU...UUUU..EEEE.......ESSSS...........TTTT.....
    //.QQQQ......QQQQ.UUUU...UUUU..EEEEEEEEEE..SSSSSSS........TTTT.....
    //.QQQQ......QQQQ.UUUU...UUUU..EEEEEEEEEE...SSSSSSSSS.....TTTT.....
    //.QQQQ..QQQ.QQQQ.UUUU...UUUU..EEEEEEEEEE.....SSSSSSS.....TTTT.....
    //.QQQQQ.QQQQQQQQ.UUUU...UUUU..EEEE..............SSSSS....TTTT.....
    //..QQQQQ.QQQQQQ..UUUU...UUUU..EEEE.......ESSS....SSSS....TTTT.....
    //..QQQQQQQQQQQQ..UUUUUUUUUUU..EEEEEEEEEEEESSSSSSSSSSS....TTTT.....
    //...QQQQQQQQQQQ...UUUUUUUUU...EEEEEEEEEEE.SSSSSSSSSS.....TTTT.....
    //.....QQQQQQQQQQ...UUUUUUU....EEEEEEEEEEE..SSSSSSSS......TTTT.....
    //............QQQ..................................................
    //.................................................................

    public function makeRoomMultiplayer($token_ap, $quest_id)
    {
        $header = [
            'Authorization: gumi '.$token_ap.''
        ];

        $this->setHeader($header);

        $this->setBody('
            {
                "ticket": '.$this->randomTicket().',
                "param": {
                    "iname": "'.$quest_id.'",
                    "comment": "Welcome to join!",
                    "pwd": "1234",
                    "private": 1
                }
            }
        ');
        
        $url = $this->tac_url;
        $url = $url.'/btl/room/make';

        $tac_res = $this->curl($url, $this->getHeader(), $this->getBody());
        $tac_res_decode = json_decode($tac_res, true);


        if (isset($tac_res_decode['body']['token'])) {
            return $tac_res;
        } else {
            return $this->error($tac_res_decode['stat_msg'], 'room token');
        }
    }

    public function doQuestMultiplayer()
    {
        $quest_id = $this->input->post('quest_id');
        $token = $this->input->post('token');
        $token_ap = $this->input->post('token_ap');
        $btlid = $this->input->post('btlid');

        $token = $this->decryptAES(base64_decode($token));

        // เอา token make room จากไอดีไก่
        $res_makeroom = $this->makeRoomMultiplayer($token_ap, $quest_id);
        $res_makeroom_decode = json_decode($res_makeroom, true);
        if (isset($res_makeroom_decode['body']['token'])) {
            $room_token = $res_makeroom_decode['body']['token'];

            $header = [
            'Authorization: gumi '.$token.''
            ];
            $this->setHeader($header);
            $this->setbody('
                {
                    "ticket": '.$this->randomTicket().',
                    "param": {
                        "iname": "'.$quest_id.'",
                        "partyid": 2,
                        "token": "'.$room_token.'",
                        "host": "0",
                        "plid": "0",
                        "seat": "0",
                        "ticket": '.$this->randomTicket().',
                        "btlparam": {
                            "help": {
                                "fuid": ""
                            }
                        }
                    }
                }
            ');
            $res_req = $this->composeTo('quest_multiplay_req');
            // return $res_req;
            $check_stat = json_decode($res_req, true);
            if ($check_stat['stat'] == "3702") {
                // เควสเก่าไม่จบ ใส่ btl เก่า ส่ง end ไปเคลียก่อน
                $this->setBody('
                {
                    "ticket": '.$this->randomTicket().',
                    "param": {
                        "btlid": '.$btlid.',
                        "btlendparam": {
                            "time": 0,
                            "result": "win",
                            "beats": [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
                            "steals": {
                                "items": [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
                                "golds": [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1]
                            },
                            "missions": [1, 1, 1],
                            "inputs": [],
                            "token": ""
                        },
                        "hpleveldamage": [113, 1, 122]
                    }
                }
            ');
                $res_end = $this->composeTo('quest_multiplay_end');
                // return $res;
            } elseif ($check_stat['stat'] == "5002") {
                $output = $this->error('Token AP has Expired', 'quest multi req');
                $this->output
                    ->set_header('HTTP/1.1 200 OK')
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output($output);
            } else {
                // เอา btl จาก check_stat ถ้า req ผ่าน
                $res_req = $check_stat;
            }
        
            // เซ็ต data end ใส่ btl ลงไป
            if ($check_stat['stat'] != "3702") {
                $this->setBody('
                {
                    "ticket": '.$this->randomTicket().',
                    "param": {
                        "btlid": '.$res_req['body']['btlid'].',
                        "btlendparam": {
                            "time": 0,
                            "result": "win",
                            "beats": [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
                            "steals": {
                                "items": [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
                                "golds": [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1]
                            },
                            "missions": [1, 1, 1],
                            "inputs": [],
                            "token": ""
                        },
                        "hpleveldamage": [113, 1, 122]
                    }
                }
            ');
                $res_end = $this->composeTo('quest_multiplay_end');
            }

            $res_end = json_decode($res_end, true);
            // เพิ่ม token ลงไปใน result
            if ($check_stat['stat'] != "3702") {
                $res_end['drops'] = $res_req['body']['btlinfo']['drops'];
            }
            $res_end = json_encode($res_end);
            $output = $res_end;
        } else {
            $output = $res_makeroom;
        }

        $this->output
            ->set_header('HTTP/1.1 200 OK')
            ->set_content_type('application/json', 'utf-8')
            ->set_output($output);
    }

    //..............................................................................
    //.MMMMMM...MMMMMMIIIII..SSSSSSS.....SSSSSSS...SIIII...OOOOOOO.....NNNN...NNNN..
    //.MMMMMM...MMMMMMIIIII.SSSSSSSSS...SSSSSSSSS..SIIII..OOOOOOOOOO...NNNNN..NNNN..
    //.MMMMMM...MMMMMMIIIII.SSSSSSSSSS..SSSSSSSSSS.SIIII.OOOOOOOOOOOO..NNNNN..NNNN..
    //.MMMMMMM.MMMMMMMIIIIISSSSS..SSSS.SSSSS..SSSS.SIIII.OOOOO..OOOOO..NNNNNN.NNNN..
    //.MMMMMMM.MMMMMMMIIIIISSSSS.......SSSSS.......SIIIIIOOOO....OOOOO.NNNNNN.NNNN..
    //.MMMMMMM.MMMMMMMIIIII.SSSSSSS.....SSSSSSS....SIIIIIOOO......OOOO.NNNNNNNNNNN..
    //.MMMMMMMMMMMMMMMIIIII..SSSSSSSSS...SSSSSSSSS.SIIIIIOOO......OOOO.NNNNNNNNNNN..
    //.MMMMMMMMMMMMMMMIIIII....SSSSSSS.....SSSSSSS.SIIIIIOOO......OOOO.NNNNNNNNNNN..
    //.MMMMMMMMMMMMMMMIIIII.......SSSSS.......SSSSSSIIIIIOOOO....OOOOO.NNNNNNNNNNN..
    //.MMMM.MMMMM.MMMMIIIIISSSS....SSSSSSSS....SSSSSIIII.OOOOO..OOOOO..NNNN.NNNNNN..
    //.MMMM.MMMMM.MMMMIIIIISSSSSSSSSSSSSSSSSSSSSSSSSIIII.OOOOOOOOOOOO..NNNN..NNNNN..
    //.MMMM.MMMMM.MMMMIIIII.SSSSSSSSSS..SSSSSSSSSS.SIIII..OOOOOOOOOO...NNNN..NNNNN..
    //.MMMM.MMMMM.MMMMIIIII..SSSSSSSS....SSSSSSSS..SIIII....OOOOOO.....NNNN...NNNN..
    //..............................................................................

    public function doMission()
    {
        $quest_id = $this->input->post('quest_id');
        $token = $this->input->post('token');
        $token_ap = $this->input->post('token_ap');

        $token = $this->decryptAES(base64_decode($token));

        $header = [
            'Authorization: gumi '.$token.'',
        ];
        $this->setHeader($header);

        date_default_timezone_set("America/Los_Angeles");
        $ymd = date("ymd");

        if (preg_match("/AWARD_RECORD/", $quest_id)) {
            $this->setBody('
                {
                    "ticket": 25,
                    "param": {
                        "trophyprogs": [{
                            "iname": "'.$quest_id.'",
                            "pts": [100],
                            "ymd": "'.$ymd.'",
                            "rewarded_at": "'.$ymd.'"
                        }]
                    }
                }
            ');
            $res_trophy = $this->composeTo('trophy');
        } elseif (preg_match("/CHALLENGE/", $quest_id)) {
            if (preg_match("/CHALLENGE_01/", $quest_id)) {
                $parent = "CHALLENGE_01";
            } elseif (preg_match("/CHALLENGE_02/", $quest_id)) {
                $parent = "CHALLENGE_02";
            } elseif (preg_match("/CHALLENGE_03/", $quest_id)) {
                $parent = "CHALLENGE_03";
            } elseif (preg_match("/CHALLENGE_04/", $quest_id)) {
                $parent = "CHALLENGE_04";
            } elseif (preg_match("/CHALLENGE_05/", $quest_id)) {
                $parent = "CHALLENGE_05";
            }
            $this->setBody('
                {
                    "ticket": '.$this->randomTicket().',
                    "param": {
                        "bingoprogs": [{
                            "iname": "'.$quest_id.'",
                            "parent": "'.$parent.'",
                            "pts": [10],
                            "ymd": "'.$ymd.'"
                        }]
                    }
                }
            ');
            $res_trophy = $this->composeTo('bingo');
        } else {
            $this->setBody('
                {
                    "ticket": '.$this->randomTicket().',
                    "param": {
                        "trophyprogs": [{
                            "iname": "'.$quest_id.'",
                            "pts": [30],
                            "ymd": "'.$ymd.'"
                        }]
                    }
                }
            ');
            $res_trophy = $this->composeTo('trophy');
        }
        
        $res = json_decode($res_trophy, true);
        if (isset($res['body'])) {
            $output = $res_trophy;
        } else {
            $output = $this->error($res['stat_msg'], 'do mission');
        }

        $this->output
            ->set_header('HTTP/1.1 200 OK')
            ->set_content_type('application/json', 'utf-8')
            ->set_output($output);
    }

    //....................................................................................
    //...SSSSSSS....UUUU...UUUU.UMMMMM...MMMMMMMMMMMM...MMMMMM...OOOOOOO.....NNNN...NNNN..
    //..SSSSSSSSS...UUUU...UUUU.UMMMMM...MMMMMMMMMMMM...MMMMMM..OOOOOOOOOO...NNNNN..NNNN..
    //..SSSSSSSSSS..UUUU...UUUU.UMMMMM...MMMMMMMMMMMM...MMMMMM.OOOOOOOOOOOO..NNNNN..NNNN..
    //.SSSSS..SSSS..UUUU...UUUU.UMMMMMM.MMMMMMMMMMMMMM.MMMMMMM.OOOOO..OOOOO..NNNNNN.NNNN..
    //.SSSSS........UUUU...UUUU.UMMMMMM.MMMMMMMMMMMMMM.MMMMMMMOOOOO....OOOOO.NNNNNN.NNNN..
    //..SSSSSSS.....UUUU...UUUU.UMMMMMM.MMMMMMMMMMMMMM.MMMMMMMOOOO......OOOO.NNNNNNNNNNN..
    //...SSSSSSSSS..UUUU...UUUU.UMMMMMMMMMMMMMMMMMMMMMMMMMMMMMOOOO......OOOO.NNNNNNNNNNN..
    //.....SSSSSSS..UUUU...UUUU.UMMMMMMMMMMMMMMMMMMMMMMMMMMMMMOOOO......OOOO.NNNNNNNNNNN..
    //........SSSSS.UUUU...UUUU.UMMMMMMMMMMMMMMMMMMMMMMMMMMMMMOOOOO....OOOOO.NNNNNNNNNNN..
    //.SSSS....SSSS.UUUU...UUUU.UMMM.MMMMM.MMMMMMMM.MMMMM.MMMM.OOOOO..OOOOO..NNNN.NNNNNN..
    //.SSSSSSSSSSSS.UUUUUUUUUUU.UMMM.MMMMM.MMMMMMMM.MMMMM.MMMM.OOOOOOOOOOOO..NNNN..NNNNN..
    //..SSSSSSSSSS...UUUUUUUUU..UMMM.MMMMM.MMMMMMMM.MMMMM.MMMM..OOOOOOOOOO...NNNN..NNNNN..
    //...SSSSSSSS.....UUUUUUU...UMMM.MMMMM.MMMMMMMM.MMMMM.MMMM....OOOOOO.....NNNN...NNNN..
    //....................................................................................

    public function doSummon()
    {
        $banner_id = $this->input->post('banner_id');
        $token = $this->input->post('token');

        $token = $this->decryptAES(base64_decode($token));

        // return $banner_id;
        $header = [
            'Authorization: gumi '.$token.''
        ];
        $this->setHeader($header);
        $this->setBody('
            {
                "ticket": '.$this->randomTicket().',
                "param": {
                    "gachaid": "'.$banner_id.'",
                    "free": 0
                }
            }
        ');
        $res_gacha = $this->composeTo('gacha');
        // return $res_gacha;
        $res = json_decode($res_gacha, true);
        if (isset($res['body']['player'])) {
            $output = $res_gacha;
        } else {
            $output = $this->error($res['stat_msg'], 'do Summon');
        }

        $this->output
            ->set_header('HTTP/1.1 200 OK')
            ->set_content_type('application/json', 'utf-8')
            ->set_output($output);
    }

    // =============================================================================================
    // =============================================================================================
    // =============================================================================================
    // =============================================================================================
    // =============================================================================================
    // =============================================================================================
    // =============================================================================================
    // =============================================================================================
    // =============================================================================================
    // =============================================================================================
    // =============================================================================================
    // get set

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }

    // =============================================================================================

    public function setUserAgent($platform)
    {
        if ($platform == 'android') {
            $this->user_agent = $this->user_agent_android[array_rand($this->user_agent_android)];
        } else {
            $this->user_agent = $this->user_agent_ios[array_rand($this->user_agent_ios)];
        }
    }
    public function getUserAgent()
    {
        return $this->user_agent;
    }

    // =============================================================================================

    public function setHeader($header_param = [])
    {
        $this->header = [
                        'x-app-ver: '.$this->app_ver.'',
                        'User-Agent: '.$this->getUserAgent().'',
                        'X-Unity-Version: 5.3.6p1',
                        'x-asset-ver: '.$this->asset_ver.'',
                        'X-GUMI-TRANSACTION: '.$this->randomTransaction().'',
                        'Content-Type: application/json; charset=utf-8',
                        'Host: app.alcww.gumi.sg',
                        'Connection: Keep-Alive',
                        'Accept-Encoding: gzip',
                ];

        if ($header_param) {
            foreach ($header_param as $header) {
                array_push($this->header, $header);
            }
        }
    }

    public function getHeader()
    {
        return $this->header;
    }

    // =============================================================================================

    // =============================================================================================
    // =============================================================================================
    // =============================================================================================
    // =============================================================================================
    // =============================================================================================
    // =============================================================================================
    // =============================================================================================
    // =============================================================================================
    // =============================================================================================
    // =============================================================================================
    // =============================================================================================
    // ฟังก์ชั่น

    public function error($text, $function = '')
    {
        if ($text == 5002) {
            $text = 'token has expired';
        } else {
            $text = $text;
        }

        $obj = new \stdClass();
        $obj->message = $text;
        $obj->method = $function;

        return json_encode($obj);
    }

    public function randomHex($length)
    {
        $characters = '0123456789abcdef';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function randomParent()
    {
        $result = strtoupper($this->randomHex(8));
        return $result;
    }

    public function randomTransaction()
    {
        $result = $this->randomHex(8);
        $result .= '-';
        $result .= $this->randomHex(4);
        $result .= '-';
        $result .= $this->randomHex(4);
        $result .= '-';
        $result .= $this->randomHex(4);
        $result .= '-';
        $result .= $this->randomHex(12);

        return $result;
    }

    public function randomTicket()
    {
        return rand(100, 0);
    }

    public function randomNumber()
    {
        return rand(999999999, 0);
    }

    public function randomModelDevice()
    {
        $device = [
                        'Samsung SM-C7010',
                        'Samsung SM-A320x',
                        'Samsung SM-A520x',
                        'Samsung SM-A720x',
                        'Samsung SM-C5010',
                        'Samsung SM-G955x',
                        'Samsung SM-G950x',
                        'Samsung SM-G615x',
                        'Samsung SM-J730x',
                        'Samsung SM-N950x',
                        'Samsung SM-A730x',
                ];

        return array_rand($device);
    }

    public function curl($url, $header, $content)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
    //...............................................................................................
    //....CCCCCCC......OOOOOOO....MMMMMM...MMMMMM.PPPPPPPPP.....OOOOOOO......SSSSSSS....EEEEEEEEEEE..
    //...CCCCCCCCC....OOOOOOOOOO..MMMMMM...MMMMMM.PPPPPPPPPP...OOOOOOOOOO...SSSSSSSSS...EEEEEEEEEEE..
    //..CCCCCCCCCCC..OOOOOOOOOOOO.MMMMMM...MMMMMM.PPPPPPPPPPP.OOOOOOOOOOOO..SSSSSSSSSS..EEEEEEEEEEE..
    //..CCCC...CCCCC.OOOOO..OOOOO.MMMMMMM.MMMMMMM.PPPP...PPPP.OOOOO..OOOOO.OSSSS..SSSS..EEEE.........
    //.CCCC.....CCC.OOOOO....OOOOOMMMMMMM.MMMMMMM.PPPP...PPPPOOOOO....OOOOOOSSSS........EEEE.........
    //.CCCC.........OOOO......OOOOMMMMMMM.MMMMMMM.PPPPPPPPPPPOOOO......OOOO.SSSSSSS.....EEEEEEEEEE...
    //.CCCC.........OOOO......OOOOMMMMMMMMMMMMMMM.PPPPPPPPPP.OOOO......OOOO..SSSSSSSSS..EEEEEEEEEE...
    //.CCCC.........OOOO......OOOOMMMMMMMMMMMMMMM.PPPPPPPPP..OOOO......OOOO....SSSSSSS..EEEEEEEEEE...
    //.CCCC.....CCC.OOOOO....OOOOOMMMMMMMMMMMMMMM.PPPP.......OOOOO....OOOOO.......SSSSS.EEEE.........
    //..CCCC...CCCCC.OOOOO..OOOOO.MMMM.MMMMM.MMMM.PPPP........OOOOO..OOOOO.OSSS....SSSS.EEEE.........
    //..CCCCCCCCCCC..OOOOOOOOOOOO.MMMM.MMMMM.MMMM.PPPP........OOOOOOOOOOOO.OSSSSSSSSSSS.EEEEEEEEEEE..
    //...CCCCCCCCCC...OOOOOOOOOO..MMMM.MMMMM.MMMM.PPPP.........OOOOOOOOOO...SSSSSSSSSS..EEEEEEEEEEE..
    //....CCCCCCC.......OOOOOO....MMMM.MMMMM.MMMM.PPPP...........OOOOOO......SSSSSSSS...EEEEEEEEEEE..
    //...............................................................................................

    public function composeTo($where)
    {
        $url = $this->tac_url;

        $url_post = [
                        'chkver' => 'chkver',
                        'token' => 'gauth/accesstoken',
                        'register' => 'gauth/register',
                        'playnew' => 'playnew',
                        'login' => 'login',
                        'trophy' => 'trophy/exec',
                        'gacha' => 'gacha/exec',
                        'bingo' => 'bingo/exec',
                        'mail' => 'mail',
                        'read' => 'mail/read',

                        'quest_make_room' => 'btl/room/make',
                        'quest_req' => 'btl/com/req',
                        'quest_resume' => 'btl/com/resume',
                        'quest_end' => 'btl/com/end',
                        'quest_skip' => 'btl/com/raid2',
                        'quest_make_roon' => 'btl/room/make',
                        'quest_multiplay_req' => 'btl/multi/req',
                        'quest_multiplay_end' => 'btl/multi/end',
                        'quest_make_roon' => 'btl/room/make',

                ];

        $url = $url.$url_post[$where];

        $result = $this->curl($url, $this->getHeader(), $this->getBody());

        return $result;
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

/* End of file TAC_API.php */
