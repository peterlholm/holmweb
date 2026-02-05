"utils for picture control"
from pathlib import Path
from datetime import datetime
import json
from urllib import parse
from django.conf import settings
from django.forms.models import model_to_dict
from PIL import Image
from PIL.ExifTags import TAGS, GPSTAGS, IFD
from .models import Album
#from .location import reverse_location

GPS_ADDRESS = False

PIC_URL = settings.MEDIA_URL + "Photos/"
#PIC_URL = "photo/pictures/Photos/"

DEBUG = False

def gms2deg(pos):
    "convert to degres"
    g, m, s = pos[0], pos[1], pos[2]
    decimal_degrees = g + m / 60 + s / 3600
    return float(decimal_degrees)

def get_pic_filename(folder : Path):
    "get list of relative picture filenames"
    pictures = [p for p in folder.iterdir() if p.suffix in ('.png', '.PNG', '.jpg', '.jpeg', '.JPG')]
    pictures.sort()
    return pictures

def get_media_filename(folder : Path):
    "get list of relative picture and video filenames"
    medias = [p for p in folder.iterdir() if p.suffix in ('.png', '.PNG', '.jpg', '.jpeg', '.JPG', '.mp4')]
    medias.sort()
    return medias

def count_pictures(folder : Path):
    "count number of pictures in folder"
    pictures = [p for p in folder.iterdir() if p.suffix in ('.png', '.PNG', '.jpg', '.jpeg', '.JPG')]
    return len(pictures)

def count_video_files(folder : Path):
    "count number of pictures in folder"
    videos = [p for p in folder.iterdir() if p.suffix in ('.mp4',)]
    return len(videos)

def get_url_picture(filename: Path):
    "create the url for filename"
    rel = Path(filename).relative_to(settings.PHOTO_DIR)
    #print("rel", rel )
    url = PIC_URL + parse.quote(str(rel))
    #print("url", url)
    return url

def create_folder_list(root_folder: Path, folderlist:list = None):
    "create list of all sub folders containing photos"
    if folderlist is None:
        folderlist = []
    root_folder = Path(root_folder)
    iterl = root_folder.iterdir()
    for i in iterl:
        if i.is_dir():
            folderlist.append(i)
            create_folder_list(i, folderlist)
    if DEBUG:
        print(folderlist)
    return folderlist

def create_folder_table(root_folder: Path, rel_folder=settings.PHOTO_DIR, clear_table= False):
    "create the folder table from file tree, if clear_table then clear before"
    if clear_table:
        #clear the table before
        Album.objects.all().delete()
    abs_folder_list = create_folder_list(root_folder)
    rel_list = []
    for a in abs_folder_list:
        #l = Path(a)
        l = Path(a).relative_to(rel_folder)
        print(l)
        n = count_pictures(a)
        v = count_video_files(a)
        if n>0:
            s = Album(folder = str(l), name=l.name, date=datetime.today(), no_pictures=n, no_video=v)
            s.save()
            rel_list.append(l)
        #print("Rellist",rel_list)
    return rel_list

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
    #print("folder",folder)
    abs_folder = settings.PHOTO_DIR / folder
    #print("abs_folder", abs_folder)
    picfiles = get_pic_filename(abs_folder)
    plist = []
    pinfo = []
    for f in picfiles:
        #print(f)
        u = get_url_picture(f)
        info = get_picture_info(f)
        plist.append({"file":f, "name":f.name, "url":u, "atributes":info})
        pinfo.append(info)
    #print("plist", plist)
    return plist

def get_media_list(folder: Path):
    "get list of (filename, url)"
    #print("folder",folder)
    abs_folder = Path(settings.PHOTO_DIR) / folder
    #print("abs_folder", abs_folder)
    picfiles = get_media_filename(abs_folder)
    plist = []
    #pinfo = []
    for f in picfiles:
        print(f)
        u = get_url_picture(f)
        if f.suffix==".mp4":
            # this is a video
            ftype = "video"
            info = ""
        else:
            ftype = "picture"
            info = get_picture_info(f)
        plist.append({"file":f, "name":f.name, "type": ftype, "url":u, "atributes":info})
        #plist.append({"file":f, "name":f.name, "url":u })
        #pinfo.append(info)
    #print("plist", plist)
    return plist

def get_picture_info(file: Path):
    "return a dict with picture info"
    make = model = orientation = date = address = ""
    img = Image.open(file)
    exif = img.getexif()
    gps_tags = exif.get_ifd(IFD.GPSInfo)
    if 306 in exif:
        date = exif[306]
    if 274 in exif:
        orientation = exif[274]
    if 271 in exif:
        make = exif[271]
    if 272 in exif:
        model = exif[272]

    #print("GPStags", GPStags)
    if DEBUG:
        print("Exif")
        for k,v  in exif.items():
            print(f" {TAGS.get(k,k):25},{k}, {v}")
        print("GPS")
        for k,v  in gps_tags.items():
            print(f" {GPSTAGS.get(k,k):25},{k}, {v}")
    gps_info = {}
    # if len(GPStags)>0:
    #     latitude = GPStags[2]
    #     longitude = GPStags[4]
    #     gps_time = GPStags[7]
    #     gps_direction = ""
    #     #gps_direction = GPStags[17]
    #     gps_info = {"latitude": gms2deg(latitude), "longitude": gms2deg(longitude), "time": f"{int(gps_time[0]):02d}:{int(gps_time[1]):02d}:{int(gps_time[2]):02d}", "direction":gps_direction }
    #     if GPS_ADDRESS:
    #         address = reverse_location(gms2deg(latitude), gms2deg(longitude))
    #     else:
    #         address = ""
    pict_info = {"DateTime": date, "Orientation": orientation, "Make": make, "Model": model, "gps":gps_info, "address": address}
    return pict_info


def save_dict_to_file(album_info: dict, filename: Path):
    "save dict to file"
    fp = open(filename, "w", encoding='utf-8')
    json.dump(album_info, fp, indent=2, ensure_ascii=False)

def read_dict_from_file(filename: Path):
    "read dict in file"
    if not filename.exists(): 
        return None
    fp = open(filename, 'r', encoding="utf-8")
    album_info = json.load(fp)
    return album_info

def default_dict(album_path: Path):
    "find default dict info"
    if not album_path.is_dir():
        raise Exception("not a folder")
    label = {'title':"", "date": ""}
    pic_list = get_picture_list(album_path)
    if len(pic_list)>0:
        pic_info = get_picture_info(pic_list[0]['file'])
        print(pic_info)
        label = {'title': album_path.name, "date": pic_info['DateTime']}
    print("label", label)
    return label

if __name__ == '__main__':
    print("starter")
    a = {"dffd": "iæøå", "b": 26}
    save_dict_to_file(a, "dummy.txt")
    a=read_dict_from_file("dummy.txt")
    print("dict", a)
