<?php

namespace Codatsoft\Codatbase\Network;

use Codatsoft\Codatbase\Accounts\AccountCredential;
use stdClass;

class TFilterBase
{
    //private TMerchant $curMerch;
    private string $urlTemplate;

    protected TNetworkParameters $initialParameters;
    protected TNetworkParameters $fullParameters;

    public string $fullUrl;
    public string $authToken;
    public string $extraHeader;
    public string $endPoint;
    public string $authType = 'Bearer ';
    public string $authValue;
    public string $authUser = 'root';
    protected ?AccountCredential $creds;
    public bool $isPost;
    public ?stdClass $postData;

    public function __construct()
    {
        //$this->curMerch = $curMerch;
        //$this->authToken = $curMerch->gatewayPasswordToken;
        $this->isPost = false;
        $this->postData = null;


    }

    public function createFilter(string $endPointUrl, AccountCredential $accCredential = null,bool $isPost = false)
    {
        if ($accCredential != null)
        {
            $this->creds = $accCredential;
        } else
        {
            $this->creds = null;
        }

        $this->endPoint = $endPointUrl;
        $this->isPost = $isPost;



        $this->setUrlTemplate();
        $this->processCommon();

    }

    public function setPost(stdClass $postData)
    {
        $this->isPost = true;
        $this->postData = $postData;
    }

    protected function processCommon(): void
    {
        $this->initialParameters = $this->creds->getInitialParameters();

        $this->fullParameters = new TNetworkParameters();

        foreach ($this->initialParameters as $one)
        {
            $this->fullParameters->add($one);
        }




        //$this->paramValues[] = $this->curMerch->gatewayUrl;
       ////// $this->paramValues[] = $this->creds->getGatewayUrl();
        //$this->paramValues[] = $this->curMerch->gatewayMerchantCode;
      //////  $this->paramValues[] = $this->creds->getGatewayCode();

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

        $this->authValue = $this->creds->getAuthValue();
        return $newUrl;

    }

    public function addParameter(string $paramTitle, string $paramValue)
    {
        $this->fullParameters->addParameter($paramTitle,$paramValue);

    }

    protected function setUrlTemplate(): void
    {
        $this->urlTemplate = $this->endPoint;
    }

    public function build(): void
    {
        if ($this->creds != null)
        {
            $this->authToken = $this->creds->getGatewayToken();
            $this->extraHeader = $this->creds->getExtraHeader();
        }

        $this->fullUrl = $this->parseUrl();
    }


}
