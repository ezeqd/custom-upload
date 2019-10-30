(function($){

  let UploadController = function(){

    let self = this;

    let eventListener;

    showPreloadFileList = function(){
      var input = document.getElementById('uploadInput');
      var output = $('.uc-file-name');
      output.append(input.files.item(0).name);
    }

    self.init = function(listener){
      eventListener = listener;
    }

    self.run = function(){

      $(eventListener).on('change', '#uploadInput', function(){
        showPreloadFileList();
      });
    }
  }

  let Navigator = function(){
    let self = this;

    let eventListener = '#fileTree';

    let target = '#fileTree .uc-list';

    let actualDir;

    let filename;

    let navigate = function(dirName){
      var data = {
        'path': dirName,
        'action': 'cu_navigate',
      }
      $.get(ajaxurl, data, function(data){
        $(target).html(data);
      })
    }

    self.run = function(){

      $(eventListener).off().on('click', '.uc-dir', function(){
        let dirName = $(this).text();
        navigate(dirName);
      });

      $(eventListener).on('click', '#ucGoBack', function(){
        navigate();
      });

    }
  }

// ####################################################################

  var loadUserContentCallback = function(form, action, target, callback){
    var data = {
      'user': $(form).serialize(),
      'action': action,
    }
    $.post(ajaxurl, data, function(data){
      $(target).html(data);

      if (callback != undefined)
        callback(data);
    })
  }

  var sendContent = function(form, action, target, callback){
    var data = {
      'data': $(form).serialize(),
      'action': action,
    }
    $.post(ajaxurl, data, function(data){
      data = JSON.parse(data);
      if (data['response'] != undefined && target.length > 0)
        $(target).html(data['response']);

      if (callback != undefined)
        callback(data);
    })
  }

  var showCallbackMessage = function(msg, type){
    $('.cu-loader').empty();
    $('#actionResult').removeClass('hidden');
    msg = '<p>'+msg+'</p>';
    $('#actionResult').removeClass();
    $('#actionResult').addClass(type);
    $('#actionResult').html(msg);
  }

  var geocodeResults = [];

  var geocoder = new google.maps.Geocoder();
  /*
    Nota: Se añaden los timeout debido a que la api de google responde que se alcanza
    el limite de peticiones por segundo. Con los timeout garantizamos que se disminuyan
    la cantidad de respuestas de error.
  */
  var geocodeSucursales = function(i, sucursales, cant, results, progressTarget, resultTarget){
    if (i<cant){
        let sucursal = sucursales[i];
        let location = sucursal['direccion_real']+","+sucursal['ciudad']+","+sucursal['provincia']+",Argentina";

        geocoder.geocode( { 'address': location}, function(response, status) {
         if (status == google.maps.GeocoderStatus.OK) {

           let lat = response[0].geometry.location.lat();
           let lng = response[0].geometry.location.lng();

           results[i] = [ sucursal['id'], lat, lng ];

           setTimeout(function(){
             i++;
             $(progressTarget).html('<p>Geolocalizando sucursal ' + i + ' de '+cant+'</p>');
             geocodeSucursales(i, sucursales, cant, results, progressTarget, resultTarget);
           }, 500);
         }
         else if (status == google.maps.GeocoderStatus.OVER_QUERY_LIMIT) {
           setTimeout(function(){
              $(progressTarget).html('<p>Se produjo un error geolocalizando la sucursal '+ i+'</p>');
              $(progressTarget).append('<p>Reintentando...</p>');
              $(progressTarget).append('<p>Geolocalizando sucursal ' + i + ' de '+cant+'</p>');
              geocodeSucursales(i, sucursales, cant, results, progressTarget, resultTarget);
           }, 1000);
         }
       });
  } else{
    var data = {
      'data': results,
      'action': 'cu_geocode_sucursales',
    }
    $.post(ajaxurl, data, function(data){
      data = JSON.parse(data);
      $(progressTarget).append('<p>PROCESO DE GEOLOCALIZACIÓN FINALIZADO</p>')

      if (data['response'] != undefined){
        $('#actionResult').removeClass('hidden');
        if (data['response'].length){
          $('#actionResult').addClass('gbs-error');
          $(resultTarget).html('Se produjo uno o mas errores al actualizar la base de datos. Contáctese con el administrador.');
        }else{
          $('#actionResult').addClass('gbs-success');
          $(resultTarget).html('Se completó exitosamente el proceso de geolocalización');
        }
        $('body').removeClass('cu-progress');
      }
    })
  }

  }

  $(document).ready(function(){

    let root = '#customUploadPanel';

    $(root).off().on('submit', '#filesByClientForm', function(e){
      let loader = '<i class="fa fa-spinner fa-pulse fa-5x fa-fw" aria-hidden="true"></i>';
      e.preventDefault(); e.stopPropagation();
      $('#filesPermissionTable').html(loader);
      loadUserContentCallback(this, 'load_permission', '#filesPermissionTable');

    });

    $(root).on('click', '#initGeocode', function(){
      $('body').addClass('cu-progress');
      loadUserContentCallback('', 'cu_get_all_sucursales', '', function(data){
        data = JSON.parse(data);
        let geocodeProgress = '.geocode-progress .bodyProgress';
        let geocodeResultTarget = '#actionResult';
        $('.geocode-progress .headerProgress').html('<p>INICIANDO PROCESO DE GEOLOCALIZACIÓN</p>');
        geocodeSucursales(0, data, data.length, geocodeResults, geocodeProgress, geocodeResultTarget);
      })
    });

    $(root).on('submit', '#downloadsByClientForm', function(e){
      let loader = '<i class="fa fa-spinner fa-pulse fa-5x fa-fw" aria-hidden="true"></i>';
      e.preventDefault(); e.stopPropagation();
      $('#filesDownloadsTable').html(loader);
      loadUserContentCallback(this, 'load_history', '#filesDownloadsTable');
    });

    $(root).on('submit', '#newClientForm', function(e){
      let loader = '<i class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i>';
      e.preventDefault(); e.stopPropagation();
      $('.cu-loader').html(loader);

      sendContent(this, 'cu_add_client', '#newClientsTable table', function(data){
        showCallbackMessage(data['msg'], data['type']);
      });
    });

    $(root).on('change', '#sucursalClientSelection select', function(e){
      let loader = '<i class="fa fa-spinner fa-pulse fa-5x fa-fw" aria-hidden="true"></i>';
      $('.cu-loadIndicator').html(loader);
      $('.uc-list ul').empty();
      $('#sucursalInput').val('');

      loadUserContentCallback(this, 'cu_get_sucursales', '.uc-list ul', function(){
        $('.cu-loadIndicator').empty();
      });
    })

    $(root).on('submit', '#uploadSucursalForm', function(e){
      e.preventDefault(); e.stopPropagation();
      let loader = '<i class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i>';
      $('.cu-loader').html(loader);

      sendContent(this, 'cu_add_sucursal', '.uc-list ul', function(data){
        showCallbackMessage(data['msg'], data['type']);
        $('#sucursalInput').val('');

      });
    })

    $(root).on('submit', '#editFeaturesForm', function(e){
      e.preventDefault(); e.stopPropagation();
      let loader = '<i class="fa fa-spinner fa-pulse fa-3x fa-fw" aria-hidden="true"></i>';
      $('.cu-loader').html(loader);
      sendContent(this, 'cu_edit_features', '#actionResult', function(data){
        $('.cu-loader').empty();
        $('#actionResult').addClass(data['type']);
        $('#actionResult').removeClass('hidden');
      })
    })

    $(root).on('click', '.edit-client', function(){
      let row = $(this).closest('tr');
      row.find('.client-name').hide();
      row.find('.edit-client-name').show();
    });

    $(root).on('click', '.cancel-edit-client', function(){
      let row = $(this).closest('tr');
      row.find('.client-name').show();
      row.find('.edit-client-name').hide();
      row.find('input').val("");
    });

    $(root).on('click', '.confirm-edit-client', function(){
      let form = $(this).closest('form');
      let target = $(this).closest('tr').find('.client-name');
      $('body').addClass('cu-progress');
      sendContent(form, 'cu_edit_client', '#'+target.attr('id'), function(data){
        showCallbackMessage(data['msg'], data['type']);
        $('.cancel-edit-client').click();
        $('body').removeClass('cu-progress');
      })
    });

    $(root).on('click', '.delete-client', function(){
      let form = $(this).closest('form');
      $('body').addClass('cu-progress');
      let confirmation = confirm('ATENCIÓN, vas a eliminar un cliente! ¿Deseas continuar?');
      if (confirmation){
        sendContent(form, 'cu_delete_client', '', function(data){
          showCallbackMessage(data['msg'], data['type']);
          let id = form.attr('data-delete');
          $('#cliente_'+id).closest('tr').remove();
          $('body').removeClass('cu-progress');
        });
      }
    });

    let controller = new UploadController();
    let nav = new Navigator();

    controller.init(root);
    controller.run();
    nav.run();
  })
})(jQuery);