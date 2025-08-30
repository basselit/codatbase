<?php

namespace Codatsoft\Codatbase\Network;

use Codatsoft\Codatbase\Accounts\AccountCredential;
use stdClass;

class TFilterBase
{
    //private TMerchant $curMerch;
    private string $urlTemplate;

    protected TNetworkParameters $fullParameters;

    public string $baseUrl;
    public string $fullUrl;
    public string $authToken = '';
    public string $extraHeader = '';
    public string $endPoint;
    public string $authType = 'Bearer ';
    public string $authValue = '';
    public string $authUser = 'root';
    protected ?AccountCredential $creds;
    public bool $isPost;
    public ?stdClass $postData;

    public function __construct()
    {
        //$this->curMerch = $curMerch;
        //$this->authToken = $curMerch->gatewayPasswordToken;
        //$this->baseUrl = $baseUrl;
        $this->isPost = false;
        $this->postData = null;
        $this->fullParameters = new TNetworkParameters();

    }

    public function create(string $baseUrl, string $authToken = ''): void
    {
        $this->baseUrl = $baseUrl;
        $this->authToken = $authToken;

        if ($authToken != '')
        {
            $this->authValue = $this->authType . $this->authToken;
        }

    }

    public function build(AccountCredential $accCredential = null): void
    {
        if ($accCredential != null)
        {
            $this->creds = $accCredential;
            $this->authToken = $this->creds->getGatewayToken();
            $this->extraHeader = $this->creds->getExtraHeader();
        } else
        {
            $this->creds = null;
        }

        $this->setUrlTemplate();
        $this->fullUrl = $this->parseUrl();
    }


    public function setPost(stdClass $postData)
    {
        $this->isPost = true;
        $this->postData = $postData;
    }


    protected function parseUrl(): string
    {

        $newUrl = $this->urlTemplate;

        foreach ($this->fullParameters as $oneParam)
        {
            if ($oneParam->parameterTitle == "{AUTH_USER}")
            {
                $this->authUser = $oneParam->parameterValue;
                $newUrl = str_replace($oneParam->parameterTitle ,'' ,$newUrl);
            } else
            {
                $newUrl = str_replace($oneParam->parameterTitle ,$oneParam->parameterValue ,$newUrl);
            }

        }


//        foreach ($this->params as $key => $value)
//        {
//            $newUrl = str_replace($value,$this->paramValues[$key],$newUrl);
//        }

        if ($this->creds != null)
        {
            $this->authValue = $this->creds->getAuthValue();
        }


        return $newUrl;

    }

    public function addParameter(string $paramTitle, string $paramValue): void
    {
        $this->fullParameters->addParameter($paramTitle,$paramValue);

    }

    protected function setUrlTemplate(): void
    {
        $this->urlTemplate = $this->baseUrl . $this->endPoint;
    }



}
