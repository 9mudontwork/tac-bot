<?php 
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set("Asia/Bangkok");

class Database_model extends CI_Model
{
    private $tb_user = 'user';

    public function __construct()
    {
        parent::__construct();
    }

    public function selectUser($data)
    {
        $this->db->select('*');
        $this->db->where($data);
        $this->db->from($this->tb_user);
        $result = $this->db->get()->result_array();
    
        if (empty($result)) {
            return false;
        } else {
            return $result[0];
        }
    }

    public function addUser($data)
    {
        $this->db->insert($this->tb_user, $data);
        $id = $this->db->insert_id();
        if ($id) {
            return $id;
        } else {
            false;
        }
    }

    public function updateUser($data)
    {
        $this->db->set($data);
        $this->db->where('id', $data['id']);
        $this->db->update($this->tb_user);

        if ($this->db->affected_rows() >= 1) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteUser($data)
    {
        $this->db->where('id', $data['id']);
        $this->db->delete($this->tb_user);

        if ($this->db->affected_rows() >= 1) {
            return true;
        } else {
            return false;
        }
    }

    public function selectAllUser()
    {
        $result = $this->db->get($this->tb_user)->result();
        if (empty($result)) {
            return false;
        } else {
            return $result;
        }
    }

    public function updateLastLogin($id)
    {
        $this->db->set('last_login', date("Y-m-d H:i:s"));
        $this->db->where('id', $id);
        $this->db->update($this->tb_user);
    }

    public function updateBtlid($id, $btlid)
    {
        $this->db->set('btlid', $btlid);
        $this->db->where('id', $id);
        $this->db->update($this->tb_user);
    }

    public function updateToken($id, $token)
    {
        $this->db->set('token', $token);
        $this->db->where('id', $id);
        $this->db->update($this->tb_user);
    }

    public function updateTokenAP($id, $token)
    {
        $this->db->set('token_ap', $token);
        $this->db->where('id', $id);
        $this->db->update($this->tb_user);
    }

    public function updateDeviceSecretAp($id, $device_id, $secret_key)
    {
        $this->db->set('device_id_ap', $device_id);
        $this->db->set('secret_key_ap', $secret_key);
        $this->db->where('id', $id);
        $this->db->update($this->tb_user);
    }
}

/* End of file Database_model.php */
