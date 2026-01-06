"utils for picture control"
from pathlib import Path
from datetime import datetime
from django.conf import settings
from django.forms.models import model_to_dict
from .models import Album

def get_pic_filename(folder : Path):
    "get list of relative filenames"
    pictures = [p for p in folder.iterdir() if p.suffix in ('.png', '.PNG', '.jpg', '.jpeg', '.JPG')]
    #pictures = folder.glob('*.png') +folder.glob('*.PNG') + folder.glob('*.jpg') + folder.glob('*.JPG')
    #print(pictures)
    return pictures

def get_url_picture(filename: Path):
    "create the url for filename"
    rel = Path(filename).relative_to(settings.PHOTO_DIR)
    #print("rel", rel )
    url = "http://www.holmnet.dk/pictures/" + str(rel)
    #print("url", url)
    return url

def create_folder_list(root_folder: Path, folderlist:list = None):
    "create list of all folders containing photos"
    if folderlist is None:
        folderlist = []
    iterl = root_folder.iterdir()
    for i in iterl:
        if i.is_dir():
            folderlist.append(i)
            create_folder_list(i, folderlist)
    return folderlist

def create_folder_table(root_folder: Path, clear_table= False):
    "create the folder table from file tree, if clear_table then clear before"
    if clear_table:
        #clear the table before
        Album.objects.all().delete()
    abs_folder_list = create_folder_list(root_folder)
    rel_list =[]
    for a in abs_folder_list:
        l = Path(a).relative_to(settings.PHOTO_DIR)
        print(l)
        s = Album(folder = str(l), name=l.name, date=datetime.today(), no_pictures=0)
        s.save()
        rel_list.append(l)
    print("Rellist",rel_list)
    return

def create_album_list():
    "create the list of albums"
    s = Album.objects.all()
    s_list = []
    for o in s:
        #print("o", o)
        s_list.append(model_to_dict(o))
        #s_list.append({"name": o.name, "folder" :o.folder, "date": o.date, "no_pictures": o.no_pictures})
    #print("s_list", s_list)
    return s_list

def get_picture_list(folder: Path):
    "get list of (filename, url)"
    print("folder",folder)
    abs_folder = settings.PHOTO_DIR / folder
    print("abs_folder", abs_folder)
    picfiles = get_pic_filename(abs_folder)
    plist = []
    for f in picfiles:
        #print(f)
        u = get_url_picture(f)
        #print(u)
        plist.append({"file":f, "url":u})
    #print("plist", plist)
    return plist
