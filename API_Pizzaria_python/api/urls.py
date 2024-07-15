from django.urls import path
from . import views

urlpatterns = [
    # path('pedido/', views.PedidoViewSet, name="pedido-list"),
    path('ingrediente/', views.IngredienteViewSet.as_view(), name="ingrediente-list"),
    path('DeleteIngrediente/<int:pk>', views.DeleteIngredienteViewSet.as_view(), name="ingrediente-delete"),
    path('pizza/', views.PizzaViewSet.as_view(), name="pizza-list"),
    
    
]
