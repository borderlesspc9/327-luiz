<?php

namespace App\Exceptions;

use Exception;

class customException extends Exception
{
    public function render()
    {
        return response()->json([
            'statusCode' => $this->getCode(),
            'message' => $this->getMessage(),
        ], $this->getCode());
    }
}
