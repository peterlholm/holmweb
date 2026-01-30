"photo urls"
from django.urls import path
from django.conf import settings
from django.conf.urls.static import static
from . import views

app_name = 'photo'

urlpatterns = [
    path('', views.index, name='photo_index'),
    path('album', views.album, name='album'),
    path('show', views.show, name='show'),
    path('functions', views.functions, name='functions'),
    path('create_albums', views.create_albums, name='create_albums'),
    path('test', views.test,),
    path('test1', views.test1,),
    path('test2', views.test2,),
    path('base', views.base,),
    path('menu', views.menu,),
]

#urlpatterns += static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
#print (urlpatterns)
