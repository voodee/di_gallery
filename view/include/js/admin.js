function di_captcha_refresh() {
  var cell_size = 3;
  $.post(base_url + 'log/in/', {action: 'captcha_refresh'},
    function(data) { 
      var data = eval(data);
      $('#DICaptchaPic').css('width', ((((cell_size+2)*6)+(3*cell_size)+1)*data[0])+'px');
      
      var html_p_tag = '';
      for (i = 1; i <= 7*7*data[0]; ++i)  {
        var style = (i%7 == 0)?'margin-right: '+2*cell_size+'px;':'';
        for (j = 0; j < data[1].length; j += 2) style +=(((i%(data[0]*7)==0)?(data[0]*7):i%(data[0]*7)) == data[1][j] && Math.ceil(i/(data[0]*7)) == data[1][j+1])?'background-color: #000;':'';
        html_p_tag += '<p'+((style=='')?'':' style=\''+style+'\'')+'></p>';
      }
      $('#DICaptchaPic').html(html_p_tag);
    }
  )
}

$(document).ready(function() {
  /* Buttons */
  $('.notices .close').live('click', function(e) {
    e.preventDefault();
    $(this).closest('.notices').remove();
  });
  $('.input-control.text .helper').live('click', function(e) {
    e.preventDefault();
    $(this).parent().find('input').val('').focus();
  });
  $('.input-control.password .helper').live('click', function(e) {
    e.preventDefault();
    $(this).parent().find('input')[0].type = ($(this).parent().find('input')[0].type == 'text') ? 'password' : 'text';
    $(this).parent().find('input').focus();
  });
  /* Captcha */
  if ($('#DICaptchaPic').length != 0) {
    $('#DICaptchaPic').css('overflow', 'hidden');
    $('#DICaptchaPic').css('height', (3+2)*7);
    di_captcha_refresh();
    $('#DICaptchaPic').click(function() {di_captcha_refresh()});
  }
  setTimeout(function() {$('.notices').hide()}, 3000);
  
  if ($('.GalleryListEdite').length != 0) $('.GalleryListEdite').sortable({
    opacity: 0.6,
    stop: function(event, ui) {
      $array_id = [];
      $('.GalleryListEdite > li').each(function() {
        $array_id[$array_id.length] = $(this).data('id');
      });
      $.post(base_url + 'upload/gallery_priority/', { id: $array_id } );
    }
  });
});