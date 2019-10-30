(function($) {

	var geocoder;
  var map;
  function initialize() {
     geocoder = new google.maps.Geocoder();
     var latlng = new google.maps.LatLng(-34.609030, -58.373220);
     var mapOptions = {
       zoom: 8,
       center: latlng
     }
     map = new google.maps.Map(document.getElementById('mapas'), mapOptions);
  }

  function codeAddress(address) {
     geocoder.geocode( { 'address': address}, function(results, status) {
       if (status == 'OK') {
         map.setCenter(results[0].geometry.location);
         var marker = new google.maps.Marker({
             map: map,
             position: results[0].geometry.location
         });
       } else {
         alert('Geocode was not successful for the following reason: ' + status);
       }
     });
  }

	var loadUserContentCallback = function(form, action, target, callback){
    var data = {
      'user': $(form).serialize(),
      'action': action,
    }
    $.post(ajaxurl, data, function(data){
			console.log(data);
      $(target).html(data);

      if (callback != undefined)
        callback(data);
    })
  }

  /*initialize();*/


	let root = '#body';
	let element = '.nombre_cliente';

	$(root).off().on('click', element, function(){
		let address = $(this).data('address');
		codeAddress(address);
	})

	$(root).on('click', '.ciudad', function(){
    	$(this).siblings(".sucursal").toggleClass("mostrar", 1000, "easeOutSine");
  	});

	$('.trescol').on('click', 'span.uploadtextfield', function(){
		$(this).siblings(".file-archivo").children('.file-archivo').click();
	});

	$(root).on('change', '#provincia', function(e){
			loadUserContentCallback (this, "load_prov", '#sucursales');
	})

	let showPreloadFile = function(input){
		//var input = $('input.file-archivo')[0];
		var output = $(input).closest('p').find('input.uploadtextfield');
		console.log(output);
		output.val(input.files.item(0).name);
		console.log(input.files.item(0).name);
	}
	$('.trescol').on('change', 'input.file-archivo', function(){
		showPreloadFile(this);

	});

})(jQuery);
