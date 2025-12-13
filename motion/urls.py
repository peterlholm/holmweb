"motion urls"
from django.urls import path
from . import views

app_name = 'motion'

urlpatterns = [
    path('', views.index, name='index'),
    path('blodtryk/', views.blood_pressure, name='blood_pressure'),
    path('save/', views.save_weight, name='save_weight'),
    path('save-blodtryk/', views.save_blood_pressure, name='save_blood_pressure'),
]
