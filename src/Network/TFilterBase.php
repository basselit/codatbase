<?php

namespace Codatsoft\Codatbase\Network;

use Codatsoft\Codatbase\Accounts\AccountCredential;

abstract class TFilterBase implements IProcessFilter
{
    //private TMerchant $curMerch;
    private string $urlTemplate;

    protected TNetworkParameters $initialParameters;
    protected TNetworkParameters $fullParameters;

    public string $fullUrl;
    public string $authToken;
    public string $endPoint;
    protected AccountCredential $creds;

    public function __construct(AccountCredential $accCredential)
    {
        //$this->curMerch = $curMerch;
        //$this->authToken = $curMerch->gatewayPasswordToken;
        $this->authToken = $accCredential->getGatewayToken();
        $this->creds = $accCredential;
        $this->setUrlTemplate();
        $this->processCommon();
    }

    protected function processCommon(): void
    {
        $this->initialParameters = $this->creds->getInitialParameters();

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
            $newUrl = str_replace($oneParam->parameterValue ,$oneParam->parameterTitle ,$newUrl);
        }


//        foreach ($this->params as $key => $value)
//        {
//            $newUrl = str_replace($value,$this->paramValues[$key],$newUrl);
//        }

        return $newUrl;

    }

    protected function addParameter(string $paramTitle, string $paramValue)
    {
        $this->fullParameters->addParameter($paramTitle,$paramValue);

    }

    protected function setUrlTemplate(): void
    {
        $this->urlTemplate = $this->endPoint;
    }

    public function process()
    {


    }


}
