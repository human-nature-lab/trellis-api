<?php

namespace App\Library;

use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;
use Illuminate\Http\Response;

class RestValidator extends Validator {

    protected $firstError = null;
    protected $errorTypes = [
        'min'      => Response::HTTP_BAD_REQUEST,
        'exists'   => Response::HTTP_NOT_FOUND,
        'integer'  => Response::HTTP_BAD_REQUEST,
        'unique'   => Response::HTTP_CONFLICT,
        'required' => Response::HTTP_BAD_REQUEST
    ];

    protected function addError($attribute, $rule, $parameters) {

        $message = $this->getMessage($attribute, $rule);
        $message = $this->doReplacements($message, $attribute, $rule, $parameters);

        $lowercaseRule = strtolower($rule);
        if ($this->firstError === null) {
            $this->firstError = $this->errorTypes[$lowercaseRule];
        }

        $customMessage = new MessageBag();
        $customMessage->merge(['code' => $lowercaseRule.'_error']);
        $customMessage->merge(['msg'  => $message]);

        $this->messages->add($attribute, $customMessage);
    }

    public function statusCode() {
        return $this->firstError;
    }
}