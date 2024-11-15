<?php

namespace App\TFramework\Network;

use App\Clover\Business\CLParameters;
use App\TeckPay\Models\TMerchant;

abstract class TFilterBase implements IProcessFilter
{
    private TMerchant $curMerch;
    private string $urlTemplate;

    protected array $params;
    protected array $paramValues;

    public string $fullUrl;
    public string $authToken;
    public string $endPoint;

    public function __construct(TMerchant $curMerch)
    {
        $this->curMerch = $curMerch;
        $this->authToken = $curMerch->gatewayPasswordToken;
        $this->setUrlTemplate();
        $this->processCommon();
    }

    protected function processCommon(): void
    {
        $this->params[] = CLParameters::BASE_URL;
        $this->params[] = CLParameters::MERCHANT_ID;

        $this->paramValues[] = $this->curMerch->gatewayUrl;
        $this->paramValues[] = $this->curMerch->gatewayMerchantCode;

    }

    protected function parseUrl(): string
    {
        $newUrl = $this->urlTemplate;

        foreach ($this->params as $key => $value)
        {
            $newUrl = str_replace($value,$this->paramValues[$key],$newUrl);
        }

        return $newUrl;

    }

    protected function setUrlTemplate()
    {
        $this->urlTemplate = $this->endPoint;
    }

    public function process()
    {


    }


}
