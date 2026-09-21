<?php

namespace Codatsoft\Codatbase\Http;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use JsonSerializable;

/**
 * Base for a typed endpoint response that is its own envelope.
 *
 * A subclass declares the endpoint's data as public typed properties. On the wire it
 * serialises to the same { success, data?, message? } shape HTResponse produces, with the
 * subclass's own properties nested under `data`, so a controller can return the concrete
 * HTRes* type and clients see no change.
 *
 * Only a top-level endpoint response extends this. A part nested inside another response,
 * and a payload carried by an event rather than an HTTP reply, stays a plain class:
 * extending the base there would serialise an envelope inside an envelope.
 *
 * `success` and `message` are reserved property names on every subclass.
 */
abstract class HTResBase implements Responsable, JsonSerializable
{
    public bool $success = true;
    public ?string $message = null;

    /**
     * False only after fail(): a failed reply carries no data, whatever defaults the
     * subclass declares. A reply built by hand with success false keeps its data, which
     * is what an endpoint needs when the client must act on a failure (resume a pending
     * registration, for example).
     */
    protected bool $withData = true;

    /**
     * An expected failure: success false, a message, and no data.
     */
    public static function fail(string $message): static
    {
        $res = new static();
        $res->success = false;
        $res->message = $message;
        $res->withData = false;

        return $res;
    }

    public function jsonSerialize(): array
    {
        $out = ['success' => $this->success];

        if ($this->withData)
        {
            $data = get_object_vars($this);
            unset($data['success'], $data['message'], $data['withData']);

            if ($data !== [])
            {
                $out['data'] = $data;
            }
        }

        if (!is_null($this->message))
        {
            $out['message'] = $this->message;
        }

        return $out;
    }

    public function toResponse($request): JsonResponse
    {
        return response()->json($this->jsonSerialize(), 200, [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

}
