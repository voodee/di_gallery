$(document).ready(function() {
  if (Modernizr.csstransforms3d) {
    if (fullScreenApi.supportsFullScreen) {
      $('#CubeStart').show();
      $('#CubeStart').find('a').click(function(e) {
        e.preventDefault();
        $('#CubeStart').hide();
        cube.init();
        fullScreenApi.requestFullScreen(document.getElementById('fullscreen'));
      });
      $(window).keyup(function(event) {
        if (event.keyCode == 122) {
          event.preventDefault();
          $('#CubeStart').hide();
          cube.init();
          fullScreenApi.requestFullScreen(document.getElementById('fullscreen'));
        }
      });
      
    } else {
      cube.init();
    }
  } else {
    var easycube = (function() {
      var
        $obj = $('#EasyMain'),
        path_photo = base_url + 'view/images/',
        gallery = {
          page: 0,
          photo: ''
        },
        name_gallery = '',
        $loading =  $('<div></div>').addClass('EasyCubeLoading').activity({segments: 8, steps: 3, opacity: 0.3, width: 3, space: 0, length: 5, color: '#fff', speed: 1.5})

      init = function() {
        $('body').css({'overflow-y': 'auto', 'overflow-x': 'hidden'});
        $obj.show();
        // animated
        $('#BGLoading').activity({segments: 3, steps: 3, opacity: 0.3, width: 15, space: 4, length: 10, color: '#fff', speed: 1.5});
        // action
        $('#EasyMainGalleryList li a').live('click', function(e) {
          get_gallery($(this).data('id_gallery'));
          move_on_main();
        });
        $('#EasyMainPhoto li a').live('click', function(e) {
          get_photo($(this).find('img').data('pic'));
        });
        $('.BGButtonZoom').live('click', function(e) {
          e.preventDefault();
          if ($(this).hasClass('BGButtonZoomFull')) {
            $(this).removeClass('BGButtonZoomFull').addClass('BGButtonZoomNoFull');
            $('.jTscrollerContainer').hide();
            $('.BGNoScroll').stop().animate({opacity: 'toggle'}, 2000).show();
          } else {
            $(this).removeClass('BGButtonZoomNoFull').addClass('BGButtonZoomFull');
            $('.jTscrollerContainer').stop().animate({opacity: 'toggle'}, 2000).show();
            $('.BGNoScroll').hide();
          }
        });
        $('.BGFilter').live('click', function(e) {
          e.preventDefault();
          window.location.hash = (name_gallery != '') ? 'gallery='+name_gallery : '';
          $('#BG').hide();
          $obj.show();
          $('.BGButtonZoom').removeClass('BGButtonZoomNoFull').addClass('BGButtonZoomFull');
          $('.jTscrollerContainer').stop().show();
          $('.BGNoScroll').stop().hide();
        });
        // Strat
        (function() {
          if ($.url().fparam('photo') != undefined && $.url().fparam('photo') != '') {
            get_photo($.url().fparam('photo'));
            get_gallery($('#ListGallerys li a:first').data('id_gallery'));
            return;
          }
          if ($.url().fparam('gallery') != undefined && $.url().fparam('gallery') != '')
            if ($('#ListGallerys li a[data-id_gallery=' + $.url().fparam('gallery') + ']') != undefined) {
              get_gallery($.url().fparam('gallery'));
              return;
            }
          get_gallery($('#EasyMainGalleryList li a:first').data('id_gallery'));
        }());
      },

      get_photo = function(name) {
        window.location.hash = 'photo=' + name;
        $('.jThumbnailScroller').find('.jTscrollerContainer').remove();
        $('.jThumbnailScroller').prepend('<div class=\'jTscrollerContainer\'><div class=\'jTscroller\'></div></div>');
        $obj.hide();
        $('#BGLoading').show();
        $('#BG').find('.jTscroller').empty().append(
          $('<img />')
          .error(function() {
            $('#BGLoading').hide();
            $obj.show();
          })
          .load(function() {
            $('#BG').find('.BGNoScroll').empty().append(
              $('<img />')
              .load(function() {
                $('#BG').show().thumbnailScroller({scrollerOrientation: 'vertical'});
                $('#BGLoading').hide();
              })
              .attr('src', path_photo + name + '/0/' + (($(document).height() < $(window).height()) ? $(document).height() : $(window).height()) + '/')
            );
          })
          .attr('src', path_photo + name + '/' + $(document).width() + '/')
        );
      },

      get_gallery = function(id_gallery) {
        name_gallery = id_gallery;

        $('#EasyMainPhoto').animate({
          height: ['toggle', 'swing'],
          opacity: 'toggle'
        }, 500);

        $.ajax({
          type: 'POST',
          url: base_url + 'ajax/get_list_photo/',
          data: {'gallery_id': id_gallery},
          dataType: 'json',
          success: function(data) {
            gallery.page = 0;
            gallery.photo = data;
            $('#EasyMainPhoto').empty();
            for (i in data) {
              $('#EasyMainPhoto').append(
                $('<li />').append(
                  $('<a />')
                    .append($('<img />').attr('src', path_photo + data[i] + '/90/90/').data('pic', data[i]))
                    .append($loading.clone().append($('<p>терпение</p>')))
                  .attr('href', '#photo='+data[i])
                )
              );
            }
            $('#EasyMainPhoto').animate({
          height: ['toggle', 'swing'],
          opacity: 'toggle'
        }, 500);
          },
          error: function() {
            alert('error');
          }
        });
      }

      return { init : init };
    })();
    easycube.init();
  }
});

