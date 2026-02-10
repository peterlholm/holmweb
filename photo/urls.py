"photo urls"
from django.urls import path
from django.conf import settings
from django.conf.urls.static import static
from . import views

app_name = 'photo'

urlpatterns = [
    path('', views.index, name='photo_index'),
    path('album', views.album, name='album'),
    #path('slide', views.slides, name='slide'),
    path('show', views.show, name='show'),
    path('makezip', views.makezip, name='makezip'),
    path('label', views.label, name='label'),
    path('functions', views.functions, name='functions'),
    path('copy_testdata', views.copy_testdata, name='copy_testdata'),
    path('create_albums', views.create_albums, name='create_albums'),
    path('create_test_albums', views.create_test_albums, name='create_test_albums'),
    path('add_albums', views.create_albums, name='add_albums'),
    path('test', views.test,),
    path('base', views.base,),
    path('menu', views.menu,),
]

urlpatterns += static(settings.PHOTO_URL, document_root=settings.PHOTO_DIR, show_indexes=True)
#print (urlpatterns)
