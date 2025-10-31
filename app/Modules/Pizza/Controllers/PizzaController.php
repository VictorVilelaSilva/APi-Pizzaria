<?php

namespace Modules\Pizza\Controllers;

use App\Models\PizzasModel;
use CodeIgniter\RESTful\ResourceController;

class PizzaController extends ResourceController
{
    public function GetAllPizzas()
    {
        helper('jwt'); // Carrega o helper JWT

        $pizzasInfos = (new PizzasModel())->getAllPizzas();

        // Exemplo: você pode acessar dados do usuário autenticado
        $authenticatedUser = getAuthenticatedUser();

        return $this->respond(
            [
                'message' => 'Pizzas listadas com sucesso',
                'data' => $pizzasInfos,
                'authenticated_user' => [
                    'id' => $authenticatedUser->id ?? null,
                    'email' => $authenticatedUser->email ?? null
                ]
            ]
        );
    }

    public function EditPizza($pizzaId)
    {
        try {
            helper('jwt'); // Carrega o helper JWT

            $bodyRequest = $this->request->getJSON(true);
            $newPizzaInfos = [
                'nome'         => $bodyRequest['nome'],
                'preco'        => $bodyRequest['preco'],
                'ingredientes' => $bodyRequest['ingredientes'],
                'img_url'      => $bodyRequest['img_url']
            ];

            (new PizzasModel())->update($pizzaId, $newPizzaInfos);

            // Exemplo: registrar quem editou a pizza
            $userId = getAuthUserId();

            return $this->respond(
                [
                    'message' => 'Pizza editada com sucesso',
                    'data' => $newPizzaInfos,
                    'edited_by_user_id' => $userId
                ]
            );
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}
