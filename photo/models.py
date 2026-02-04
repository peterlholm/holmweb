"models for photo"
from django.db import models

class Album(models.Model):
    "slide series"
    folder = models.CharField(max_length=90)
    name = models.CharField(max_length=40)
    date = models.DateField()
    no_pictures = models.IntegerField(default=0)
    no_video = models.IntegerField(default=0)

    def __str__(self):
        return str(self.name)
