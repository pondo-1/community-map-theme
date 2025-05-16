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
  $lon = get_post_meta(get_the_ID(), 'longitude', true) ?  get_post_meta(get_the_ID(), 'longitude', true) : get_option('map_center_long',  true);
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
    <button type="button" id="use_geocode">verwenden</button>
  </form>
  <br>

  <b>Adresssuche</b>
  <div id="search">
    <input type="text" name="addr" value="" id="addr" size="58" />
    <button type="button" id="search_geocode" onclick="addr_search();">Suchen</button>
    <div id="results"></div>
  </div>
  <br>

</div>