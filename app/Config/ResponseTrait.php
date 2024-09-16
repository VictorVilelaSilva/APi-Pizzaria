<?php

namespace Config;

use App\Helpers\MessageHandler;
use App\Models\LogAccessModel;
use App\Database\LogAcessDB;
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

    public static function saveLogAccess(array $payload, int $statusCode): void
    {
        try {
            $requestInfos = service('request');
            $logAccessModel = new LogAccessModel();
            $isPhpErro = array_search($payload['code'], self::$phpErroCodes) !== false;

            $requestMethod = json_encode($requestInfos->getJSON());
            if ($requestInfos->getMethod(true) == 'GET') {
                $requestMethod = $requestInfos->getGet();
            }

            if (gettype($requestMethod) == 'array') {
                $requestMethod = json_encode($requestMethod);
            }
            $data = [
                'uuid'                => generate_uuid(),
                'created_at'          => date('Y-m-d H:i:s') ?? '',
                'id_log_query'        => $isPhpErro ? '' : session('id_log_query'),
                'created_at'          => date('Y-m-d H:i:s') ?? '',
                'success'             => $payload['success'] ?? '',
                'ip_address'          => $requestInfos->getIPAddress() ?? '',
                'request_method'      => $requestInfos->getMethod(true) ?? '',
                'request_endpoint'    => $requestInfos->getPath() ?? '',
                'status_code'         => $statusCode ?? '',
                'code'                => $payload['code'] ?? '',
                'request_json_body'   => $requestMethod ?? '',
                'response_json_body'  => json_encode($payload) ?? '',
            ];
            if ($isPhpErro) {
                (new LogAcessDB())->savePhpError($data);
                return;
            }
            $logAccessModel->insert($data);
            session()->remove('id_log_query');
        } catch (\Throwable $th) {
            (new ExceptionHandler($th, "ELOGACES0001"))->print();
        }
    }

    public static function Error(string $code, array $errors = [], $detail = [], $pending = [])
    {
        $messageHandler = new MessageHandler();
        $messagePayload = $messageHandler->getMessage($code);

        if (count($detail) > 0) {
            $messagePayload['message'] = $detail[0];
        }

        $response = [
            'success' => false,
            'message' => $messagePayload['message'],
            'code'    => $messagePayload['code'],
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


        self::saveLogAccess($response, $messagePayload['status_code']);

        $responseService = service('response');
        $responseService->setJSON($response);
        $responseService->setStatusCode($messagePayload['status_code']);
        $responseService->send();
        die;
    }

    public static function Success(string $code, array $data = [], array $pagination = [])
    {
        $messageHandler = new MessageHandler();
        $messagePayload = $messageHandler->getMessage($code);

        $response = [
            'success' => true,
            'message' => $messagePayload['message'],
            'code' => $messagePayload['code'],
        ];


        $response['data'] = $data;


        if (!empty($pagination)) {
            $response['pagination'] = $pagination;
        }

        self::saveLogAccess($response, $messagePayload['status_code']);

        $responseService = service('response');
        $responseService->setJSON($response);
        $responseService->setStatusCode($messagePayload['status_code']);
        $responseService->send();
        die;
    }
}
