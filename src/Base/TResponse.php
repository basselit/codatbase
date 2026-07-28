<?php

namespace Codatsoft\Codatbase\Base;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use stdClass;

class TResponse
{
    public bool $success = false;
    public string $message = "";
    public int $httpErrorCode = 0;
    public stdClass $data;
    public string $accessToken = "";
    public string $status;
    public int $code;
    public JsonResponse $response;

    public static function from(string $fullError): TResponse
    {
        //$fullError = "successOrError:message:200";
        $me = new self();
        $parts = explode(':',$fullError);
        $me->success = $parts[0] == "success" ? true:false;
        $me->message = $parts[1];
        $me->httpErrorCode = $parts[2];
        
        $me->response = new JsonResponse(['message' => $me->message]);

        return $me;

    }

    public static function fromObject(mixed $object, bool $jsonPretty = false): TResponse
    {
        $me = new self();
        $me->success = true;
        if ($jsonPretty) {
            $me->response = new JsonResponse($object, 200, [], JSON_PRETTY_PRINT);
        } else
        {
            $me->response = new JsonResponse($object);
        }

        return $me;

    }

    public static function fromResourceCollection(ResourceCollection $resourceCollection): TResponse
    {
        $me = new self();
        $me->data = json_decode(json_encode($resourceCollection));
        $me->success = true;
        $me->response = new JsonResponse($me->data);
        return $me;

    }

}
