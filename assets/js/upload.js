/**@jsx vNode */
// EXAMPLE LINK: https://appdev.ocdla.org/directory/members/0035b00002aT2irAAC
// PDFJS dist (es module support): https://github.com/bundled-es-modules/pdfjs-dist
// PDFJS documentation: https://mozilla.github.io/pdf.js/api/draft/index.html
// PDFJS Hello World walk through: https://mozilla.github.io/pdf.js/examples/
import pdfjs from "/node_modules/@bundled-es-modules/pdfjs-dist/build/pdf.js";
// import viewer from "/node_modules/@bundled-es-modules/pdfjs-dist/web/pdf_viewer.js";

pdfjs.GlobalWorkerOptions.workerSrc =
  "/node_modules/@bundled-es-modules/pdfjs-dist/build/pdf.worker.js";
window.pdfjs = pdfjs;
window.loadThumbs = loadThumbs;
// window.viewer = view;
/*
var url = "/content/uploads/modules/directory/current_members_alpha.pdf"
var task = pdfjs.getDocument(url);
loadThumbs(task);
*/

function makeThumb(page) {
  // draw page to fit into 96x96 canvas
  var vp = page.getViewport({ scale: 1, });
  var canvas = document.createElement("canvas");
  var scalesize = 1;
  canvas.width = vp.width * scalesize;
  canvas.height = vp.height * scalesize;
  var scale = Math.min(canvas.width / vp.width, canvas.height / vp.height);
  console.log(vp.width, vp.height, scale);
  return page.render({ canvasContext: canvas.getContext("2d"), viewport: page.getViewport({ scale: scale }) }).promise.then(function () {
      return canvas; 
  });
}


function loadThumbs(p) {
  p.promise.then(function (doc) {
    var pages = []; while (pages.length < doc.numPages) pages.push(pages.length + 1);
    return Promise.all(pages.map(function (num) {
      // create a div for each page and build a small canvas for it
      var div = document.createElement("div");
      document.body.appendChild(div);
      return doc.getPage(num).then(makeThumb)
        .then(function (canvas) {
          div.appendChild(canvas);
      });
    }));
  }).catch(console.error);
}



function base(num, radix) {
  let foo = 0b111; // Decimal 4.
  return num.toString(radix);
}

domReady(function() {
  const inputElement = document.getElementById("upload");
  inputElement.addEventListener("change", handleFiles, false);
});



function handleFiles() {
  const fileList = this.files; /* now you can work with the file list */
  const numFiles = fileList.length;
  console.log(fileList);
  for (let i = 0, count = fileList.length; i < count; i++) {
    let f = fileList[i];
    console.log([f.name, f.size, f.type].join(" | "));
    getPreview(f);
  } 
}


function getPreview(file) {
  const img = document.createElement("img");
  img.classList.add("obj");
  img.style = "width:200px; height:auto";
  img.file = file;
  preview.appendChild(img); // Assuming that "preview" is the div output where the content will be displayed.

  const reader = new FileReader();
  reader.onload = (function(aImg) { return function(e) { aImg.src = e.target.result; }; })(img);
  reader.readAsDataURL(file);
}