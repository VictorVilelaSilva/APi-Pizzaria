from django.contrib.auth.models import User, Group
from rest_framework.response import Response
from rest_framework import serializers
from .models import Pedido, UserProfile, Ingrediente, Pizza

class UserSerializer(serializers.ModelSerializer):
    class Meta:
        model = UserProfile
        fields = ['id', 'username', 'password','email','address','phone']
        extra_kwargs = {'password': {'write_only': True}}


class IngredienteSerializer(serializers.ModelSerializer):
    class Meta:
        model = Ingrediente
        fields = ['id', 'nome']

   

class PizzaSerializer(serializers.ModelSerializer):
    class Meta:
        model = Pizza
        fields = ['id', 'nome', 'id_ingredientes', 'preco']




class PedidoSerializer(serializers.ModelSerializer):
    pizzas = PizzaSerializer(many=True)
    class Meta:
        model = Pedido
        fields = ['id', 'cliente', 'endereco', 'pizzas', 'data_hora_pedido', 'status']


