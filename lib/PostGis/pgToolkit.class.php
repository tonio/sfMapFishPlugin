<?php

/**
 * PostGis toolkit 
 *
 * @package    MapFish
 * @subpackage PostGis
 * @author     Camptocamp <info@camptocamp.com>
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class pgToolkit
{

  /**
   * Bufferise a geometry
   *
   * @param string $wkt A geometry as WKT
   * @param integer $buffer The buffer size
   *
   * @return string The resulting geomtry, binary formatted
   */
  static public function buffer($wkt, $buffer)
  {
    $query = '
      SELECT
        ENCODE(ASBINARY(BUFFER(GEOMETRYFROMTEXT(?), ?)), \'hex\') AS the_geom
    ';
    
    $con = Doctrine_Manager::getInstance()->getCurrentConnection();
    $s = $con->prepare($query);
    $s->execute(array($wkt, $buffer));

    $lines = $s->fetchAll(PDO::FETCH_ASSOC);

    return $lines[0]['the_geom'];
  }
  
}
