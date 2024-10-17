<?php

require '../app.php';

class Bot
{
    protected $baseUrl = 'https://www.idfootballmallcc.com';
    protected $headers = [
        'accept' => '*/*',
        'accept-language' => 'en-US,en;q=0.9,id;q=0.8',
        'cache-control' => 'no-cache',
        'content-type' => 'application/x-www-form-urlencoded; charset=UTF-8',
        'pragma' => 'no-cache',
        'priority' => 'u=1, i',
        'sec-ch-ua' => '"Google Chrome";v="129", "Not=A?Brand";v="8", "Chromium";v="129"',
        'sec-ch-ua-mobile' => '?1',
        'sec-ch-ua-platform' => '"Android"',
        'sec-fetch-dest' => 'empty',
        'sec-fetch-mode' => 'cors',
        'sec-fetch-site' => 'same-origin',
        'x-requested-with' => 'XMLHttpRequest',
        'Referer' => 'https://www.idfootballmallcc.com/home/reg/',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
    ];

    public function homePage()
    {
        $url = "{$this->baseUrl}/f/88926";
        $method = 'GET';
        $body = null;

        $response = App::curlRequest($url, $method, $this->headers, $body);

        return $response;
    }

    public function registerPage()
    {
        $url = "{$this->baseUrl}/home/reg/";
        $method = 'POST';
        $body = null;

        $response = App::curlRequest($url, $method, $this->headers, $body);

        return $response;
    }

    public function register($pageKey, $phoneNumber)
    {
        $url = "{$this->baseUrl}/home/reg/";
        $method = 'POST';
        $body = "spread_id=&pagekey=$pageKey&phone=$phoneNumber&password=hoseaag&password2=hoseaag&nickname=$phoneNumber&inviter_code=88926";

        $response = App::curlRequest($url, $method, $this->headers, $body);

        return $response;
    }
}

for ($i = 0; $i < 2; $i++) {
    App::resetCookie();

    $bot = new Bot();

    $bot->homePage();

    $responseRegisterPage = $bot->registerPage();

    $pageKey = App::subString($responseRegisterPage, 'name="pagekey"  value="', '"');

    $randomNumber = App::generateRandomNumbers(9);
    $phoneNumber = "81$randomNumber";

    $responseRegister = $bot->register($pageKey, $phoneNumber);

    echo $responseRegister . PHP_EOL;
}
