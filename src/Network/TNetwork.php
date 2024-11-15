<?php


namespace App\TFramework\Network;


use App\TeckPay\Database\DBSaveRaw;
use App\TeckPay\Database\TeckDatabase;
use GuzzleHttp\Client;
use stdClass;

class TNetwork
{
    public bool $orderFound;
    public bool $success;
    public stdClass $content;
    public array $contents;
    public string $message;

    public string $fullUrl;
    public ?string $authToken;
    public string $networkMessage;

    public function __construct(TFilterBase $filter = null)
    {
        if ($filter != null)
        {
            $this->executeFilter($filter);
        }

    }

    public function executeFilter(TFilterBase $oneFilter): stdClass
    {
        $this->fullUrl = $oneFilter->fullUrl;
        $this->authToken = $oneFilter->authToken;

        $this->readUrlContentAuth();
        $this->processNetworkResult();

        return $this->content;

    }

    public function executeFilters(TFilters $filters): array
    {
        $retArray = array();
        foreach ($filters->elements as $key => $value)
        {
            $oneFilter = $filters->get($key);

            $this->fullUrl = $oneFilter->fullUrl;
            $this->authToken = $oneFilter->authToken;

            $this->readUrlContentAuth();
            $this->processNetworkResult();

            if (!$this->success)
            {
                return $retArray;
            }
            $retArray[] = $this->content;

        }

        return $retArray;


    }

    public function buildFilters(CLFilters $filters): array
    {
        $retArray = array();
        foreach ($filters->elements as $key => $value)
        {
            $oneFilter = $filters->get($key);

            $this->fullUrl = $oneFilter->fullUrl;
            $this->authToken = $oneFilter->merchantToken;

            $this->readUrlContentAuth();
            $this->processNetworkResult();

            if (!$this->success)
            {
                return $retArray;
            }
            $retArray[] = $this->content;

        }

        return $retArray;

    }


    public function buildUrl(string $fullUrl, ?string $authToken, bool $isPost = false, $postData = null)
    {
        $this->fullUrl = $fullUrl;
        $this->authToken = $authToken;

        if ($this->authToken)
        {
            $this->readUrlContentAuth($isPost,$postData);

        } else
        {
            $this->readUrlContent();

        }

    }

    private function readUrlContent()
    {
        $client = new Client();
        $this->orderFound = false;
        $this->success = false;

        //$client->setDefaultOption('verify', false);
        //'exceptions' => false,
        try {
            $res1 = $client->request('GET', $this->fullUrl,['verify' => false]);
            $stCode = $res1->getStatusCode();

        } catch(\GuzzleHttp\Exception\ClientException $e){
            $stCode = $e->getCode();
            $this->networkMessage = $e->getMessage();
            if ($stCode == 404)
            {
                $response = $e->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                $res = json_decode($responseBodyAsString);
                if ($res->details == 'Order not found')
                {
                    $stCode = 900;
                    $this->orderFound = false;
                }
            }
        }

        if ($stCode == 200)
        {
            $response_data = $res1->getBody()->getContents();
            $res = json_decode($response_data);
            $this->success = true;
            $this->content = $res;
            $this->orderFound = true;
        } else if ($stCode == 900) {
            $this->success = false;
            $this->orderFound = false;
            $this->message = 'Order not found';
        } else
        {
            $this->success = false;
            $this->message = 'Network Error Cound not read clover orders ' . $this->networkMessage;
        }

    }

    private function readUrlContentAuth(bool $isPost = false, $postData = null)
    {
        $client = new Client();
        $this->orderFound = false;
        $this->success = false;

        $method = 'GET';
        if ($isPost)
        {
            $method = 'POST';
        }

        //$client->setDefaultOption('verify', false);
        //'exceptions' => false,
        try {
            if ($isPost)
            {
                $res1 = $client->request($method, $this->fullUrl,['verify' => false, 'headers' => ['Authorization' => 'Bearer ' . $this->authToken], 'body' => json_encode($postData)]);
            } else
            {
                $res1 = $client->request($method, $this->fullUrl,['verify' => false, 'headers' => ['Authorization' => 'Bearer ' . $this->authToken]]);
            }

            $stCode = $res1->getStatusCode();

        } catch(\GuzzleHttp\Exception\ClientException $e){
            $myHint = new \Sentry\EventHint();
            $myHint->extra = ['fullUrl' => $this->fullUrl, 'responseBody' => $e->getResponse()->getBody()->getContents() ?? 'no response body'];
            \Sentry\captureException($e, $myHint);
            $stCode = $e->getCode();
            if ($stCode == 404)
            {
                $response = $e->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                $res = json_decode($responseBodyAsString);
                \Sentry\captureMessage('Network client error of 404 :' . $responseBodyAsString);
                if (property_exists($res,'details') && $res->details == 'Order not found')
                {
                    $stCode = 900;
                    $this->orderFound = false;
                }
            }
            DBSaveRaw::recordTeckLog('ERROR','NETWORK',1,$e->getResponse()->getBody()->getContents() ?? 'no response body');
        }

        if ($stCode == 200)
        {
            $response_data = $res1->getBody()->getContents();
            $res = json_decode($response_data);
            $this->success = true;
            $this->content = $res;
            $this->orderFound = true;
            $logDet = $this->prepareTeckLog();
            TeckDatabase::recordTeckLog('INFO','NETWORK',0,$logDet);
        } else if ($stCode == 900) {
            $this->success = true;
            $this->orderFound = false;
        } else
        {
            $this->success = false;
            $this->message = 'Network Error Cound not read clover orders';
        }




    }


    private function processNetworkResult(): bool
    {
        if (property_exists($this->content,'elements'))
        {
            $totalCount = count($this->content->elements);

            if ($totalCount > 999)
            {
                $this->success = false;
                $this->message = 'more than 1000 records were returned';
                return false;
            }

            if ($totalCount == 0)
            {
                $this->success = false;
                $this->message = 'No elements available for this query';
                return false;
            }

        }

        $this->success = true;
        return true;

    }


    private function prepareTeckLog()
    {
        if (property_exists($this->content,'elements'))
        {
            $totRows = count($this->content->elements);
        } else
        {
            $totRows = 0;
        }

        $fullTrac = '';
        $arr = debug_backtrace(!DEBUG_BACKTRACE_PROVIDE_OBJECT|DEBUG_BACKTRACE_IGNORE_ARGS,8);
        for ($iCount = 0;$iCount < count($arr); $iCount++)
        {
            $deOne = $arr[$iCount]['function'];
            if (!str_contains($deOne,'prepareTeckLog') && !str_contains($deOne,'construct') && !str_contains($deOne,'readUrlContentAuth'))
            {
                $fullTrac = $fullTrac . $deOne . ' ==> ';
            }

        }

        $fullDesc = $fullTrac . ' Total Rows: ' . $totRows . ' ==> ' . $this->fullUrl;

        return $fullDesc;


    }

}
