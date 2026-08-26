<?php

namespace Codatsoft\Codatbase\Http;

use Codatsoft\Codatbase\Base\PBase;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use JsonSerializable;

final class HTResponse implements Responsable, JsonSerializable
{
    public bool $success = true;
    public ?PBase $data = null;
    public ?string $message = null;

    public function jsonSerialize(): array
    {
        $out = ['success' => $this->success];

        if (!is_null($this->data))
        {
            $out['data'] = $this->data;
        }

        if (!is_null($this->message))
        {
            $out['message'] = $this->message;
        }

        return $out;
    }

    public function toResponse($request): JsonResponse
    {
        //return response()->json($this, 200,[], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return response()->json($this->jsonSerialize(), 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

}
