<?php

class App
{
    public static function curlRequest($url, $method, $headers, $body = null, $cookieFilePath = 'cookie.txt', $proxy = null)
    {
        App::saveLog($body);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFilePath);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFilePath);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        curl_close($ch);

        App::saveLog($response);

        return $response;
    }

    public static function saveLog($message)
    {
        $datetime = date('Y-m-d H:i:s');
        $message = "[$datetime] $message";

        file_put_contents('debug.log', $message . PHP_EOL, FILE_APPEND);
    }

    public static function resetCookie($cookieFilePath = 'cookie.txt')
    {
        file_put_contents($cookieFilePath, '');
    }

    public static function subString($haystack, $start, $end)
    {
        $start_pos = strpos($haystack, $start);

        $end_pos = strpos($haystack, $end, $start_pos + strlen($start));

        $length = $end_pos - ($start_pos + strlen($start));

        return trim(substr($haystack, $start_pos + strlen($start), $length));
    }

    public static function generateRandomNumbers($length = 6)
    {
        // Pastikan panjang digit minimal 1
        if ($length < 1) {
            return false;
        }

        // Tentukan batas angka sesuai dengan panjang yang diminta
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;

        // Generate angka acak
        return mt_rand($min, $max);
    }

    public static function getProxyList()
    {
        $proxyListRaw = file_get_contents(__DIR__ . '/proxies.txt');
        $proxyList = explode(PHP_EOL, $proxyListRaw);

        return $proxyList;
    }
}
