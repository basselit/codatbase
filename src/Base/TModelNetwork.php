<?php

namespace Codatsoft\Codatbase\Base;

use Codatsoft\Codatbase\Network\TFilterBase;
use Codatsoft\Codatbase\Network\TNetwork;

class TModelNetwork extends TModelBase
{
    public TFilterBase $filter;
    //public string $baseUrl;
    //public string $authToken = '';

    public function create(string $baseUrl, string $authToken = ''): void
    {
        $this->filter = new TFilterBase();
        $this->filter->create($baseUrl,$authToken);
    }

    public function setEndPoint(string $endPoint): void
    {
        $parts = explode(':',$endPoint);
        $theEndPoint = $parts[0];
        $theMethod = $parts[1];

       // $this->filter = new TFilterBase();
       // $this->filter->baseUrl = $this->baseUrl;
       // $this->filter->authToken = $this->authToken;
        $this->filter->endPoint = $theEndPoint;
        $this->filter->addParameter("{BASE_URL}" ,$this->filter->baseUrl);

        if ($theMethod == "post")
        {
            $this->filter->isPost = true;
        } else
        {
            $this->filter->isPost = false;
        }
    }

    public function addParameter(string $title, string $value): void
    {
        $this->filter->addParameter($title, $value);
    }

    public function setPost(\stdClass $postData): void
    {
        $this->filter->setPost($postData);
    }

    public function runFilter(): TNetwork
    {
        $this->filter->build();
        $net = new TNetwork($this->filter);
        return $net;
    }


    public function getDbClass(): string
    {
        // TODO: Implement getDbClass() method.
    }
}