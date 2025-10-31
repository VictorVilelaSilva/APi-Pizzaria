<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class ErrorsController extends ResourceController
{
    public function show404()
    {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Rota não encontrada.'
        ])->setStatusCode(404);
    }
}
