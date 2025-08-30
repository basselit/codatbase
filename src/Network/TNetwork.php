<?php


namespace Codatsoft\Codatbase\Network;

use Codatsoft\Codatbase\Logging\LoggerInterface;
use GuzzleHttp\BodySummarizer;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use Sentry\EventHint;
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
    public string $extraHeader;
    public string $authValue;
    public bool $isPost = false;
    public ?stdClass $postData;
    private $config = [];
    private $headers = [];


    protected $logger;


    public function convertString(string $value): int
    {

    }

    public function convertSomething($value)
    {

    }


    public function __construct(TFilterBase $filter = null, LoggerInterface $logger = null)
    {
        if ($filter != null)
        {
            $this->executeFilter($filter);
        }

        $this->logger = $logger;

    }

    protected function logException(\Throwable $exception, EventHint $myHint = null): void
    {
        if ($this->logger) {
            $this->logger->logException($exception, $myHint);
        }
    }

    public function executeFilter(TFilterBase $oneFilter): stdClass
    {
        $this->extraHeader = $oneFilter->extraHeader;
        $this->fullUrl = $oneFilter->fullUrl;
        $this->authToken = $oneFilter->authToken;
        $this->isPost = $oneFilter->isPost;
        $this->authValue = $oneFilter->authValue;
        $this->postData = $oneFilter->postData;

        if (!str_contains($this->authValue, 'whm'))
        {
            $this->headers['Content-Type'] ='application/json';
            $this->headers['Accept'] ='application/json';

        }

        $this->config['verify'] = false;

        if ($this->extraHeader !== '')
        {
            $parts[] = explode('::',$this->extraHeader);
            $this->headers[$parts[0][0]] = $parts[0][1];
        }

        if ($this->authToken)
        {
            $this->headers['Authorization'] = $this->authValue;
        }

        $this->config['headers'] = $this->headers;
        //$this->config[] = ['headers' => ['Accept' => 'application/json','Content-Type' => 'application/json']];
        if ($this->postData != null)
        {
            $this->config['body'] = json_encode($this->postData);
        }

        $this->readUrlContent();


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

            $this->readUrlContentAuth('');
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

    private function readUrlContent(): void
    {
        //$stack = HandlerStack::create();
        //$stack->remove('http_errors');
        //$stack->unshift(Middleware::httpErrors(new BodySummarizer(1500)), 'http_errors');
        //$client = new Client(['handler' => $stack]);

        $client = new Client();

        $this->orderFound = false;
        $this->success = false;

        $method = 'GET';
        if ($this->isPost)
        {
            $method = 'POST';
        }


        //            "Authorization: whm root:$apiToken",

        //$client->setDefaultOption('verify', false);
        //'exceptions' => false,
        try {

            $res1 = $client->request($method, $this->fullUrl,$this->config);
            $stCode = $res1->getStatusCode();

        } catch(\GuzzleHttp\Exception\ClientException|GuzzleException $e){
            $myHint = new \Sentry\EventHint();
            $myHint->extra = ['fullUrl' => $this->fullUrl, 'responseBody' => $e->getResponse()->getBody()->getContents() ?? 'no response body'];
            $this->logException($e, $myHint);
            $stCode = $e->getCode();
            $this->networkMessage = $e->getMessage();
            $body = $e->getResponse()->getBody()->getContents();
            if ($stCode == 404)
            {
                $response = $e->getResponse();
                $responseBodyAsString = $response->getBody()->getContents();
                $res = json_decode($responseBodyAsString);
                \Sentry\captureMessage('Network client error of 404 :' . $responseBodyAsString);
                if ($res == null)
                {
                    $stCode = 900;
                    $this->orderFound = false;
                } else
                {
                    if (property_exists($res,'details') && $res->details == 'Order not found')
                    {
                        $stCode = 900;
                        $this->orderFound = false;
                    }
                }
            }
            // DBSaveRaw::recordTeckLog('ERROR','NETWORK',1,$e->getResponse()->getBody()->getContents() ?? 'no response body');
        }

        if ($stCode == 200 || $stCode == 201)
        {
            $response_data = $res1->getBody()->getContents();
            //file_put_contents("mmmm.png", $response_data);
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
            $this->message = 'Network Error Cound not read clover orders ' . $this->networkMessage ?? '';
        }

    }

    private function readUrlContentAuth(): void
    {
        $client = new Client();
        $this->orderFound = false;
        $this->success = false;


        $method = 'GET';
        if ($this->isPost)
        {
            $method = 'POST';
        }

        //$client->setDefaultOption('verify', false);
        //'exceptions' => false,
        try {
            if ($this->postData == null)
            {
                $res1 = $client->request($method, $this->fullUrl,['verify' => false, 'headers' => ['Authorization' => $this->authValue]]);

            } else
            {
                $res1 = $client->request($method, $this->fullUrl,['verify' => false, 'headers' => ['Authorization' => $this->authValue], 'body' => json_encode($this->postData)]);
            }

            $stCode = $res1->getStatusCode();

        } catch(\GuzzleHttp\Exception\ClientException $e){
            $myHint = new \Sentry\EventHint();
            $myHint->extra = ['fullUrl' => $this->fullUrl, 'responseBody' => $e->getResponse()->getBody()->getContents() ?? 'no response body'];
            $this->logException($e, $myHint);
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
           // DBSaveRaw::recordTeckLog('ERROR','NETWORK',1,$e->getResponse()->getBody()->getContents() ?? 'no response body');
        } catch (GuzzleException $e) {
            $mess = $e->getMessage();
        }

        if ($stCode == 200)
        {
            $response_data = $res1->getBody()->getContents();
            $res = json_decode($response_data);
            $this->success = true;
            $this->content = $res;
            $this->orderFound = true;
            $logDet = $this->prepareTeckLog();
          //  TeckDatabase::recordTeckLog('INFO','NETWORK',0,$logDet);
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
