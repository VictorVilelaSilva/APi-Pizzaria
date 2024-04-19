from django.shortcuts import render
from django.contrib.auth.models import User
from rest_framework.response import Response
from rest_framework import generics, status
from rest_framework.views import APIView
from .serializers import *
from rest_framework.permissions import IsAuthenticated, AllowAny
from .models import *

# views.py
from rest_framework.decorators import api_view
from rest_framework.request import Request
# Create your views here.


class CreateUserView(generics.CreateAPIView):
    queryset = UserProfile.objects.all()
    serializer_class = UserSerializer
    permission_classes = [AllowAny]

    def post(self, request, *args, **kwargs):
        serializer = self.get_serializer(data=request.data)
        serializer.is_valid(raise_exception=True)
        self.perform_create(serializer)
        user_data = serializer.data
        response_data = {
            'message': 'Usuário criado com sucesso',
            'user': user_data
        }
        return Response(response_data, status=status.HTTP_201_CREATED)
    
class IngredienteViewSet(generics.CreateAPIView):
    queryset = Ingrediente.objects.all()
    serializer_class = IngredienteSerializer
    permission_classes = [AllowAny]


    def post(self, request, *args, **kwargs):
        serializer = self.get_serializer(data=request.data)
        serializer.is_valid(raise_exception=True)
        self.perform_create(serializer)
        ingrediente = serializer.data
        response_data = {
            'message': 'Ingrediente criado com sucesso',
            'user': ingrediente
        }
        return Response(response_data, status=status.HTTP_201_CREATED)

    def get(self, request, *args, **kwargs):
        ingredientes = Ingrediente.objects.all()
        serializer = self.serializer_class(ingredientes, many=True)
        return Response(serializer.data, status=status.HTTP_200_OK)
    
class DeleteIngredienteViewSet(generics.DestroyAPIView):
    queryset = Ingrediente.objects.all()
    serializer_class = IngredienteSerializer
    permission_classes = [AllowAny]

    def delete(self, request, *args, **kwargs):
        ingrediente_id = kwargs.get('pk')
        try:
            ingrediente = Ingrediente.objects.get(pk=ingrediente_id)
            ingrediente.delete()
            return Response({'message': 'Ingrediente deletado com sucesso'}, status=status.HTTP_200_OK)
        except Ingrediente.DoesNotExist:
            return Response({'message': 'Ingrediente não encontrado'}, status=status.HTTP_404_NOT_FOUND)

    
class PizzaViewSet(generics.CreateAPIView):
    queryset = Pizza.objects.all()
    serializer_class = PizzaSerializer
    permission_classes = [AllowAny]

    def post(self, request, *args, **kwargs):
        serializer = self.get_serializer(data=request.data)
        serializer.is_valid(raise_exception=True)
        self.perform_create(serializer)
        pizza = serializer.data
        response_data = {
            'message': 'Pizza criada com sucesso',
            'pizza': {
                'nome': pizza['nome'],
                'preco': pizza['preco'],
                'ingredientes': pizza['id_ingredientes']
            
            }
        }
        return Response(response_data, status=status.HTTP_201_CREATED)
    
    
    def get(self, request, *args, **kwargs):
        pizzas = Pizza.objects.all()
        serializer = self.serializer_class(pizzas, many=True)
        pizzas_data = serializer.data

        Response_data = []

        for pizza_data in pizzas_data:
            ingredientes = Ingrediente.objects.filter(id__in=pizza_data['id_ingredientes'])
            ingredientes_data = [ingrediente.nome for ingrediente in ingredientes]
            Response_data.append({
                'nome': pizza_data['nome'],
                'preco': pizza_data['preco'],
                'ingredientes': ingredientes_data
            })

        return Response(Response_data, status=status.HTTP_200_OK)

        
    

class PedidoViewSet(generics.CreateAPIView):
    queryset = Pedido.objects.all()
    serializer_class = PedidoSerializer

    def post(self, request, *args, **kwargs):
        
        serializer = self.get_serializer(data=request.data)
        serializer.is_valid(raise_exception=True)
        self.perform_create(serializer)
        pedido = serializer.data
        response_data = {
            'message': 'Pedido criado com sucesso',
            'user': pedido
        }
        return Response(response_data, status=status.HTTP_201_CREATED)
    

