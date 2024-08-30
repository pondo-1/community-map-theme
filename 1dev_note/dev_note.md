- make geojson to display the markers
  - make geojson
  - load in frontend
  - display with kategorie icon
  - take care the popup
- prüf the cluster, group, zoom in functions

- mapapp_init.js use : /wp-json/ILEK-Map-App/geojson & /wp-json/ILEK-Map-App/infojson
  "ILEK-Map-APP" : need to be dynamic -> theme name

- Save Map center value in infojson -> use it

# Basic Structure

## General

- Benutzer Kann Marker(post_type = marker) erstellen
- Je Marker(post) hat Meta(post_meta -> name) "longitude" "latitude" -> Erstellen Pin in Map
- Marker Post type hat eigene Taxonome, Kategory gennat.
  - Es sieht aus wie original Kategory aber in Program nicht
  - term-> name : markertax
  - Je Kategory Element hat einen Icon
- Je Marker hat einen Kategory
- In Map wurde die Pins mit Kategorie-Icon bezeichnen

# Map Setting Options

- registered with : update_option
- call with : esc_attr(get_option())
- all in infojson **not yet**

| Name-------------------- | Type    | Description        |
| ------------------------ | ------- | ------------------ |
| 'map_center_point'       | $string | ex 50.0, 20.1      |
| 'map_center_long'        | $string | longitude          |
| 'map_center_lati'        | $string | latitude           |
| 'map_radius'             | $number | 0 < $number < 1000 |
| 'min_longitude'          | $number |                    |
| 'max_longitude'          | $number |                    |
| 'min_latitude'           | $number |                    |
| 'max_latitude'           | $number |                    |

# Dev Admin Menu

This is for the Dev Enviroment
Theme/functions/dev.php

- Import All Marker category and delete
- Generate Posts(post_type = marker) randomly 20
  generate 20 random posts of a custom post type marker in WordPress, with random titles, geocodes, and a randomly selected taxonomy term

# Two different Icon Art

1. Static
   selector: .icon
2. Dynamic by color selection in Theme Setting
   selector: .theme-icon, .primary or .primary--fill
