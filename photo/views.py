"Views for photo"
#from pathlib import Path
from django.shortcuts import render
from django.conf import settings
from .utils import create_folder_table, create_album_list, get_picture_list

# pages #######################################

def index(request):     # pylint: disable=unused-argument
    "photo default side"
    context = {
    }
    return render(request, 'photo/index.html', context)

def album(request):     # pylint: disable=unused-argument
    "Show list of albums"
    a_list = create_album_list()
    context = { "album_list": a_list}
    return render(request, 'photo/album.html', context)

def show(request):     # pylint: disable=unused-argument
    "show photo sledis from folder"
    #picturelist(request)
    #return HttpResponse("Hello, show. Wellcome.")
    print(request.GET)
    folder = request.GET.get('folder', "2025/2025-01 Ski Vinterferie")
    pinfo = get_picture_list(folder)
    print("pinfo", pinfo)

    # slides = []
    # for p in pinfo:
    #     #print (p)
    #     slides.append({"url": p[1]})
    # print(slides)
    context = {"folder":folder, "slides": pinfo}
    return render(request, 'photo/slides.html', context)

# dev functions #####################

def functions(request):     # pylint: disable=unused-argument
    "photo default side"
    context = {
    }
    return render(request, 'photo/functions.html', context)

def create_albums(request):
    "photo slide serie list"
    clear = request.GET.get('clear', False)
    print("PHOTO_DIR", settings.PHOTO_DIR)
    create_folder_table(settings.PHOTO_DIR, clear_table=clear)
    context = { "folder_list": []}
    return render(request, 'photo/folder_list.html', context)
