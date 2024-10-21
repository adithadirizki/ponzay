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
    protected $cookieFilePath = 'cookie.txt';
    protected $proxy = null;

    public function setCookieFilePath($cookieFilePath)
    {
        $this->cookieFilePath = $cookieFilePath;
    }

    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    public function getAccounts()
    {
        $json = file_get_contents('accounts.json');

        if (empty($json)) $json = '[]';

        $accountList = json_decode($json);

        return $accountList;
    }

    public function saveAccounts($accountList)
    {
        $json = json_encode($accountList);

        file_put_contents('accounts.json', $json);
    }

    public function addAccounts($phoneNumber)
    {
        $accountList = $this->getAccounts();
        $accountList[] = $phoneNumber;

        $this->saveAccounts($accountList);
    }

    public function registerPage()
    {
        $url = "{$this->baseUrl}/home/reg/";
        $method = 'GET';
        $body = null;

        $response = App::curlRequest($url, $method, $this->headers, $body, $this->cookieFilePath, $this->proxy);

        return $response;
    }

    public function loginPage()
    {
        $url = "{$this->baseUrl}/home/login/";
        $method = 'GET';
        $body = null;

        $response = App::curlRequest($url, $method, $this->headers, $body, $this->cookieFilePath, $this->proxy);

        return $response;
    }

    public function register($pageKey, $phoneNumber, $referralCode)
    {
        $url = "{$this->baseUrl}/home/reg/";
        $method = 'POST';
        $body = "spread_id=&pagekey=$pageKey&phone=$phoneNumber&password=hoseaag&password2=hoseaag&nickname=$phoneNumber&inviter_code=$referralCode";

        $response = App::curlRequest($url, $method, $this->headers, $body, $this->cookieFilePath, $this->proxy);

        return $response;
    }

    public function login($pageName, $pageKey, $phoneNumber)
    {
        $url = "{$this->baseUrl}/home/login/";
        $method = 'POST';
        $body = "$pageName=$pageKey&phone=$phoneNumber&password=hoseaag";

        $response = App::curlRequest($url, $method, $this->headers, $body, $this->cookieFilePath, $this->proxy);

        return $response;
    }

    public function getBalance()
    {
        $url = "{$this->baseUrl}/my/";
        $method = 'GET';
        $body = null;

        $response = App::curlRequest($url, $method, $this->headers, $body, $this->cookieFilePath, $this->proxy);

        $balance = App::subString($response, '<code>Rp</code>', '</strong>');

        return $balance;
    }

    public function getOrder()
    {
        $url = "{$this->baseUrl}/order/";
        $method = 'GET';
        $body = null;

        $response = App::curlRequest($url, $method, $this->headers, $body, $this->cookieFilePath, $this->proxy);

        if (strpos($response, 'Waktu mulai:</span>') === false) {
            return '-';
        }

        $orderTime = App::subString($response, 'Waktu mulai:</span>', '</p>');

        return $orderTime;
    }

    public function getFriends()
    {
        $url = "{$this->baseUrl}/my/friends/";
        $method = 'GET';
        $body = null;

        $response = App::curlRequest($url, $method, $this->headers, $body, $this->cookieFilePath, $this->proxy);

        $friends = App::subString($response, '<p><span>', '</span>');

        return $friends;
    }

    public function checkin()
    {
        $url = "{$this->baseUrl}/task/index/clockin/";
        $method = 'POST';
        $body = null;

        $response = App::curlRequest($url, $method, $this->headers, $body, $this->cookieFilePath, $this->proxy);

        return $response;
    }

    public function claimInviteTask($num)
    {
        $url = "{$this->baseUrl}/task/index/invite_reg_task/";
        $method = 'POST';
        $body = "num=$num";

        $response = App::curlRequest($url, $method, $this->headers, $body, $this->cookieFilePath, $this->proxy);

        return $response;
    }

    public function claimInvestTask($num)
    {
        $url = "{$this->baseUrl}/task/index/invite_invest_task/";
        $method = 'POST';
        $body = "num=$num";

        $response = App::curlRequest($url, $method, $this->headers, $body, $this->cookieFilePath, $this->proxy);

        return $response;
    }

    public function getReferralCode()
    {
        $url = "{$this->baseUrl}/team/index/share/";
        $method = 'GET';
        $body = null;

        $response = App::curlRequest($url, $method, $this->headers, $body, $this->cookieFilePath, $this->proxy);

        $referralCode = App::subString($response, 'https://www.idfootballmallcc.com/f/', '</strong>');

        return $referralCode;
    }

    public function createAccount($length = 20)
    {
        for ($i = 0; $i < $length; $i++) {
            App::resetCookie();

            $this->registerPage();

            $responseRegisterPage = $this->registerPage();

            sleep(2);

            $pageKey = App::subString($responseRegisterPage, 'name="pagekey"  value="', '"');

            $randomNumber = App::generateRandomNumbers(9);
            $phoneNumber = "81$randomNumber";
            $referralCode = '60086';

            echo 'Phone number: ' . $phoneNumber . PHP_EOL;

            $responseRegister = $this->register($pageKey, $phoneNumber, $referralCode);

            $responseRegisterObject = json_decode($responseRegister);

            if ($responseRegisterObject->code === 'Error') {
                echo 'Register failed: ' . $responseRegisterObject->msg . PHP_EOL;
            } else {
                echo 'Register successfully: ' . $responseRegisterObject->msg . PHP_EOL;
                $this->addAccounts($phoneNumber);
            }
        }
    }

    public function checkAccount()
    {
        $accountList = $this->getAccounts();

        foreach ($accountList as $index => $phoneNumber) {
            $this->setCookieFilePath('cookies/' . $phoneNumber . '.txt');

            $this->loginPage();

            $responseLoginPage = $this->loginPage();

            $pageName = App::subString($responseLoginPage, 'name="pagekey_', '"  value="');
            $pageName = "pagekey_$pageName";
            $pageKey = App::subString($responseLoginPage, 'name="' . $pageName . '"  value="', '"');

            $responseCheckin = $this->checkin();

            // $responseClaimTask = $this->claimInviteTask(5);
            // $responseClaimTask = $this->claimInviteTask(10);
            // $responseClaimTask = $this->claimInviteTask(20);
            // $responseClaimTask = $this->claimInviteTask(30);

            $this->login($pageName, $pageKey, $phoneNumber);

            $responseGetBalance = $this->getBalance();

            $responseGetOrder = $this->getOrder();

            $responseGetTeam = $this->getFriends();

            $responseGetReferralCode = $this->getReferralCode();

            echo "Phone Number: " . $phoneNumber . PHP_EOL;
            echo "Balance     : " . $responseGetBalance . PHP_EOL;
            echo "Order Time  : " . $responseGetOrder . PHP_EOL;
            echo "Friends     : " . $responseGetTeam . PHP_EOL;
            echo "Reff Code   : " . $responseGetReferralCode . PHP_EOL;
            echo "======================" . PHP_EOL;
        }
    }
}

$bot = new Bot();

// $bot->createAccount(1);

$bot->checkAccount();
