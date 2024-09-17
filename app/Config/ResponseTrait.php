<?php

namespace Config;

use App\Helpers\MessageHandler;
use App\Models\LogAccessModel;
use App\Database\LogAcessDB;
use App\Handlers\ErrosCode;
use App\Handlers\ExceptionHandler;

final class ResponseTrait
{
    public static $phpErroCodes = ['PHPERR-PIX1'];

    public static function getBearerToken()
    {
        $request = \Config\Services::request();
        $headers = $request->getServer('HTTP_AUTHORIZATION');

        // HEADER: Get the access token from the header
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    public static function Error(string $code, $errors = [],?string $detail = null)
    {
        $message = '';
        $detail =(new ErrosCode())->getError($code);
        if ($detail) {
            $message = $detail;
        } else {
            $message = $code; 
        }
        
        $response = [
            'success' => false,
            'code'    => $code,
            'message' => $message, 
        ];
    
        if (!empty($errors)) {
            $response['errors'] = [];
            foreach ($errors as $message) {
                $response['errors'][] = $message;
            }
        }
    
        if (!empty($pending)) {
            $response['pending'] = $pending;
        }
    
        $responseService = service('response');
        $responseService->setJSON($response);
        $responseService->setStatusCode(500); 
        $responseService->send();
        die;
    }
}
