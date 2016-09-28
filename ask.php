<?php

/**
 * @author Kome-Ine Creative
 * @license GPL-3.0
 * @copyright 2016
 */
 
require("simple_html_dom.php");

class askFm
{
    protected $_loggedIn;
    protected $cookies;
    public $lastError;
    public $last_cookies;
    
    public function __construct()
    {
        $this->_loggedIn = false;
        $this->lastError = NULL;
        $this->last_cookies = NULL;
        $this->cookies   = NULL;
    }
    private function http($url, $urlRef = "http://ask.fm/", $post = false, $postData = array(), $csrf = false, $proxy = false, $requireXML = false)
    {
        $userAgent = 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36';
        $ch        = curl_init();
        $h         = array(
            'Accept-Encoding: gzip, deflate, sdch',
            'Host: ask.fm',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Upgrade-Insecure-Requests: 1'
        );
        if($this->cookies !== NULL){
            $h[] = $this->cookies;
        }
        if ($csrf) {
            $h[] = 'X-CSRF-Token: ' . $csrf;
        }
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_USERAGENT => $userAgent,
            CURLOPT_HTTPHEADER => $h,
            CURLOPT_HEADER => TRUE,
            CURLOPT_ENCODING => "gzip",
            
        );
        if($proxy !== FALSE AND $proxy !== NULL){
            $options[CURLOPT_PROXY] = $proxy;
        }
        if ($post) {
            $options[CURLOPT_POST]       = 1;
            $options[CURLOPT_POSTFIELDS] = $postData;
        }
        curl_setopt_array($ch, $options);
        $pagina = curl_exec($ch);

        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $pagina, $matches);
        $cookies = array();
        foreach($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }
        $d = "Cookie: ";
        foreach($cookies as $c => $k){
            $d .= $c . "=" . $k . ";";
        }
        $this->last_cookies = $d;
        curl_close($ch);
        return $pagina;
    }
    
    private function get_token($c = false)
    {
        $pagina  = ($c) ? "http://ask.fm/signup" : "http://ask.fm/account/wall";
        $pagina = $this->http($pagina);
        if($c){
            $r = $this->getStr('name="authenticity_token" value="', '" />', $pagina);
        } else {
            $pattern = '/(name="csrf-token" content=")(.*)(" \/>)/';
            preg_match($pattern, $pagina, $matches);
            $r = (!empty($matches[2])) ? $matches[2] : false;
        }
        return $r;
        
    }
    
    public function ask($nickname, $question, $anon = false)
    {
        $token = $this->get_token();
        $data = array(
            'authenticity_token' => $token,
            'question[question_text]' => $question
        );
        
        if ($anon && $this->_loggedIn)
            $data['question[force_anonymous]'] = 1;
        
        $this->http("http://ask.fm/$nickname/questions/create/", "http://ask.fm/$nickname/", true, $data);
    }
    
    public function login($cookie)
    {
        $this->cookies   = $cookie;
        $return          = $this->http("http://ask.fm/account/wall", "http://ask.fm/", false, false);
        $this->_loggedIn = ($return == "") === FALSE;
        if(preg_match("/{\"error\":/i", $return)){
        	$this->_loggedIn = FALSE;	
        }
        if (!$this->_loggedIn)
            $this->lastError = "Incorrect username or password";
        return $this->_loggedIn;
    }
    public function simsimi($text)
    {
        $userAgent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:30.0) Gecko/20100101 Firefox/30.0';
        $ch        = curl_init();
        $options   = array(
            CURLOPT_URL => "http://rebot.me/ask",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_USERAGENT => $userAgent,
            CURLOPT_HTTPHEADER => array(
                'X-CSRF-Token: 4L6Kc6G7popLprFRCLAWnkVCfAPZ0axM1Xq8vaEF',
                'X-Requested-With: XMLHttpRequest',
                'Cookie: _gat=1; _ga=GA1.2.809778750.1472389502; laravel_session=eyJpdiI6IkhIeVV3VGN4SU9JTStnRXJYTUZYN3c9PSIsInZhbHVlIjoidUFLMWFuVUZ1enM4U1k4WUFhY29lZThvYVpVWnlzVm44XC9WMlpWRGhqZlFuZTJoRHpIcEh2aVV3TUxuaUgzSlwvWHFMZ0hWRnVodnNCZ0xVbFJZbncrdz09IiwibWFjIjoiODFkNjQ0YzRjMzI4MmM2ZjBjMmJiYWM0MGJmMjFjNmQwNmQzMGI0ZmQ4NWI5ZTYzNDNiODUwNzk4YjFmNDUyYyJ9'
            ),
            CURLOPT_HEADER => FALSE,
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => 2
        );
        
        $options[CURLOPT_POST]       = 1;
        $options[CURLOPT_POSTFIELDS] = 'username=simsimi&question=' . $text;
        
        curl_setopt_array($ch, $options);
        $pagina = curl_exec($ch);
        curl_close($ch);
        return $pagina;
        
    }
    public function likeOne($target, $id){
        if ($this->_loggedIn) {
            $token = $this->get_token();
            $a = $this->http("http://ask.fm/{$target}/answers/{$id}/likes", "http://ask.fm/" . $target, true, array(), $token);
            return $a; 
        } else {
            $this->lastError = "Not logged in";
            return false;
        }
    }
    public function likeall($target, $page = 0){
    	if ($this->_loggedIn) {
    	    $token = $this->get_token();
            $pagina = $this->http("http://ask.fm/{$target}/answers/more?page=".$page, "http://ask.fm/account/wall");
            preg_match_all('/AnswerLikeToggle" data-url="\/'.$target.'\/answers\/([0-9]+)\/likes"/', $pagina, $match);
            foreach($match[1] as $id){
            	$a = $this->http("http://ask.fm/{$target}/answers/{$id}/likes", "http://ask.fm/" . $target, true, array(), $token);
            }
        } else {
            $this->lastError = "Not logged in";
            return false;
        }
    }
    public function logout()
    {
        if ($this->_loggedIn) {
            $data = array(
                'commit' => ''
            );
            $this->http("http://ask.fm/logout", "http://ask.fm/account/wall", true, $data);
        }
        $this->_loggedIn = false;
        unlink($this->_cookieFile);
    }
    public function registerAccount($proxy = null, $count = 1, $forceCookies = false){
        if(!$this->_loggedIn){
            $r = array();
            for($i=0;$i<$count;$i++){
                $token = $this->get_token(true);
                $this->cookies = $this->last_cookies;
                $a = json_decode(@file_get_contents('http://api.randomuser.me/?nat=us'), true);
                $u = $a['results'][0]['login'];
                $z = $a['results'][0];
                $username = $u['username'] . rand();
                $email = str_replace("@example.com", $username . "@gmail.com", $z['email']);
                $password = $u['password'] . $u['salt'];
                $name = ucfirst($z['name']['first']) . " " . ucfirst($u['name']['last']);
                $data['authenticity_token'] = $token;
                $data['user[gmt_offset]'] = 420;
                $data['user[login]'] = $username;
                $data['user[name]'] = $name;
                $data['user[password]'] = $password;
                $data['user[email]'] = $email;
                $data['user[born_at_day]'] = rand(1, 28);
                $data['user[born_at_month]'] = rand(1, 12);
                $data['user[born_at_year]'] = rand(1965, 1998);
                $data['user[language_code]'] = "id";
                $send = $this->http('http://ask.fm/signup', 'http://ask.fm/signup', true, $data, false, $proxy);
                if(preg_match("/wall/i", $send)){
                    if($count > 1){
                        $r[] = $this->last_cookies;
                    } else {
                    	if($forceCookies){
                    	   $this->cookies = $this->last_cookies;
                        	$v = $this->get_token();
                            return $this->last_cookies;
                    	} else {
                    	   return $username . ":" . $password;
                    	}
                    }
                } else {
                    $d = $this->getStr('"fields":["', '"]}', $send);
                    if(!empty($d) AND strlen($d) > 0){
                        return $d;
                    } else {
                        return false;   
                    }  
                }
            }
            if($count > 1 AND $forceCookies){
                return $r;
            } else {
                return $username . ":" . $password;
            }
        }
    }
    public function requestRandom($c = 1)
    {
        for ($i = 0; $i < $c; $i++) {
            $token = $this->get_token();
            $a     = $this->http("http://ask.fm/account/inbox/random-question", "http://ask.fm/account/inbox/", true, array(), $token);
        }
    }
    public function fetchQuestions()
    {
        if ($this->_loggedIn) {
            $questions = array();
            $pagina    = $this->http("http://ask.fm/account/inbox", "http://ask.fm/account/wall");
            preg_match_all('/\/account\/private-questions\/([0-9]+)">/', $pagina, $match);
            return $match[1];
        } else {
            $this->lastError = "Not logged in";
            return false;
        }
    }
    function getStr($start, $end, $sc)
    {
        $a = explode($start, $sc);
        $b = explode($end, $a[1]);
        return $b[0];
    }
    public function answer($q)
    {
        if (!$this->_loggedIn) {
            $this->lastError = "Not logged in";
            return false;
        } else {
            foreach ($q as $questionId) {
                $data     = $this->http("http://ask.fm/account/private-questions/" . $questionId, "http://ask.fm/account/inbox");
                $question = str_replace("  ", "", $this->getStr('<div class="question">', '</div>', $data));
                $token    = $this->get_token();
                
                $data = array(
                    'question[answer_text]' => $this->simsimi($question),
                    'authenticity_token' => $token,
                    'question[answer_type]' => '',
                    'question[photo_url]' => ''
                );
                $d =$this->http("http://ask.fm/account/private-questions/" . $questionId, "http://ask.fm/account/private-questions/" . $questionId, true, $data);            	
            }
            return true;
        }
    }
    
    
}
?> 
