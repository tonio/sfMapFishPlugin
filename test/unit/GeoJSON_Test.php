<?php

require_once(dirname(__FILE__).'/../bootstrap/unit.php');

$t = new lime_test(13, new lime_output_color());

try
{

  $point = new Point(0, 0);
  $point_string = '{"type":"Point","coordinates":[0,0]}';
  $line1 = new LineString(array(new Point(0, 0), new Point(0, 10), new Point(10, 10), new Point(10, 0), new Point(0, 0)));
  $line1_string =  '{"type":"LineString","coordinates":[[0,0],[0,10],[10,10],[10,0],[0,0]]}';
  $line2 = new LineString(array(new Point(1, 1), new Point(2, 5), new Point(1, 1))); // inner ring
  $polygon = new Polygon(array($line1, $line2));
  $polygon_string = '{"type":"Polygon","coordinates":[[[0,0],[0,10],[10,10],[10,0],[0,0]],[[1,1],[2,5],[1,1]]]}';
  $polygon2 = new Polygon(array($line2));
  $multipoint = new MultiPoint(array(new Point(0, 0), new Point(0, 10), new Point(10, 10)));
  $multilinestring = new MultiLineString(array($line1, $line2));
  $multipolygon = new MultiPolygon(array($polygon, $polygon2));
  $geomcoll = new GeometryCollection(array(new Point(1, 10), $line));
  $geojson = '{"type":"Feature","id":6,"geometry":{"type":"GeometryCollection","geometries":[{"type":"Point","coordinates":[1,10]},{"type":"LineString","coordinates":[[1,10],[2,15]]}]},"properties":{"name":"toto","age":20}}';

}
catch (Exception $e)
{
  $t->fail();
}

$t->diag('GeoJSON class');

$t->is($point->__toString(), $point_string, 'Dumps a point');
$t->is($line1->__toString(), $line1_string, 'Dumps a line');
$t->is($polygon->__toString(), $polygon_string, 'Dumps a polygon');

$t->is($geojson, GeoJSON::dump(GeoJSON::load($geojson)), 'Load then dump returns same GeoJSON');

$t->is(get_class(GeoJSON::load($geojson)), 'Feature', 'Correctly load into Feature');
$t->is(GeoJSON::load($geojson)->getProperties(), array('name'=>'toto', 'age'=>20), 'Correctly load properties');
$t->is(GeoJSON::load($geojson)->getId(), 6, 'Correctly load Feature id');
$t->is(get_class(GeoJSON::load($geojson)->getGeometry()), 'GeometryCollection', 'Correctly load Feature geometry');

$geojson = '{"type":"Feature","id":6,"geometry":null, "properties": {}}';
$t->is(get_class(GeoJSON::load($geojson)), 'Feature', 'Load allows null Geometry');

$geojson = '{"type":"Feature","id":null,"geometry": {"type":"Point","coordinates":[0,0]}, "properties": {}}';
$t->is(get_class(GeoJSON::load($geojson)), 'Feature', 'Load allows null id');

$geojson = '{"type":"Feature","geometry":null, "properties": {}}';
$t->is(get_class(GeoJSON::load($geojson)), 'Feature', 'Load allows missing id');

try {
  $geojson = '{"type":"Feature","id":6,"geometry":null}';
  GeoJSON::load($geojson);
  $t->fail('Properties in Feature should be mandatory');
} catch (Exception $e) {
  $t->pass('Propeties in Feature in Feature are mandatory');
}

try {
  $geojson = '{"id":6,"geometry":null, "properties": {}}';
  GeoJSON::load($geojson);
  $t->fail('Type in Feature should be mandatory');
} catch (Exception $e) {
  $t->pass('Type in Feature is mandatory');
}



