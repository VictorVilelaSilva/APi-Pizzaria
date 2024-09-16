<?php

namespace App\Helpers;

use App\Handlers\Response\ResponseHandler;
use App\Models\MessageModel;
use Config\ErrorTrait;
use Config\ResponseTrait;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class MessageHandler
{
    protected $CI;

    public function getMessage($messageCode)
    {
        $messagesPath = APPPATH; // Substitua pelo caminho correto
        $directory = new RecursiveDirectoryIterator($messagesPath);
        $iterator = new RecursiveIteratorIterator($directory);
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                if (str_contains($file->getPathName(), 'Messages')) {
                    require $file->getPathname();
                    if (isset($arrayCodes[$messageCode])) {
                        return [
                            'status_code' => $arrayCodes[$messageCode][1],
                            'message' => $arrayCodes[$messageCode][0],
                            'code' => $messageCode,
                        ];
                    }
                }
            }
        }
        $messageModel = new MessageModel();
        $dados = $messageModel->getDataById($messageCode);

        if (!$dados) {
         
            throw new ErrorTrait('EMSG00001', ["code" => $messageCode]);
        }
        return [
            'status_code' => $dados['status_code'],
            'message' => $dados['message'],
            'code' => $dados['id'],
        ];
        
    }

    public function withoutPermissionMessage()
    {
        new ErrorTrait("EPERMISS01");
    }
}
