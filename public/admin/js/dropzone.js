$(function() {
  'use strict';

  $("exampleDropzone").dropzone({
    url: 'nobleui.com'
  });

    Dropzone.prototype.defaultOptions.init = function () {

        this.hiddenFileInput.removeAttribute('multiple');
        this.on("maxfilesexceeded", function (file) {
            this.removeAllFiles();
            this.addFile(file);
        });
    };

});
