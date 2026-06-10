<?php

namespace App\Exceptions;

use Exception;

class BusinessException extends Exception
{
    protected $code;
    protected $message;
    protected $data;

    public function __construct(string $message = '', int $code = 400, $data = null)
    {
        $this->message = $message;
        $this->code = $code;
        $this->data = $data;

        parent::__construct($message, $code);
    }

    public function getData()
    {
        return $this->data;
    }

    public function render($request)
    {
        return response()->json([
            'code' => $this->code,
            'message' => $this->message,
            'data' => $this->data,
            'timestamp' => now()->toIso8601String(),
        ], $this->code >= 400 && $this->code < 600 ? $this->code : 400);
    }
}
