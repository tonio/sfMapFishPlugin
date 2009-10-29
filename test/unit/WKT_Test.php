<?php
/*
 * This file is part of the sfMapFishPlugin package.
 * (c) Camptocamp <info@camptocamp.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @package    sfMapFishPlugin
 * @author     Camptocamp <info@camptocamp.com>
 */
require_once(dirname(__FILE__).'/../bootstrap/unit.php');

$t = new lime_test(21, new lime_output_color());

$t->diag('WKT class');

$null = WKT::load('invalid');
$t->is($null, null, 'Invalid WKT returns null value');

$null = WKT::load('invalid(10, 10)');
$t->is($null, null, 'Invalid WKT returns null value');

$point = WKT::load('POINT (10 11)');
$t->is(get_class($point), 'Point', 'Loads a point into a Point object');
$t->is($point->getX(), 10, 'Loads a correct X coordinates');
$t->is($point->getY(), 11, 'Loads a correct Y coordinates');

$multipoint = WKT::load('MULTIPOINT(10 10, 20 20)');
$t->is(get_class($multipoint), 'MultiPoint', 'Loads a point into a MultiPoint object');

$linestring = WKT::load('LINESTRING( 10 10, 20 20, 30 40)');
$t->is(get_class($linestring), 'LineString', 'Loads a point into a LineString object');

$multilinestring = WKT::load('MULTILINESTRING(( 10 10, 20 20, 30 40), ( 15 16, 21 22, 35 45))');
$t->is(get_class($multilinestring), 'MultiLineString', 'Loads a point into a MultiLineString object');

$polygon = WKT::load('POLYGON ((10 10, 10 20, 20 20, 20 15, 10 10))');
$t->is(get_class($polygon), 'Polygon', 'Loads a point into a Polygon object');

$polygon2 = WKT::load('POLYGON ((10 10, 10 20, 20 20, 20 15, 10 10), (10 11, 10 21, 20 21, 20 15, 10 11))');
$t->is(get_class($polygon2), 'Polygon', 'Loads a polygon with internal ring into a Polygon object');
$t->is(count($polygon2->getComponents()), 2, 'Polygon with internal ring has correct count of components');

$multipolygon = WKT::load('MULTIPOLYGON(((10 10, 10 20, 20 20, 20 15, 10 10)),((60 60, 70 70, 80 60, 60 60)))');
$t->is(get_class($multipolygon), 'MultiPolygon', 'Loads a multipolygon into a MultiPolygon object');

$geometrycollection = WKT::load('GEOMETRYCOLLECTION(POINT (10 10), POINT(30 30), LINESTRING(15 15, 20 20))');
$t->is(get_class($geometrycollection), 'GeometryCollection', 'Loads a geometrycollection into a GeometryCollection object');


$t->is(WKT::dump($point), 'POINT(10 11)', 'Dumps a Point');

$t->is(WKT::dump($multipoint), 'MULTIPOINT(10 10,20 20)', 'Dumps a MultiPoint');

$t->is(WKT::dump($linestring), 'LINESTRING(10 10,20 20,30 40)', 'Dumps a LineString');

$t->is(WKT::dump($multilinestring), 'MULTILINESTRING((10 10,20 20,30 40),(15 16,21 22,35 45))', 'Dumps a MultiLineString');

$t->is(WKT::dump($polygon), 'POLYGON((10 10,10 20,20 20,20 15,10 10))', 'Dumps a Polygon');

$t->is(WKT::dump($polygon2), 'POLYGON((10 10,10 20,20 20,20 15,10 10),(10 11,10 21,20 21,20 15,10 11))', 'Dumps a Polygon');

$t->is(WKT::dump($geometrycollection), 'GEOMETRYCOLLECTION(POINT(10 10),POINT(30 30),LINESTRING(15 15,20 20))', 'Dumps a GeometryCollection');

class Invalid extends Geometry { public function getCoordinates(){return 1 ;}};

$null = new invalid();
$t->is(WKT::dump($null), null, 'Unknown geometry are dumped to null');







