// common javascript

function toggleFullScreen(elem) {
  if (!document.fullscreenElement) {
    // If the document is not in full screen mode
    // make the video full screen
    if (!elem)
        document.body.requestFullscreen();
  } else {
    // Otherwise exit the full screen
    document.exitFullscreen?.();
  }
}

// slide

const getCarouselInstance = () => bootstrap.Carousel.getOrCreateInstance(document.getElementById('myCarousel'));

const saveImage = (downloadUrl) => {
    const downloadImage = document.createElement("a");
    document.body.appendChild(downloadImage);
    downloadImage.setAttribute("download", "image");
    downloadImage.href = downloadUrl;
    downloadImage.click();
    downloadImage.remove();
};

function setPortrait() {
  const style = document.createElement('style');
  style.id ="portrait";
  style.innerHTML = '@page { size: portrait; }';
  console.log(style);
  document.head.appendChild(style);
}

function removePortrait() {
  //const style = document.head.getElementsByTagName("style");
  const style = document.getElementById("portrait");
  console.log("style", style);
  if (style)
    style.remove();
  console.log(style);
}

function slidePause() {
  const carouselInstance = getCarouselInstance();
  carouselInstance.pause();
}

function slideStart() {
  const carouselInstance = getCarouselInstance();
  carouselInstance.cycle();
}

function slideSave() {
  const carouselInstance = getCarouselInstance();
  const img = document.getElementsByClassName("carousel-item active")[0].getElementsByTagName("img")[0];
  console.log (img);
  const imageElement = img.getAttribute("src");
  console.log(imageElement);
  saveImage(imageElement);
}

function printPage() {
  console.log("Printing page");
  const carouselInstance = getCarouselInstance();
  carouselInstance.pause();
  /* get active item */
  const img = document.getElementsByClassName("carousel-item active")[0].getElementsByTagName("img")[0];
  console.log (img);
  console.log (img.clientHeight, img.clientWidth);
  console.log (img.naturalHeight, img.naturalWidth);
  if (img.naturalHeight > img.naturalWidth) {
    console.log("Portrait");
    setPortrait();
  } else {
    console.log("landscape");
    removePortrait();
  }
  print();
}
