// Client-side JS helpers for HimiloGuul
(function () {
  "use strict";
  // Simple DOM helper
  window.$ = (selector) => document.querySelector(selector);

  // Image preview helpers
  function makeImgNode(src, size = 96, cls = "") {
    const img = document.createElement("img");
    img.src = src;
    img.width = size;
    img.height = size;
    img.className = cls;
    img.style.objectFit = "cover";
    img.style.borderRadius = "6px";
    img.style.border = "1px solid rgba(230,230,230,1)";
    img.style.display = "inline-block";
    img.style.margin = "4px";
    return img;
  }

  // Preview single-image file input into container
  window.previewSingleImage = function (
    fileInputSelector,
    previewContainerSelector
  ) {
    const input = document.querySelector(fileInputSelector);
    const preview = document.querySelector(previewContainerSelector);
    if (!input || !preview) return;

    const render = (file) => {
      preview.innerHTML = "";
      if (!file) return;
      // allow fallback when file.type is empty by trying to load anyway
      if (!(file.type && file.type.startsWith("image/")) && file.size === 0)
        return;
      const reader = new FileReader();
      reader.onload = function (ev) {
        const img = makeImgNode(ev.target.result, 96);
        preview.appendChild(img);
      };
      reader.readAsDataURL(file);
    };

    input.addEventListener("change", (e) => {
      const f = input.files && input.files[0];
      render(f);
    });

    // render initially if an image is already selected (e.g., after navigation or browser restored state)
    if (input.files && input.files.length > 0) {
      render(input.files[0]);
    }
  };

  // Preview multiple files
  window.previewMultipleImages = function (
    fileInputSelector,
    previewContainerSelector
  ) {
    const input = document.querySelector(fileInputSelector);
    const preview = document.querySelector(previewContainerSelector);
    if (!input || !preview) return;

    const renderFiles = (files) => {
      preview.innerHTML = "";
      const filesArr = Array.from(files || []);
      if (filesArr.length === 0) return;
      filesArr.forEach((f) => {
        // show even if file.type is empty, as long as there's a size
        if (!(f.type && f.type.startsWith("image/")) && f.size === 0) return;
        const reader = new FileReader();
        reader.onload = function (ev) {
          const img = makeImgNode(ev.target.result, 120);
          preview.appendChild(img);
        };
        reader.readAsDataURL(f);
      });
    };

    input.addEventListener("change", (e) => {
      renderFiles(input.files);
    });

    // render initially if any files are already selected
    if (input.files && input.files.length > 0) {
      renderFiles(input.files);
    }
  };
})();
