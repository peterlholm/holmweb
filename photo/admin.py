"admin module for photo"
from django.contrib import admin

from .models import Album


@admin.register(Album)
class AlbumAdmin(admin.ModelAdmin):
    "Album admin display"
    list_display = ('name','date','no_pictures','folder')
