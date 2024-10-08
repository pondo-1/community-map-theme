<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly   
?>
<!-- Search box reference https://stackoverflow.com/questions/15919227/get-latitude-longitude-as-per-address-given-for-leaflet -->

<meta charset="utf-8">
<!-- <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css" /> -->
<!-- <script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js"></script> -->
<style type="text/css">
  /* html, body { width:100%;padding:0;margin:0; } */
  /* .container { width:95%;max-width:980px;padding:1% 2%;margin:0 auto } */
  #lat,
  #lon {
    text-align: right
  }

  #mapapp_map {
    width: 100%;
    height: 400px;
    padding: 0;
    margin: 0;
  }

  .address {
    cursor: pointer
  }

  .address:hover {
    color: #AA0000;
    text-decoration: underline
  }

  .metadata_save_here {
    width: 300px;
    border-style: solid;
    border-color: green;
    padding: 10px;
  }
</style>

<div class="container" style="clear:both;">
  <div id="mapapp_map" style="width:50%; float:right;"></div>
  <p>Es gibt drei Möglichkeiten die genauen geografischen Koordinaten zu ermitteln und zu speichern:</p>
  <ol>
    <li>
      Ziehen sie die blaue Markierung auf der Karte auf die gewünschte Position. Klicken sie mehrfach auf das
      Plus-Symbol, um einen detaillierteren Kartenabschnitt zu sehen. Dadurch können sie die Markierung genauer an
      die richtige Stelle ziehen.
    </li>
    <li>
      Verwenden sie die Adresssuche.
    </li>
    <li>
      Geben sie die Koordinaten direkt ein. <br>
    </li>
  </ol>
  <?php
  // Check geocode meta exist, if not print map center 
  $lat = get_post_meta(get_the_ID(), 'latitude', true) ?  get_post_meta(get_the_ID(), 'latitude', true) : get_option('map_center_lati',  true);
  $lon = get_post_meta(get_the_ID(), 'logitude', true) ?  get_post_meta(get_the_ID(), 'longitude', true) : get_option('map_center_long',  true);
  ?>
  <div class="metadata_save_here">
    <div><b>Breitengrad</b><input id="latitude" type="text" name="latitude" size=12
        value="<?php echo $lat; ?>">
    </div>
    <div>
      <b>Längengrad</b><input id="longitude" type="text" name="longitude" size=12
        value="<?php echo $lon; ?>">
    </div>
  </div>
  <br>

  <h3>Suchen</h3>
  <b>Koordinaten</b>
  <form>
    <input type="text" name="lat" id="lat" size=12 value="">
    <input type="text" name="lon" id="lon" size=12 value="">
    <button type="button" onclick="save_geocode_metadata();">verwenden</button>
  </form>
  <br>

  <b>Adresssuche</b>
  <div id="search">
    <input type="text" name="addr" value="" id="addr" size="58" />
    <button type="button" onclick="addr_search();">Suchen</button>
    <div id="results"></div>
  </div>
  <br>

</div>


<script type="text/javascript">
  function save_geocode_metadata() {
    document.getElementById("longitude").value =
      document.getElementById("lon").value;
    document.getElementById("latitude").value =
      document.getElementById("lat").value;
  }

  function addr_search() {
    var inp = document.getElementById("addr");
    var xmlhttp = new XMLHttpRequest();
    var url =
      "https://nominatim.openstreetmap.org/search?format=json&limit=3&q=" +
      inp.value;
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        var myArr = JSON.parse(this.responseText);
        myFunction(myArr);
      }
    };
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
  }

  function chooseAddr(lat1, lng1) {
    myMarker.closePopup();
    map.setView([lat1, lng1], 18);
    myMarker.setLatLng([lat1, lng1]);
    lat = lat1.toFixed(8);
    lon = lng1.toFixed(8);
    document.getElementById("lat").value = lat;
    document.getElementById("lon").value = lon;
    myMarker.bindPopup("Lat " + lat + "<br />Lon " + lon).openPopup();
  }

  function myFunction(arr) {
    var out = "<br />";
    var i;

    if (arr.length > 0) {
      for (i = 0; i < arr.length; i++) {
        out +=
          "<div class='address' title='Show Location and Coordinates' onclick='chooseAddr(" +
          arr[i].lat +
          ", " +
          arr[i].lon +
          ");return false;'>" +
          arr[i].display_name +
          "</div>";
      }
      document.getElementById("results").innerHTML = out;
    } else {
      document.getElementById("results").innerHTML = "Sorry, no results...";
    }
  }
</script>