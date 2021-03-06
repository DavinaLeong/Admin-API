<?php
//@codeCoverageIgnoreStart
defined('BASEPATH') OR exit('No direct script access allowed');

class Authentication_model extends CI_Model {

    #region Validate Account Status
    public function check_user_status($user, $possible_statuses) {
        if(is_array($possible_statuses)) {
            $user_account = NULL;
            if( ! array_key_exists('account_status', $user)) {
                $this->load->model('User_model');
                $user_account = $this->User_model->get_by_username($user['username']);
            } else {
                $user_account = $user;
            }

            if(in_array($user['access'], $possible_statuses)) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    public function validate_user_status($user) {
        return $this->check_user_status($user, array('Unverified', 'Active'));
    }

    public function validate_user_active($user) {
        return $this->check_user_status($user, array('Active'));
    }

    private function _resolve_invalid_status() {
        $this->session->set_userdata('message', 'This account has either been Suspended or Deactivated.');
        redirect('admin/authenticate/login');
    }
    #endregion

    #region Validate Access Rights
    public function validate_user_access($access_values, $user_access) {
        $valid = FALSE;
        for ($i = 0; $i < strlen($user_access); $i++)
        {
            if (strpos($access_values, substr($user_access, $i, 1)) !== FALSE)
            {
                $valid = TRUE;
                break;
            }
        }
        return $valid;
    }

    public function validate_access_super_only() {
        $this->load->library('session');
        return $this->validate_user_access("S", $this->session->userdata('access'));
    }

    public function validate_access_admin_only() {
        $this->load->library('session');
        return $this->validate_user_access("A", $this->session->userdata('access'));
    }

    public function validate_access_admin() {
        $this->load->library('session');
        return $this->validate_user_access("SA", $this->session->userdata('access'));
    }

    public function validate_access_user_only() {
        $this->load->library('session');
        return $this->validate_user_access("U", $this->session->userdata('access'));
    }

    public function validate_access_all() {
        $this->load->library('session');
        return $this->validate_user_access("SAU", $this->session->userdata('access'));
    }

    private function _resolve_invalid_access() {
        $this->session->set_userdata('message', 'This user has invalid access rights.');
        redirect('admin/authenticate/login');
    }
    #endregion

} //end Authentication_model class
//@codeCoverageIgnoreEnd