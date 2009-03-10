<?php

class GeoJSON
{
  private static $__parser = array(
    'gdal' => 'gdalDecode'
  );
  
  
  /**
   * Encode passed array into GeoJSON string
   *
   * @param array $items A list of items to encode
   * @param string $model The model of data to encode
   *
   * @return string The GeoJSON string
   */
  public static function encode(array $items, $model=false)
  {
    $encoder = new Services_GeoJson();
    if ($model)
    {
      $table = Doctrine::getTable($model);
      $geometryColumn = $table->getGeometryColumnName();
      $idColumn = $table->getIdentifier();
    }
    else
    {
      $geometryColumn = 'the_geom';
      $idColumn = 'id';
    }

    return $encoder->encode($items, $geometryColumn, $idColumn);
  }
  
  /**
   * Decode passed GeoJSON string in a array representing a feature,
   *   or in an array of ones
   *
   * @param string $geoJSON A GeoJSON 
   *
   * @return array
   */
  static public function decode($geoJSON, $method='gdal')
  {
    return call_user_func(array('self',self::$__parser[$method]), $geoJSON);
  }
  
  /**
   * Decode GeoJSON string using GDAL ogr2ogr command
   *
   * @param string $geoJSON
   *
   * @return array
   */
  static public function gdalDecode($string)
  {
    # Iniatialize temp dir & file
    $dest = sfConfig::get('sf_cache_dir').'/'.uniqid();
    $source = $dest.'.json';
    file_put_contents($source, $string);
    
    $ogr = '/usr/local/bin/ogr2ogr';

    $output = `$ogr -f CSV $dest $source -lco GEOMETRY=AS_WKT`;

    if (!is_null($output) || !is_readable($dest.'/OGRGeoJSON.csv'))
    {
      throw new sfException('Error while decoding GeoJSON');
    }
    
    $tab = fopen($dest.'/OGRGeoJSON.csv', 'r');
    
    while ($line = fgets($tab))
    {
      if (substr($line, 0, 3)!=='WKT')
      {
        $line = substr($line, 1);
        $pos = strpos($line, '"');
        $geom = substr($line, 0, $pos);
        $line = substr($line, $pos+2);
        $lines[] = array_merge(array(0=>$geom), explode(',', $line));
      } else {
        $lines[] = explode(',', $line);
      }
    }
    $desc = array_shift($lines);

    $features = array();
    foreach ($lines as $line)
    {
      $feature = array();
      foreach ($line as $key => $value)
      {
        $feature[$desc[$key]] = $value;
      }
      $features[] = $feature;
    }
    
    return $features;
  }

}
