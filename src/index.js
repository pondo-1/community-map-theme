import "../scss/style.scss";


import MapApp from './map/MapApp';
document.addEventListener('DOMContentLoaded', () => {
//   const pageType = document.body.dataset.pageType;
    if (document.getElementById("mapapp_map")) {
        console.log('MapApp initialized');
        //   if (pageType === 'filter') {
        new MapApp('mapapp_map', '/wp-json/community-map-theme/geojson', {
            filter: true,
            slider: false,
            editable: false
        });
        //   }
    }
//   if (pageType === 'view-only') {
//     new MapApp('map', '/wp-content/themes/dein-theme/assets/data/marker-data.json', {
//       filter: false,
//       slider: false,
//       editable: false
//     });
//   }

//   if (pageType === 'edit') {
//     new MapApp('map', null, {
//       editable: true
//     });
//   }
});