var cube = (function() {
  var
    hard = false,
    path_photo = base_url + 'view/images/',
    $cube = $('#AreaCube'),
    el = $('<div></div>'),
    transformProps = '-ms-transform -moz-transform -webkit-transform -o-transform transform'.split(' '),
    transformProp = (function() {
      for(var i = 0, l = transformProps.length; i < l; ++i)
        if(el.css(transformProps[i]) !== undefined) return transformProps[i];
    })(),
    nextpageactive = false,
    nextgalleryactive = false,
    position_cursor = {
      x: 0,
      y: 0
    },
    angle = {
      x: 0,
      y: 0
    },
    settings_animated  = {
      maxScale  : 1.7,
      minScale: 1,
      maxOpacity  : 0.9,
      minOpacity  : 0.4
    },
    gallery = {
      inew: true,
      page: 0,
      photo: []
    },
    $replace_box = $('<div></div>')
                     .addClass('BoxTransitionCard')
                     .append($('<div></div>').addClass('BoxTransitionCardFront'))
                     .append($('<div></div>').addClass('BoxTransitionCardBack')),
    $loading =  $('<div></div>').addClass('ListPhoto3DButtonLoad').activity({segments: 8, steps: 3, opacity: 0.3, width: 3, space: 0, length: 5, color: '#fff', speed: 1.5}),
    name_gallery = '';

  init = function() {
    if ($.browser.chrome) hard = true;

    $('body').css({'overflow': 'hidden'});
    $('#Main').show();

    $(window).resize(function() {
      resize();
    });
    resize();

    $('#BGLoading').activity({segments: 3, steps: 3, opacity: 0.3, width: 15, space: 4, length: 10, color: '#fff', speed: 1.5});

    $(document).mousemove(
      function (e) {
        position_cursor.x = e.pageX ;
        position_cursor.y = e.pageY ;
        return true;
      }
    );

    setInterval(function () {
      vibration_x_deg = 40;
      vibration_y_deg = 10;
      pos_x = -1 * ( ( ( (position_cursor.y / $(window).height()) - 0.5 ) * vibration_y_deg ) + angle.y );
      pos_y = ( ( (position_cursor.x / $(window).width()) - 0.5 ) * vibration_x_deg ) + angle.x;
      $cube.css(transformProp, 'rotateX(' + pos_x + 'deg) rotateY(' + pos_y + 'deg)')
    }, 100);

    $('.MoveOnMain').click(function(e) {
      e.preventDefault();
      move_on_main();
    });

    $('#MoveOnAlbum').click(function(e) {
      e.preventDefault();
      if (nextgalleryactive) return;
      move_on_album();
    });

    $('#MoveOnAbout').click(function(e) {
      e.preventDefault();
      move_on_about();
    });

    $('#NextPage').live('click', function(e) {
      e.preventDefault();
      if (!nextpageactive) next_page();
    });

    $('#ListGallerys li a').live('click', function(e) {
      e.preventDefault();
      //if (!nextgalleryactive) 
      get_gallery($(this).data('id_gallery'));
      move_on_main();
    });

    $('.ListPhoto3D li:not(.ListPhoto3DButton) a').live('click', function(e) {
      e.preventDefault();
      get_photo($(this).find('img').data('pic'));
    });

    $('.BGButtonZoom').live('click', function(e) {
      e.preventDefault();
      if ($(this).hasClass('BGButtonZoomFull')) {
        $(this).removeClass('BGButtonZoomFull').addClass('BGButtonZoomNoFull');
        $('.jTscrollerContainer').hide();
        $('.BGNoScroll').stop().animate({opacity: 'toggle'}, 2000).show();
      } else {
        $(this).removeClass('BGButtonZoomNoFull').addClass('BGButtonZoomFull');
        $('.jTscrollerContainer').stop().animate({opacity: 'toggle'}, 2000).show();
        $('.BGNoScroll').hide();
      }
    });

    $('.BGFilter').live('click', function(e) {
      e.preventDefault();
      window.location.hash = (name_gallery != '') ? 'gallery='+name_gallery : '';
      $('#BG').hide();
      $('#Main').show();

      $('.BGButtonZoom').removeClass('BGButtonZoomNoFull').addClass('BGButtonZoomFull');
      $('.jTscrollerContainer').stop().show();
      $('.BGNoScroll').stop().hide();
    });

    $('.ListPhoto3D li:not(.ListPhoto3DButton)').each(function() {
      $(this)
        .append(
          $('<a href=\'#\'></a>').data('minTranslate', Math.floor(Math.random( ) * (101))*-1).addClass('invisible')
        );
    });

    ///// Hard test
    if (hard)
      setInterval(function () {
        $('.ListPhoto3D li:not(.ListPhoto3DButton)').each(function() {
          max = 10;
          min = -10;
          var rand = Math.floor((max - min + 1) * Math.random() + min);
          translate = $(this).find('a').data('minTranslate');
          if (translate - rand > 0) rand = rand * -1;
          else if (translate - rand < -100) rand = rand * -1;
          $(this).find('a').data('minTranslate',  translate - rand);

          if ($(this).find('a').find('.ListPhoto3DItem').data('translateval') != undefined) {
            $el = $(this).find('a').find('.ListPhoto3DItem');
            scaleVal = $el.data('scaleval');
            translateVal = $el.data('translateval') - rand;
            $el.data('translateval', translateVal);
            css = {};
            css[transformProp] = 'scale(' + scaleVal + ') translateZ(' + translateVal + 'px)';
            $el.css(css);
          }

        });
      }, 100);
    ///// Hard test end


    $('.ListPhoto3D li.ListPhoto3DButton a').each(function() {
      $(this).data('minTranslate', Math.floor(Math.random( ) * (101))*-1)
    });

    $('#CubeAlbum').sbscroller();
    $('#CubeAbout').sbscroller();

    // Strat
    (function() {
      if ($.url().fparam('photo') != undefined && $.url().fparam('photo') != '') {
        get_photo($.url().fparam('photo'));
        get_gallery($('#ListGallerys li a:first').data('id_gallery'));
        return;
      }
      if ($.url().fparam('gallery') != undefined && $.url().fparam('gallery') != '')
        if ($('#ListGallerys li a[data-id_gallery=' + $.url().fparam('gallery') + ']') != undefined) {
          get_gallery($.url().fparam('gallery'));
          return;
        }
      if ($.url().data.attr.fragment == 'about') move_on_about();
      if ($.url().data.attr.fragment == 'album') move_on_album();
      get_gallery($('#ListGallerys li a:first').data('id_gallery'));
    }());
  },

  get_photo = function(name) {
    window.location.hash = 'photo=' + name;
    $('.jThumbnailScroller').find('.jTscrollerContainer').remove();
    $('.jThumbnailScroller').prepend('<div class=\'jTscrollerContainer\'><div class=\'jTscroller\'></div></div>');
    $('#Main').hide();
    $('#BGLoading').show();
    $('#BG').find('.jTscroller').empty().append(
      $('<img />')
      .error(function() {
        $('#BGLoading').hide();
        $('#Main').show();
      })
      .load(function() {
        $('#BG').find('.BGNoScroll').empty().append(
          $('<img />')
          .load(function() {
            $('#BG').show().thumbnailScroller({scrollerOrientation: 'vertical'});
            $('#BGLoading').hide();
          })
          .attr('src', path_photo + name + '/0/' + (($(document).height() < $(window).height()) ? $(document).height() : $(window).height()) + '/')
        );
      })
      .attr('src', path_photo + name + '/' + $(document).width() + '/')
    );
  },

  resize = function() {
    css = {};
    scale = ((($(window).height() < $(document).height()) ? $(window).height() : $(document).height())/9/100).toFixed(2);
    css[transformProp] = 'scale(' + scale + ', ' + scale + ')';
    $('#Main').css(css);
  },

  definition = function(props) {
    for(var i = 0, l = props.length; i < l; ++i)
      if(el.css(props[i]) !== undefined) return props[i];
  },

  move_on_main = function() {
    $('#CubeAbout').css({'opacity': .1});
    $('#CubeAlbum').css({'opacity': .1});
    window.location.hash = (name_gallery != '') ? 'gallery='+name_gallery : '';
    angle.x = 0;
    angle.y = 0;
  },

  move_on_album = function() {
    $('#CubeAbout').css({'opacity': .1});
    $('#CubeAlbum').css({'opacity': 1});
    window.location.hash = 'album';
    angle.x = -90;
    angle.y = 0;
  },

  move_on_about = function() {
    $('#CubeAbout').css({'opacity': .7});
    $('#CubeAlbum').css({'opacity': .1});
    window.location.hash = 'about';
    angle.x = 90;
    angle.y = 0;
  },


  get_gallery = function(id_gallery) {
    name_gallery = id_gallery;
    nextgalleryactive = true;
    //
    $('#MoveOnAlbum').find('.CubeZoom').hide();
    $('#MoveOnAlbum').append($loading);
    //
    $.ajax({
      type: 'POST',
      url: base_url + 'ajax/get_list_photo/',
      data: {'gallery_id': id_gallery},
      dataType: 'json',
      success: function(data) {
        gallery.page = 0;
        gallery.photo = data; 
        if (gallery.photo.length <= 34) {  
          if ($('.ListPhoto3D li:nth-child(35)').hasClass('ListPhoto3DButton')) 
            $('.ListPhoto3D li:nth-child(35)')
              .removeClass()
              .empty()
              .append($('<a href=\'#\'></a>').data('minTranslate', Math.floor(Math.random( ) * (101))*-1));
        } else
          $('.ListPhoto3D li:nth-child(35)')
            .removeClass()
            .addClass('ListPhoto3DButton')
            .empty()
            .append(
              $('<a href=\'#\' id=\'NextPage\'><p class=\'CubeZoom\'>далее</p></a>')
                .data('minTranslate', Math.floor(Math.random( ) * (101))*-1)
            );
        next_page();
      },
      error: function() {
        alert('error');
      }
    });
  }


  handle_next_page_animated = function(n_el, i, _that, _new_replace_box) {
    var
      that = _that,
      $new_replace_box = _new_replace_box,
      start_next = false;

      $new_replace_box.animate(
        {borderSpacing: 180},
        {
          step: function(now, fx) {
            _css = {};
            _css[transformProp] = 'rotateX(' + now + 'deg) rotateY(' + now + 'deg) translateZ(' + $(this).parent().data('minTranslate') + 'px)';
            $(this).css(_css);
            $(this).find('.BoxTransitionCardBack').css({'opacity': (1/180)*now});
            $(this).find('.BoxTransitionCardFront').css({'opacity': 1-((1/180)*now)});

            if (now > 60 && !start_next) {
              start_next = true;
              handle_next_page(++n_el, ++i);
            }
          },
          duration: 1000,
          complete: function() {
            $(this).closest('a').append(
              $(this).find('.BoxTransitionCardBack').find('img')
                .removeClass()
                .addClass('ListPhoto3DItem')
                .css(_css)
                .data('scaleval', 1)
                .data('translateval', $(this).closest('a').data('minTranslate'))
            );
            $new_replace_box.remove();
            on_animated();
          }
        });
  }

  handle_next_page = function(n_el, i) {
    $list = $('.ListPhoto3D').find('li:not(.ListPhoto3DButton)');

    if (n_el+1 > $list.length ) {
      ++gallery.page;
      nextpageactive = false;
      nextgalleryactive = false;
      $('#NextPage').css({'visibility': 'visible'});
      //
      $('#MoveOnAlbum').find('.CubeZoom').show();
      $loading.detach();
      //
      if ($list.length * gallery.page > gallery.photo.length) gallery.page = 0;
      return
    };

    var $_el = $($list[n_el]).find('a');

    if (gallery.photo.length > i) {
      $('<img />').load(function () {
        var
          that = this,
          $new_replace_box = $replace_box.clone(),
          _css = {};

        $new_replace_box.data('number_i', i);
        _css['opacity'] = $_el.find('.ListPhoto3DItem').css('opacity');
        _css[transformProp] = 'translateZ(' + $_el.find('.ListPhoto3DItem').parent().data('minTranslate') + 'px)';
        $new_replace_box.css(_css);

        if ($_el.find('.ListPhoto3DItem').attr('src') != undefined) {
          $new_replace_box.find('.BoxTransitionCardFront').append(
            $('<img />').load(function () {
              $new_replace_box.find('.BoxTransitionCardBack').append($(that));
              $_el.removeClass('invisible').append($new_replace_box);

              handle_next_page_animated(n_el, i, that, $new_replace_box);
              $_el.find('.ListPhoto3DItem').hide().remove();
            }).attr('src', $_el.find('.ListPhoto3DItem').attr('src'))
          );
        } else {
          $new_replace_box.find('.BoxTransitionCardBack').append($(that));
          $_el.removeClass('invisible').append($new_replace_box);
          handle_next_page_animated(n_el, i, that, $new_replace_box);
        }
      })
      .data('pic', gallery.photo[i])
      .attr('src', path_photo + gallery.photo[i] + '/100/100/');
    } else {
      if ($_el.find('.ListPhoto3DItem').length != 0) {
        $_el.find('.ListPhoto3DItem').fadeOut(100, function () {
          $(this).parent().addClass('invisible');
          $(this).remove();
          handle_next_page(++n_el, ++i);
        });
      }
      else handle_next_page(++n_el, ++i);

    }
  }

  next_page = function() {
    nextpageactive = true;
    $('#NextPage').css({'visibility': 'hidden'}); 
    i = $('.ListPhoto3D').find('li:not(.ListPhoto3DButton)').length * gallery.page;
    handle_next_page(0, i);
  }

  clear_area = function () {
    $('.ListPhoto3D li:not(.ListPhoto3DButton)').find('a').empty();
  }

  on_animated = function() {
    var
      $elems = $('.ListPhoto3D').find('.ListPhoto3DItem:not(.ProximityY), .CubeZoom:not(.ProximityY)');

    $elems.each(function() {
      _css = {};
      _css[transformProp] = 'translateZ(' + $(this).parent().data('minTranslate') + 'px)';
      $(this).css(_css);
    });

    $elems.on('proximity.Photo', { max: 70, throttle: 100, fireOutOfBounds : true }, function( event, proximity, distance ) {
      var
        $el = $(this),
        $li = $el.closest('li'),
        scaleVal  = proximity * ( settings_animated.maxScale - settings_animated.minScale ) + settings_animated.minScale,
        translateVal  = proximity * ( 100 - $el.parent().data('minTranslate') ) + $el.parent().data('minTranslate');

      ( scaleVal === settings_animated.maxScale ) ? $li.css( 'z-index', 1000 ) : $li.css( 'z-index', 1 );
      _obj = {};
      _obj[transformProp] = 'scale(' + scaleVal + ') translateZ(' + translateVal + 'px)';
      _obj['opacity'] = ( proximity * ( settings_animated.maxOpacity - settings_animated.minOpacity ) + settings_animated.minOpacity );
      $el.css(_obj);
      $el.data('scaleval', scaleVal);
      $el.data('translateval', translateVal);
    })
    .addClass('ProximityY');
  };

  off_animated = function() {
    $('.ListPhoto3D').find('.ListPhoto3DItem, .CubeZoom').off('.Photo').removeClass('ProximityY');
  }

  return { init : init };
})();


