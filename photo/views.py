"Views for photo"
from pathlib import Path
from shutil import copytree
from django.http import HttpResponseForbidden
from django.shortcuts import render, redirect
from django.conf import settings
from .utils import create_folder_table, create_album_list, get_media_list, save_dict_to_file, read_dict_from_file, default_dict, save_zip_archive#, get_url_picture
from .forms import LabelForm
#from .file_utils import save_dict_to_file, read_dict_from_file, default_dict

SLIDE_INTERVAL = 3000

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
    folder = request.GET.get('folder', "2025/2025-01 Ski Vinterferie")
    plist = get_media_list(folder)
    if plist is None:
        return HttpResponseForbidden("Forbiden")
    context = {**init_context, "folder":folder, "slides": plist, "interval": SLIDE_INTERVAL}
    return render(request, 'photo/show.html', context)

def makezip(request):
    "make zipfile from folder and return"
    folder = request.GET.get('folder')
    if not folder:
        print("not", folder)
        return HttpResponseForbidden("Forbidden")
    picfolder = Path(settings.PHOTO_DIR) / folder
    save_zip_archive(picfolder, Path("/tmp/zipfile"))
    return HttpResponseForbidden("OK")
    
def label(request):
    "Label form generator"
    if request.method == "GET":
        album_path = request.GET.get('path', "")
        photo_path = Path(settings.PHOTO_DIR) / album_path
        form_init = None
        if photo_path.exists():
            file_path = photo_path / "label.json"
            album_dict = read_dict_from_file(file_path)
            if album_dict:
                #print("album_dict", album_dict)
                form_init = album_dict
            else:
                def_dict = default_dict(photo_path )
                print(def_dict)
                form_init = def_dict
            form_init = {**form_init, "path": album_path}
            #print(form_init)
            form = LabelForm(form_init)
        else:
            form = LabelForm()
    if request.method == "POST":
        # create a form instance and populate it with data from the request:
        form = LabelForm(request.POST)
        if form.is_valid():
            #print(form.cleaned_data)
            path = form.cleaned_data["path"]
            album_path = Path(path) / "label.json"
            label_dict = {"title": form.cleaned_data["title"], "date": str(form.cleaned_data["date"]), "place": form.cleaned_data["place"] }
            save_dict_to_file(label_dict, album_path)

    context = {  **init_context, "form": form }
    return render(request, "photo/label.html", context)

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
    # pic1 = Path("2025/2025-09 Vinterbad/PXL_20250930_151241088.jpg")
    # pic2 = "2025/2025-09%20Vinterbad/PXL_20250930_151251602.MP.jpg"
    pic1 = Path("test/PXL_20250225_165735955.jpg")
    pic2 = "pictures/test/PXL_20250225_165750218.TS.mp4"
    #plist = get_picture_list(f1)
    #u = get_url_picture(pic1)
    #plist.append({"file":pic1, "name":pic1.name, "url":u})
    context = {**init_context, "pic1": pic1, "pic2": pic2}
    return render(request, 'photo/test.html', context)

# dev functions #####################

def functions(request):     # pylint: disable=unused-argument
    "photo default side"
    context = init_context
    return render(request, 'photo/functions.html', context)

def copy_testdata(request):
    "Copy testdata to photo folder"
    src = Path(__file__).parent / "testdata"
    dst = settings.PHOTO_DIR / "testdata"
    copytree(src, dst, dirs_exist_ok=True)
    redirect ('photo:functions')

def create_albums(request):
    "photo slide serie list"
    clear = request.GET.get('clear', False)
    #print("PHOTO_DIR", settings.PHOTO_DIR)
    create_folder_table(settings.PHOTO_DIR, clear_table=clear)
    context = { **init_context, "folder_list": []}
    return render(request, 'photo/folder_list.html', context)

def create_test_albums(request):
    "photo slide serie list"
    clear = request.GET.get('clear', False)
    #print("PHOTO_DIR", settings.PHOTO_DIR)
    folder = Path(__file__).parent.parent / "data"
    print(folder)
    create_folder_table(folder, clear_table=clear)
    context = { **init_context, "folder_list": []}
    return render(request, 'photo/folder_list.html', context)

def add_albums(request):
    "add photo slide serie list"
    clear = request.GET.get('clear', False)
    #print("PHOTO_DIR", settings.PHOTO_DIR)
    photo_dir = Path("/data/Photo")
    rel_list = create_folder_table(photo_dir, clear_table=clear)
    print("rellist", rel_list)
    context = { **init_context, "folder_list": rel_list}
    return render(request, 'photo/folder_list.html', context)
