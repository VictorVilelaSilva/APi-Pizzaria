from django.db import models
from django.contrib.auth.models import User

# Create your models here.

class UserProfile(User):
    phone = models.CharField(max_length=15)
    address = models.TextField()

    def __str__(self):
        return f"{self.username}"
    
class Ingrediente(models.Model):
    nome = models.CharField(max_length=255)

class Pizza(models.Model):
    nome = models.CharField(max_length=255)
    id_ingredientes = models.ManyToManyField(Ingrediente)
    preco = models.DecimalField(max_digits=5, decimal_places=2)

class Pedido(models.Model):
    cliente = models.CharField(max_length=255)
    endereco = models.CharField(max_length=255)
    pizzas = models.ManyToManyField(Pizza)
    data_hora_pedido = models.DateTimeField(auto_now_add=True)
    status = models.CharField(max_length=255)