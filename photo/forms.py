"forms"
from django import forms

class LabelForm(forms.Form):
    "label with album information"
    path = forms.FilePathField("/data/Photos", label="Album Path", recursive=True, allow_folders=True, required=False, allow_files=False)
    album_title = forms.CharField(label="Titel", max_length=100, required=False)
    date = forms.DateField(label="Dato", required=False)
