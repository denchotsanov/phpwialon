<?php

namespace denchotsanov;

class Wialon
{
/// PROPERTIES
    private $sid = null;
    private $base_api_url = '';
    private $default_params = [];

    /// METHODS
    /** constructor */
    function __construct($scheme = 'http', $host = 'hst-api.wialon.com', $port = '', $sid = '', array $extra_params = [])
    {
        $this->sid = '';
        $this->default_params = array_replace([], (array) $extra_params);
        $this->base_api_url = sprintf('%s://%s%s/ajax.html?', $scheme, $host, mb_strlen($port) > 0 ? ':' . $port : '');
    }

    /** sid setter */
    function set_sid($sid)
    {
        $this->sid = $sid;
    }

    /** sid getter */
    function get_sid()
    {
        return $this->sid;
    }

    /** update extra parameters */
    public function update_extra_params($params)
    {
        $this->default_params = array_replace($this->default_params, $params);
    }

    /*
     * RemoteAPI request performer
     * action - RemoteAPI command name
     * args - JSON string with request parameters
     */
    public function call($action, $args)
    {
        $url = $this->base_api_url;

        $params = [
            'svc'=> preg_replace('\'_\'', '/', $action, 1),
            'params'=> $args,
            'sid'=> $this->sid
        ];

        $all_params = array_replace($this->default_params , $params);

        $str = '';
        foreach ($all_params as $k => $v) {
            if(mb_strlen($str) > 0) {
                $str .= '&';
            }

            $str .= $k . '=' . ( is_object($v) ? json_encode($v) : $v);
        }

        /* cUrl magic */
        $ch = curl_init();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $str
        ];

        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);

        if($result === false) {
            $result = '{"error":-1,"message":'.curl_error($ch).'}';
        }

        curl_close($ch);
        return $result;
    }

    /*
     * Login
     * user - wialon username
     * password - password
     * return - server response
     */
    public function loginByToken($token)
    {
        $data = [
            'token' => urlencode($token),
        ];

        $result = $this->token_login(json_encode($data));

        $json_result = json_decode($result, true);

        if(isset($json_result['eid'])) {
            $this->sid = $json_result['eid'];
        }

        return $result;
    }
    public function loginByUser($username,$password) {
        $data = [
            'username'=>$username,
            'password'=>$password
        ];
        $result = $this->core_logon(json_encode($data));
        $json_result = json_decode($result, true);

        if(isset($json_result['eid'])) {
            $this->sid = $json_result['eid'];
        }

        return $result;
    }

    /*
     * Logout
     * return - server responce
     */
    public function logout()
    {
        $result = $this->core_logout();
        $json_result = json_decode($result, true);

        if($json_result && $json_result['error'] == 0) {
            $this->sid = '';
        }

        return $result;
    }

    /** Unknonwn methods hadler */
    public function __call($name, $args)
    {
        return $this->call($name, count($args) === 0 ? '{}' : $args[0]);
    }

}