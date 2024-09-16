<?php

namespace App\Handlers;

use App\Helpers\MessageHandler;
use Config\ErrorTrait;
use Throwable;

class ExceptionHandler
{
    private Throwable $exception;
    private string $messageCode;
    private string $statusCode;
    private array $data;
    private array $details;

    public function __construct(
        Throwable $exception,
        string $messageCode = "",
        int $statusCode = 500,
        array $data = []
    ) {
        $this->exception   = $exception;
        $this->messageCode = $messageCode;
        $this->statusCode  = $statusCode;
        $this->data        = $data;

        $this->print();
    }

    public function print(): void
    {
        $this->responseDetails();

        $messagePayload = (new MessageHandler())->getMessage($this->messageCode);

        $response = [
            'success' => false,
            'message' => $messagePayload['message'],
            'code' => $messagePayload['code'],
            'details' => $this->details
        ];

        new ErrorTrait($response['code'], $response['details']);
    }

    private function responseDetails(): void
    {
        $this->details["dateTime"] = date('y-m-d H:i:s');

        if ($this->data !== []) {
            $this->details["data"] = $this->data;
        }

        $this->details($this->exception);
    }

    private function details(Throwable $th): void
    {
        $this->details["details"][] = [
            "message" => $th->getMessage(),
            "code"    => $th->getCode(),
            "type"    => get_class($th),
            "file"    => $th->getFile(),
            "line"    => $th->getLine(),
            "trace"   => $th->getTrace()
        ];

        if ($th->getPrevious() !== null) {
            $this->details($th->getPrevious());
        }
    }
}
