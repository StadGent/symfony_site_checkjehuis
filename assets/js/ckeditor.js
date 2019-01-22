var ckeditor = require( '@ckeditor/ckeditor5-build-classic');
(function (ckeditor, document) {
  var areas = document.getElementsByTagName('textarea');
  for (var i = 0; i < areas.length; i++) {
    ckeditor.create(areas[i], {
      toolbar: ["bold", "italic", "blockQuote", "heading", "link", "numberedList", "bulletedList"]
    });
  };
})(ckeditor, document);

