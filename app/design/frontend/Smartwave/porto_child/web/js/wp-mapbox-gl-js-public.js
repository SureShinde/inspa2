(function($) {
  /* map initialization */
  $(document).ready(function() {
    if($('.wp-mapbox-gl-js-map').length) {
      var access_token = $('.wp-mapbox-gl-js-map').first().data('token');
      mapboxgl.accessToken = access_token;

      var allMaps = {};
      $('.wp-mapbox-gl-js-map').each(function() {
         var data = $(this).data();
         // console.log(data)
         var mapStyle = {
           'container' : $(this).attr('id'),
           'style' : data.style
         };
         var baseSettings = ['center','zoom','pitch','bearing',];
         baseSettings.forEach(function(setting) {
           if(data[setting]!=='') {
             if(setting==='center') {
               mapStyle[setting] = data[setting].split(',');
             } else {
               mapStyle[setting] = parseFloat(data[setting]);
             }
           }
         });
         var map = new mapboxgl.Map(mapStyle);
         allMaps[$(this).attr('id')] = map;

         map.on('load',function() {
           // console.log(data.controls)
            // Add controls
           if(data.controls.navigation) {
             var navControl = new mapboxgl.NavigationControl();
             map.addControl(navControl);
           }
           if(data.controls.geocoder) {
             var geocoder = new MapboxGeocoder({
                 accessToken: access_token
             });
             map.addControl(geocoder,'top-left');
           }
           if(data.controls.scale) {
             var scale = new mapboxgl.ScaleControl({
               maxWidth: 80,
               unit: 'metric'
             })
             map.addControl(scale,'top-left');
           }
           if(data.controls.fullscreen) {
             var fullscreen = new mapboxgl.FullscreenControl();
             map.addControl(fullscreen,'top-right');
           }
           if(data.controls.directions) {
             map.addControl(new MapboxDirections({
                accessToken: mapboxgl.accessToken
             }), 'top-left');
           }

           // Map data add
           data.mapdata.forEach(function(feature,index) {
             var featureCollection = {
               "type" : "FeatureCollection",
               "features" : [feature]
             }
             // If only a marker icon or color has changed
             if(typeof map.getSource(feature.id)==='undefined') {
               map.addSource(feature.id, {
                 "type": "geojson",
                 "data": featureCollection
               });
               if(feature.geometry.type==='Point') {
                 // Add the icon image, then add layer
                 if(!map.hasImage(feature.properties.marker_icon_url)) {
                   map.loadImage($('#wp_mapbox_gl_js_plugin_url').val()+'/wp-mapbox-gl-js/admin/wp-mapmaker/public/img/'+feature.properties.marker_icon_url, function(error, image) {
                     if (error) throw error;
                     map.addImage(feature.properties.marker_icon_url, image);
                     // default_marker.svg
                     var lineLayer = map.addLayer({
                       'id': feature.id,
                       'type': 'symbol',
                       'source' : feature.id,
                       'layout': {
                         'icon-image': feature.properties.marker_icon_url,
                         'icon-anchor' : 'bottom',
                         'icon-size' : 0.2
                       }
                     })
                     if(feature.properties.popup_open) {
                       var popup = new mapboxgl.Popup({
                         offset : 20
                       });
                       popup.setLngLat({lat: feature.geometry.coordinates[1], lng: feature.geometry.coordinates[0]})
                         .setHTML(
                           '<div>'+
                             '<div>'+feature.properties.description+'</div>'+
                           '</div>'
                         ).addTo(map);
                     }
                   });
                 } else {
                   var lineLayer = map.addLayer({
                     'id': feature.id,
                     'type': 'symbol',
                     'source' : feature.id,
                     'layout': {
                       'icon-image': feature.properties.marker_icon_url,
                       'icon-anchor' : 'bottom',
                       'icon-size' : 0.2
                     }
                   })
                 }
                 if(feature.properties.description!=='') {
                   map.on('click',feature.id,function(e) {
                     var popup = new mapboxgl.Popup({
                       offset : 20
                     })
                     popup.setLngLat({lat: feature.geometry.coordinates[1], lng: feature.geometry.coordinates[0]})
                       .setHTML(
                         '<div>'+
                           '<div>'+feature.properties.description+'</div>'+
                         '</div>'
                       ).addTo(map);
                   })
                 }
               } else if(feature.geometry.type==='Polygon') {
                 var lineLayer = map.addLayer({
                   'id': feature.id,
                   'type': 'fill',
                   'source' : feature.id,
                   'paint': {
                     'fill-color' : feature.properties.color,
                     'fill-opacity' : 0.4
                   }
                 })
               } else if(feature.geometry.type==='LineString') {
                 var lineLayer = map.addLayer({
                   'id': feature.id,
                   'type': 'line',
                   'source' : feature.id,
                   'paint': {
                     'line-color' : feature.properties.color
                   }
                 })
               }
             }
          });
        });
      });

      $(document).on('click','.wp-mapbox-gl-js-map-menu input',function() {
        allMaps[$(this).data('map-id')].setStyle($(this).attr('id'));
      });

    }
  });
})(jQuery);
