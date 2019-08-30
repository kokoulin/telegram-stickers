<?php
/**
 * Created by PhpStorm.
 * User: kokoulin
 * Date: 14/11/2018
 * Time: 00:45
 */

/**
 * Class Controller for show web page
 *
 * @author  Nikolay kokoulin <nikolay@kokoulin.org>
 * @version GIT: $Id$
 *
 */
class Controller
{

    public $botKey = "";
    public $stickerPackName = "";
    public $apiUrl = "";
    public $userID = 0;
    public $mainURL = "";


    public function translit($s)
    {
        $s = (string)$s;
        $s = strip_tags($s);
        $s = str_replace(array("\n", "\r"), " ", $s);
        $s = preg_replace("/\s+/", ' ', $s);
        $s = function_exists('mb_strtolower') ? mb_strtolower($s) : strtolower($s);
        $s = strtr($s, array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ы' => 'y', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', 'ъ' => '', 'ь' => ''));
        $s = preg_replace("/[^0-9a-z-_ ]/i", "", $s);
        $s = str_replace(" ", "-", $s);
        return $s;
    }

    /**
     * @param $type
     * @param string $message
     * @return mixed
     */
    public function showError($type, $message = '')
    {
        header(($type == 404)?'HTTP/1.1 404 Not found':'HTTP/1.1 400 Bad request');
        header('Content-Type: application/json');
        echo json_encode(['error' => $type, 'message' => $message]);
        die();
    }

    /**
     * @var resource|false
     */
    public $ch;

    /**
     * @param $url
     * @param $params
     * @param string $type
     * @return bool|string
     */
    public function call($url, $params, $type="POST")
    {
        $this->ch = curl_init();
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($this->ch,CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch,CURLOPT_USERAGENT,'JopaSlona-API-client/1.0');
        curl_setopt($this->ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($this->ch,CURLOPT_COOKIE,TRUE);
        curl_setopt($this->ch,CURLOPT_COOKIEFILE,'');
        curl_setopt($this->ch,CURLOPT_COOKIEJAR,'');
        curl_setopt($this->ch,CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($this->ch,CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($this->ch,CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($this->ch, CURLOPT_URL, $url);

        if(sizeof($params) > 0 && is_array($params)) {

            if ($type == "POST") {
                curl_setopt($this->ch, CURLOPT_POST, true);
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($params));
            } else {
                curl_setopt($this->ch, CURLOPT_HTTPGET, true);
                curl_setopt($this->ch, CURLOPT_URL, $url . '/?' . http_build_query($params));
            }
        } else {
            curl_setopt($this->ch, CURLOPT_POST, false);


            curl_setopt($this->ch, CURLOPT_HTTPGET, true);
            curl_setopt($this->ch, CURLOPT_URL, $url . '/?' . http_build_query($params));
        }

        $out=curl_exec($this->ch);
        $info = curl_getinfo($this->ch);
        $code=curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

        return $out;
    }

    public function checkCurlResponse($code)
    {
        $code=(int)$code;
        $errors=array(
            301=>'Moved permanently',
            400=>'Bad request',
            401=>'Unauthorized',
            403=>'Forbidden',
            404=>'Not found',
            500=>'Internal server error',
            502=>'Bad gateway',
            503=>'Service unavailable'
        );

        if($code!=200 && $code!=204) {
            return false;
        }

        return true;
    }
}