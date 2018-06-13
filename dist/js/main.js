var file, form_data = new FormData();

var copy = document.querySelector('#copy');
var dropZone = $('#dropZone'),
  maxFileSize = 1000000;

if (typeof (window.FileReader) == 'undefined') {
  dropZone.text('Не поддерживается браузером!');
  dropZone.addClass('error');
}

dropZone[0].ondragover = function () {
  dropZone.addClass('hover');
  return false;
};

dropZone[0].ondragleave = function () {
  dropZone.removeClass('hover');
  return false;
};

dropZone[0].ondrop = function (event) {
  event.preventDefault();
  dropZone.removeClass('hover');
  dropZone.addClass('drop');

  file = event.dataTransfer.files[0];
  // console.log(file);
  // return false;

  $('#dropZone span')
    .text(file.name)
    .css('color', '#000');

  form_data.append('file', file);

  if (file.size > maxFileSize) {
    dropZone.text('The file is too large!');
    dropZone.addClass('error');
    return false;
  }
};


var fileElem = document.querySelector('#fileElem');

fileElem.addEventListener('change', function (e) {

  $('#dropZone span')
    .text(this.files[0].name)
    .css('color', '#000');
  form_data.append('file', this.files[0]);

}, false)

$('#link').on('keypress', function (e) {
  if (e.keyCode == 13) {
    e.preventDefault();
    $('button#send_data').trigger('click');
  }
});
$('button#send_data').on('click', function () {
  if (form_data === undefined) {
    $('#dropZone span').text('Where is your file?');
    return false;
  }
  var link = $('#link').val();
  var backfix = $('#backfix').prop('checked');
  var jquery = $('#jquery').prop('checked');
  var dtime = $('#dtime').prop('checked');

  form_data.append('link', link);
  form_data.append('backfix', +backfix);
  form_data.append('jquery', +jquery);
  form_data.append('dtime', +dtime);

  $.ajax({
    url: 'src/handler.php',
    type: 'post',
    contentType: false, // важно - убираем форматирование данных по умолчанию
    processData: false, // важно - убираем преобразование строк по умолчанию
    data: form_data,
    success: function (php_script_response) {
      
      // $('#response').text(obj.info)

      //var obj = JSON.parse(php_script_response);
      //$('#dropZone span').html(obj.info); 
      $('#dropZone span').html(php_script_response); 
    }
  })
})