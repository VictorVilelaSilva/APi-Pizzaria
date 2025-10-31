<?php

namespace Modules\Pizza\Controllers;

use App\Models\PizzasModel;
use App\UseCases\Login\LoginUseCase;
use CodeIgniter\RESTful\ResourceController;

class PizzaController extends ResourceController
{
    public function GetAllPizzas()
    {
        $pizzasInfos = (new PizzasModel())->getAllPizzas();
        return $this->respond(
            [
                'message'       => 'Pizzas listadas com sucesso',
                'data'          => $pizzasInfos
            ]
        );
    }

    public function EditPizza($pizzaId)
    {
        try {
            $bodyRequest = $this->request->getJSON(true);
            $newPizzaInfos = [
                'nome'         => $bodyRequest['nome'],
                'preco'        => $bodyRequest['preco'],
                'ingredientes' => $bodyRequest['ingredientes'],
                'img_url'      => $bodyRequest['img_url']
            ];
            (new PizzasModel())->update($pizzaId, $newPizzaInfos);
            return $this->respond(
                [
                    'message'       => 'Pizza editada com sucesso',
                    'data'          => $newPizzaInfos
                ]
            );
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}
