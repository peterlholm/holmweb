"view modulse for opskrifter"
#from django.shortcuts import render

# Create your views here.

from django.http import HttpResponse


def index(request):     # pylint: disable=unused-argument
    "opskrifter default side"
    return HttpResponse("Hello, world. Wellcome.")
