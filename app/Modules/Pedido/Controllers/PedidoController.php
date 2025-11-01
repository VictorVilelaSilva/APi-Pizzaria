<?php

namespace Modules\Pedido\Controllers;

use App\Services\Pedido\PedidoService;
use App\Validation\Pedido\PedidoValidation;
use App\DTO\Pedido\CriarPedidoInputDTO;
use App\DTO\Pedido\AtualizarStatusInputDTO;
use CodeIgniter\RESTful\ResourceController;
use Config\ErrorTrait;

class PedidoController extends ResourceController
{
    private PedidoService $pedidoService;

    public function __construct()
    {
        $this->pedidoService = new PedidoService();
    }

    /**
     * Criar novo pedido
     * POST /pedido/criar
     */
    public function criar()
    {
        try {
            helper('jwt');
            $usuarioAutenticado = getAuthenticatedUser();

            if (!$usuarioAutenticado || !isset($usuarioAutenticado->id)) {
                throw new ErrorTrait('EPEDIDO011', errors: ['auth' => 'Usuário não autenticado']);
            }

            $bodyRequest = $this->request->getJSON(true);

            // Validar dados de entrada
            PedidoValidation::execute('CRIAR', $bodyRequest, 'EPEDIDO012');

            // Adicionar ID do usuário autenticado
            $bodyRequest['id_usuario'] = $usuarioAutenticado->id;

            // Processar DTO
            $pedidoInput = CriarPedidoInputDTO::get($bodyRequest);

            // Criar pedido
            $pedido = $this->pedidoService->criarPedido($pedidoInput);

            return $this->respond([
                'success' => true,
                'message' => 'Pedido criado com sucesso',
                'data' => $pedido,
            ], 201);
        } catch (ErrorTrait $e) {
            // ErrorTrait já faz o response automaticamente
            return;
        } catch (\Exception $e) {
            return $this->fail([
                'success' => false,
                'message' => 'Erro ao criar pedido',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Listar meus pedidos com filtros e paginação
     * GET /pedido/meus-pedidos
     * Query params: ?status=Pendente&page=1&per_page=10&data_inicio=2024-01-01&data_fim=2024-12-31
     */
    public function meusPedidos()
    {
        try {
            helper('jwt');
            $usuarioAutenticado = getAuthenticatedUser();

            if (!$usuarioAutenticado || !isset($usuarioAutenticado->id)) {
                throw new ErrorTrait('EPEDIDO011', errors: ['auth' => 'Usuário não autenticado']);
            }

            // Obter filtros da query string
            $filters = [
                'status' => $this->request->getGet('status'),
                'data_inicio' => $this->request->getGet('data_inicio'),
                'data_fim' => $this->request->getGet('data_fim'),
                'page' => (int) ($this->request->getGet('page') ?? 1),
                'per_page' => (int) ($this->request->getGet('per_page') ?? 10),
                'order_by' => $this->request->getGet('order_by') ?? 'pedidos.created_at',
                'order_dir' => $this->request->getGet('order_dir') ?? 'DESC',
            ];

            $resultado = $this->pedidoService->listarPedidosUsuario($usuarioAutenticado->id, $filters);

            return $this->respond([
                'success' => true,
                'message' => 'Pedidos listados com sucesso',
                'data' => $resultado['pedidos'],
                'pagination' => $resultado['pagination'],
            ]);
        } catch (ErrorTrait $e) {
            // ErrorTrait já faz o response automaticamente
            return;
        } catch (\Exception $e) {
            return $this->fail([
                'success' => false,
                'message' => 'Erro ao listar pedidos',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ver detalhes de um pedido específico
     * GET /pedido/:id/detalhes
     */
    public function detalhes($id = null)
    {
        try {
            helper('jwt');
            $usuarioAutenticado = getAuthenticatedUser();

            if (!$usuarioAutenticado || !isset($usuarioAutenticado->id)) {
                throw new ErrorTrait('EPEDIDO011', errors: ['auth' => 'Usuário não autenticado']);
            }

            if (!$id) {
                throw new ErrorTrait('EPEDIDO013', errors: ['id' => 'ID do pedido não informado']);
            }

            $pedido = $this->pedidoService->buscarPedidoDetalhado((int) $id, $usuarioAutenticado->id);

            return $this->respond([
                'success' => true,
                'message' => 'Detalhes do pedido',
                'data' => $pedido,
            ]);
        } catch (ErrorTrait $e) {
            // ErrorTrait já faz o response automaticamente
            return;
        } catch (\Exception $e) {
            return $this->fail([
                'success' => false,
                'message' => 'Erro ao buscar detalhes do pedido',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancelar pedido
     * PUT /pedido/:id/cancelar
     */
    public function cancelar($id = null)
    {
        try {
            helper('jwt');
            $usuarioAutenticado = getAuthenticatedUser();

            if (!$usuarioAutenticado || !isset($usuarioAutenticado->id)) {
                throw new ErrorTrait('EPEDIDO011', errors: ['auth' => 'Usuário não autenticado']);
            }

            if (!$id) {
                throw new ErrorTrait('EPEDIDO013', errors: ['id' => 'ID do pedido não informado']);
            }

            $bodyRequest = $this->request->getJSON(true);
            PedidoValidation::execute('CANCELAR', $bodyRequest, 'EPEDIDO014');

            $pedido = $this->pedidoService->cancelarPedido(
                (int) $id,
                $usuarioAutenticado->id,
                $bodyRequest['motivo_cancelamento']
            );

            return $this->respond([
                'success' => true,
                'message' => 'Pedido cancelado com sucesso',
                'data' => $pedido,
            ]);
        } catch (ErrorTrait $e) {
            // ErrorTrait já faz o response automaticamente
            return;
        } catch (\Exception $e) {
            return $this->fail([
                'success' => false,
                'message' => 'Erro ao cancelar pedido',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Atualizar status do pedido (Admin/Sistema)
     * PATCH /pedido/:id/status
     */
    public function atualizarStatus($id = null)
    {
        try {
            helper('jwt');
            $usuarioAutenticado = getAuthenticatedUser();

            if (!$usuarioAutenticado || !isset($usuarioAutenticado->id)) {
                throw new ErrorTrait('EPEDIDO011', errors: ['auth' => 'Usuário não autenticado']);
            }

            if (!$id) {
                throw new ErrorTrait('EPEDIDO013', errors: ['id' => 'ID do pedido não informado']);
            }

            $bodyRequest = $this->request->getJSON(true);
            PedidoValidation::execute('ATUALIZAR_STATUS', $bodyRequest, 'EPEDIDO015');

            $statusInput = AtualizarStatusInputDTO::get($bodyRequest);

            $pedido = $this->pedidoService->atualizarStatus(
                (int) $id,
                $statusInput['status'],
                $usuarioAutenticado->id,
                $statusInput['observacao']
            );

            return $this->respond([
                'success' => true,
                'message' => 'Status atualizado com sucesso',
                'data' => $pedido,
            ]);
        } catch (ErrorTrait $e) {
            // ErrorTrait já faz o response automaticamente
            return;
        } catch (\Exception $e) {
            return $this->fail([
                'success' => false,
                'message' => 'Erro ao atualizar status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Ver estatísticas dos meus pedidos
     * GET /pedido/estatisticas
     */
    public function estatisticas()
    {
        try {
            helper('jwt');
            $usuarioAutenticado = getAuthenticatedUser();

            if (!$usuarioAutenticado || !isset($usuarioAutenticado->id)) {
                throw new ErrorTrait('EPEDIDO011', errors: ['auth' => 'Usuário não autenticado']);
            }

            $estatisticas = $this->pedidoService->buscarEstatisticas($usuarioAutenticado->id);

            return $this->respond([
                'success' => true,
                'message' => 'Estatísticas dos pedidos',
                'data' => $estatisticas,
            ]);
        } catch (ErrorTrait $e) {
            // ErrorTrait já faz o response automaticamente
            return;
        } catch (\Exception $e) {
            return $this->fail([
                'success' => false,
                'message' => 'Erro ao buscar estatísticas',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
