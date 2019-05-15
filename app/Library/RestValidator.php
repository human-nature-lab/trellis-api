<?php

namespace App\Library;

use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;
use Illuminate\Http\Response;

class RestValidator extends Validator
{
    public function statusCode()
    {
        return Response::HTTP_BAD_REQUEST;
    }

    public function failedResponse ($response) {
        return $response()->json([
            'err' => $this->errors()
        ], $this->statusCode());
    }
}
