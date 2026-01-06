"photo urls"
from django.urls import path
from . import views

app_name = 'photo'

urlpatterns = [
    path('', views.index, name='photo_index'),
    path('album', views.album, name='album'),
    path('show', views.show, name='show'),
    path('functions', views.functions, name='functions'),
    path('create_albums', views.create_albums, name='create_albums'),
]
