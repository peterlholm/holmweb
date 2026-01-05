"Views for photo"
#from pathlib import Path
from django.shortcuts import render
#from django.http import HttpResponse
from .config import PHOTO_DIR
from .utils import create_folder_table, create_album_list, get_picture_list


#albumlist = []

# def album_listx(root_folder: Path, folderlist:list):
#     "create list of all folders containing photos"
#     #print ("Root", root_folder)
#     #folderlist.append(root_folder)
#     iterl = root_folder.iterdir()
#     for i in iterl:
#         if i.is_dir():
#             folderlist.append(i)
#             print(i)
#             create_album_list(i, folderlist)
#     return folderlist

# def create_picture_list(folder: Path):
#     "create list of photos"
#     files = [p for p in folder.iterdir() if p.suffix in ('.png', '.PNG', '.jpg', '.jpeg', '.JPG')]
#     return files

# pages #######################################

def index(request):     # pylint: disable=unused-argument
    "photo default side"
    context = {
    }
    return render(request, 'photo/index.html', context)

def album(request):     # pylint: disable=unused-argument
    "Show list of albums"
    a_list = create_album_list()
    # print("------------------")
    # print(a_list)
    # print("------------------")
    #a_list.sort()
    #print(a_list)
    # for i in a_list:
    #     print(i)
    context = { "album_list": a_list}
    #print(context)
    return render(request, 'photo/album.html', context)

def show(request):     # pylint: disable=unused-argument
    "photo default side"
    #picturelist(request)
    #return HttpResponse("Hello, show. Wellcome.")
    print(request.GET)
    folder = request.GET.get('folder', "2025/2025-01 Ski Vinterferie")
    pinfo = get_picture_list(folder)
    print(pinfo)
    context = {"folder":"jdjd", "pinfo": pinfo}
    return render(request, 'photo/slides.html', context)

# dev functions #####################
def functions(request):     # pylint: disable=unused-argument
    "photo default side"
    context = {
    }
    return render(request, 'photo/functions.html', context)


def folder_list(request):
    "photo slide serie list"
    print("PHOTO_DIR", PHOTO_DIR)
    create_folder_table(PHOTO_DIR)

    #create_folder_table(PHOTO_DIR)
    # #print(albums)
    # rel_list =[]
    # for a in albums:
    #     l = Path(a).relative_to(PHOTO_DIR)
    #     print(l)
    #     rel_list.append(l)
    # print("Rellist")
    # print (rel_list)
    context = { "folder_list": []}
    return render(request, 'photo/folder_list.html', context)



# def picturelist(request):     # pylint: disable=unused-argument
#     "photo default side"
#     album_list = []
#     create_album_list(PHOTO_DIR, album_list)

#     files = create_picture_list(album_list[4])
#     print(files)
#     return HttpResponse("Hello, picturelist. Wellcome.")
