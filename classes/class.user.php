<?php
/**
* Author: Idongesit Ntuk
* Date: 15/6/2017
* Project: calitunesAds
*/
class User extends Api {
    private $_userOk = false;
    private $_data;
    private $_sessionId;

    function __construct($id = null) {
        if (!$id) {
            if (isset($_SESSION['id'])) {
                $id = (int) $_SESSION['id'];

                if ($this->find($id)) {
                    $this->_userOk = true;
                }
            }
        } else {
            $this->find($id);
        }

    }

    public function find($id) {
        if ($id) {
             $id = isset($id) ? $id : $_SESSION['id']; //check if @param is NULL and assign session id to id variable
            $url = 'android_543ASBD/userdetail.php?login=true&uid='.(int)$id; // build api url with id variable
            $api = Api::get($url);

            if ($api->{'success'}) {
                $this->_data = $api->{'usersd'}[0];
                return true;
            }
        }

        return false;
    }

    public function login($username, $password) {
        $url = 'android_543ASBD/loginapi.php?useremail='.$username.'&password='.$password;
        $api = Api::get($url);

        if ($api->{'success'} === 1) {
            $userdetails = $api->{'userlogin'}[0];
            //setting sessions variable
            $_SESSION['id'] = $userdetails->{'idu'};
            $_SESSION['username'] = $userdetails->{'username'};
            $_SESSION['password'] = $userdetails->{'password'};

            return true;
        }

        return false;
    }

    public function ads($user_id = null)
    {
        $db = DB::get_instance();
        $user_id = isset($user_id) ? $user_id : (int) $_SESSION['id'];

        $db->get('promotions', array('user_id', '=', $user_id));
        return $db->results();
    }

    public function active_and_running_ads($user_id = null)
    {
        $db = DB::get_instance();
        $user_id = isset($user_id) ? $user_id : (int) $_SESSION['id'];

        $db->query("SELECT * FROM promotions WHERE user_id = ? AND status = ? AND payment_status = ?", [$user_id, 1, 1]);
        return $db->results();
    }

    public function pending_ads($user_id = null)
    {
        $db = DB::get_instance();
        $user_id = isset($user_id) ? $user_id : (int) $_SESSION['id'];

        $db->query("SELECT * FROM promotions WHERE user_id = ? AND status = ?", [$user_id, 0]);
        return $db->results();
    }

    public function expired_ads($user_id = null)
    {
        $db = DB::get_instance();
        $user_id = isset($user_id) ? $user_id : (int) $_SESSION['id'];

        $db->query("SELECT * FROM promotions WHERE user_id = ? AND status = ?", [$user_id, 2]);
        return $db->results();
    }

    public function sites($user_id = null)
    {
        $db = DB::get_instance();
        $user_id = isset($user_id) ? $user_id : (int) $_SESSION['id'];

        $db->get('bloggers', array('b_user_id', '=', $user_id));
        return $db->results();
    }

    public function payment($user_id = null)
    {
        $db = DB::get_instance();
        $user_id = isset($user_id) ? $user_id : (int) $_SESSION['id'];
        $data = $db->query('SELECT * FROM payments WHERE user_id = ? ORDER BY id DESC', [$user_id]);

        if ($data->count()) {
            return $data->results();
        }
    }

    public function earnings($user_id = null)
    {
        $db = DB::get_instance();
        $user_id = isset($user_id) ? $user_id : (int) $_SESSION['id'];
        $data = $db->query('
            SELECT SUM(earnings) AS earnings
            FROM bloggers
            WHERE b_user_id = ? AND b_status = ? AND verified = ?', [$user_id, 1, 1]);

        return $data->first()->{'earnings'};
    }

    public function get_payment_method($user_id = null)
    {
        $db = DB::get_instance();
        $user_id = isset($user_id) ? $user_id : (int) $_SESSION['id'];
        $data = $db->query('SELECT * FROM bank_details WHERE user_id = ?', [$user_id]);

        if ($data->count() > 0) {
            return $data->first();
        }
    }

    public function add_payment_method($data = [])
    {
        $db = DB::get_instance();

        if (!$db->insert('bank_details', $data)) {
            throw new Exception("Error Processing Request", 1);

        }
    }

    public function edit_payment_method($data = [], $id)
    {
        $db = DB::get_instance();

        if (!$db->update('bank_details', $id, $data)) {
            throw new Exception("Error Processing Request", 1);

        }
    }

    public function request_payment($user_id = null)
    {
        $db = DB::get_instance();
        $user_id = isset($user_id) ? $user_id : $_SESSION['id'];
        $withdrawable_amount = (($this->earnings() * Utility::getSetting('request-percent')) / 100); // calculate percentage
        $user_total_sites = $this->sites();
        $validation = false;
        $remaining_withdrawal = $withdrawable_amount;

        foreach ($user_total_sites as $site) {
            $amount_to_withdraw = $site->earnings;

            if ($amount_to_withdraw > $remaining_withdrawal) {
                $amount_to_withdraw = $remaining_withdrawal;
            }

            if ($db->query("
                UPDATE bloggers
                SET earnings = earnings - ?
                WHERE id = ? AND b_status = ? AND verified = ?
                LIMIT 1", [$amount_to_withdraw, $site->id, 1, 1])) {
                $remaining_withdrawal = $remaining_withdrawal - $amount_to_withdraw;
                $validation = true;
            }
        }

        if ($validation == true && $remaining_withdrawal == 0) {
            $data = [
                'user_id' => $user_id,
                'amount' => $withdrawable_amount,
                'time_created' => date('Y-m-d H:i:s')
            ];

            if ($db->insert('payments', $data)) {
                echo 1;
            }
        }
    }

    public function has_pending_request($user_id = null)
    {
        $db = DB::get_instance();
        $user_id = isset($user_id) ? $user_id : $_SESSION['id'];
        $data = $db->query('SELECT id FROM payments WHERE user_id = ? AND status = ? LIMIT 1', [$user_id, 0]);

        if ($data->count()) {
            return true;
        }
    }

    public function user_ok() {
        return $this->_userOk;
    }

    public function logout () {
        session_destroy();
        return true;
    }

    public function data() {
        return $this->_data; // returns user data
    }
}
?>