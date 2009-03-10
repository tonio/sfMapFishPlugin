<?php

/**
 * Geometric queries helpers
 *
 */
class mfQuery extends Doctrine_Query
{
  /**
   * The name of the geometric column
   *
   * @var string
   */
  private $__geoColumn;
  
  /**
   * The projection of the geometric column
   *
   * @var string
   */
  private $__epsg;
  
  /**
   * Format string for geom column transform in select statement
   *
   * @var array
   */
  private static $__format = array(
    'BINARY' => ", encode(asbinary(%s), 'hex') %s"
  );
  
  /**
   * Create the query & set the geo column
   *
   * @param string $column
   *
   * @return mfQuery 
   */
  public static function create($column='the_geom')
  {
    $instance = new self();
    $instance->__geoColumn = $column;
    
    return $instance;
  }
  
  /**
   * sets the SELECT part of the query, and add geo column according to format
   *
   * @param string $string
   * @param mixed $append false or string : way to transform the geom 
   *
   * @return mfQuery
   */
  public function select($string, $append='BINARY')
  {
    if ($append!==false)
      $string .= sprintf(self::$__format[$append], $this->__geoColumn, $this->__geoColumn);

    return parent::select($string);
  }

  /**
   * Add where clause with geometry contains passed point
   *
   * @param float $lon
   * @param float $lat
   * @param int $epsg
   * @param int $tolerance
   * 
   * @return mfQuery
   */
  public function hasPoint($lon, $lat, $epsg=null, $tolerance=0)
  {
    return $this->intersect("POINT($lon $lat)", $epsg, $tolerance);
  }

  /**
   * Add where clause with geometry intersects passed bbox
   *
   * @param array $box
   * @param int $epsg
   * @param int $tolerance
   *
   * @return mfQuery
   */
  public function inBBox(array $box, $epsg=null, $tolerance=0)
  {
    $box = array_map('floatval', $box);

    $A = $box[0].' '.$box[1];
    $B = $box[0].' '.$box[3];
    $C = $box[2].' '.$box[3];
    $D = $box[2].' '.$box[1];

    return $this->intersect("POLYGON(($A, $B, $C, $D, $A))", $epsg, $tolerance);
  }
  
  /**
   * Add where clause with geometry intersects passed geometry
   *
   * @param string $geometry
   * @param int $epsg
   * @param int $tolerance
   * @param boolean $isWKB If passed geometry is WKB instead of WKT
   *
   * @return mfQuery
   */
  public function intersect($geometry, $epsg=null, $tolerance=0, $isWKB=false)
  {
    $pg_geometry = ($isWKB)?'?':'GEOMETRYFROMTEXT(?, 27572)';
    $the_geom = (is_null($epsg))?
      $this->__geoColumn:
      'TRANSFORM('.$this->__geoColumn.', '.(int) $epsg.')';

    $this
      ->addWhere("$the_geom && $pg_geometry", $geometry)
      ->andWhere("DISTANCE($pg_geometry, $the_geom) <= $tolerance", $geometry);
      
    return $this;
  }
}

