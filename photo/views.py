"Views for photo"
from pathlib import Path
from django.shortcuts import render
from django.conf import settings
from .utils import create_folder_table, create_album_list, get_picture_list, get_url_picture

# pages #######################################

init_context = {"devel": settings.DEVEL}

def index(request):     # pylint: disable=unused-argument
    "photo default side"
    context = init_context
    return render(request, 'photo/index.html', context)

def album(request):     # pylint: disable=unused-argument
    "Show list of albums"
    a_list = create_album_list()
    context = { **init_context, "devel": settings.DEVEL, "album_list": a_list}
    return render(request, 'photo/album.html', context)

def show(request):     # pylint: disable=unused-argument
    "show photo sledis from folder"
    #picturelist(request)
    #return HttpResponse("Hello, show. Wellcome.")
    #print(request.GET)
    folder = request.GET.get('folder', "2025/2025-01 Ski Vinterferie")
    plist = get_picture_list(folder)
   

    # slides = []
    # for p in pinfo:
    #     #print (p)
    #     slides.append({"url": p[1]})
    # print(slides)
    context = {**init_context, "folder":folder, "slides": plist}
    #print(context)
    return render(request, 'photo/slides.html', context)

def base(request):
    "base test page"
    context = {     }
    return render(request, 'photo/base.html', context)

def menu(request):
    "menu test page"
    context = init_context
    return render(request, 'photo/base_menu.html', context)

def test(request):
    "test page"
    #context = {**init_context, "pic1": "2025/2025-09%20Vinterbad/PXL_20250930_151241088.jpg","pic2": "2025/2025-09%20Vinterbad/PXL_20250930_151251602.MP.jpg"    }
    if request.GET.get("pic"):
        pic = "2025/2025-09%20Vinterbad/PXL_20250930_151241088.jpg" # 3472 x 4624  HxB Landscape
    else:
        pic = "2025/2025-09%20Vinterbad/PXL_20250930_151251602.MP.jpg"  # 4624 x 3472 Portrait
    context = {**init_context, "pic1": pic}
    return render(request, 'photo/test.html', context)

def test1(request):
    "test page"
    #context = {**init_context, "pic1": "2025/2025-09%20Vinterbad/PXL_20250930_151241088.jpg","pic2": "2025/2025-09%20Vinterbad/PXL_20250930_151251602.MP.jpg"    }
    pic1 = "2025/2025-09%20Vinterbad/PXL_20250930_151241088.jpg"
    pic2 = "2025/2025-09%20Vinterbad/PXL_20250930_151251602.MP.jpg"
   
    context = {**init_context, "pic1": pic1, "pic2": pic2}
    return render(request, 'photo/test1.html', context)

def test2(request):
    "test page"
    #context = {**init_context, "pic1": "2025/2025-09%20Vinterbad/PXL_20250930_151241088.jpg","pic2": "2025/2025-09%20Vinterbad/PXL_20250930_151251602.MP.jpg"    }
    pic1 = Path("2025/2025-09 Vinterbad/PXL_20250930_151241088.jpg")
    pic2 = "2025/2025-09%20Vinterbad/PXL_20250930_151251602.MP.jpg"
    f1 = "mytest/both"
    plist = get_picture_list(f1)
    #u = get_url_picture(pic1)
    #plist.append({"file":pic1, "name":pic1.name, "url":u})
    context = {**init_context, "slides": plist, "pic1": pic1, "pic2": pic2}
    return render(request, 'photo/test2.html', context)

# dev functions #####################

def functions(request):     # pylint: disable=unused-argument
    "photo default side"
    context = init_context
    return render(request, 'photo/functions.html', context)

def create_albums(request):
    "photo slide serie list"
    clear = request.GET.get('clear', False)
    #print("PHOTO_DIR", settings.PHOTO_DIR)
    create_folder_table(settings.PHOTO_DIR, clear_table=clear)
    context = { **init_context, "folder_list": []}
    return render(request, 'photo/folder_list.html', context)