(function() {
    var
        fullScreenApi = {
            supportsFullScreen: false,
            isFullScreen: function() { return false; },
            requestFullScreen: function() {},
            cancelFullScreen: function() {},
            fullScreenEventName: '',
            prefix: ''
        },
        browserPrefixes = 'webkit moz o ms khtml'.split(' ');

    // check for native support
    if (typeof document.cancelFullScreen != 'undefined') {
        fullScreenApi.supportsFullScreen = true;
    } else {
        // check for fullscreen support by vendor prefix
        for (var i = 0, il = browserPrefixes.length; i < il; i++ ) {
            fullScreenApi.prefix = browserPrefixes[i];

            if (typeof document[fullScreenApi.prefix + 'CancelFullScreen' ] != 'undefined' ) {
                fullScreenApi.supportsFullScreen = true;

                break;
            }
        }
    }

    // update methods to do something useful
    if (fullScreenApi.supportsFullScreen) {
        fullScreenApi.fullScreenEventName = fullScreenApi.prefix + 'fullscreenchange';

        fullScreenApi.isFullScreen = function() {
            switch (this.prefix) {
                case '':
                    return document.fullScreen;
                case 'webkit':
                    return document.webkitIsFullScreen;
                default:
                    return document[this.prefix + 'FullScreen'];
            }
        }
        fullScreenApi.requestFullScreen = function(el) {
            return (this.prefix === '') ? el.requestFullScreen() : el[this.prefix + 'RequestFullScreen']();
        }
        fullScreenApi.cancelFullScreen = function(el) {
            return (this.prefix === '') ? document.cancelFullScreen() : document[this.prefix + 'CancelFullScreen']();
        }
    }

    // jQuery plugin
    if (typeof jQuery != 'undefined') {
        jQuery.fn.requestFullScreen = function() {

            return this.each(function() {
                if (fullScreenApi.supportsFullScreen) {
                    fullScreenApi.requestFullScreen(this);
                }
            });
        };
    }

    // export api
    window.fullScreenApi = fullScreenApi;
})();




var t;