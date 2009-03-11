<?php

class sfMapFishRecord extends sfDoctrineRecord 
{

  /**
   * return current record geometry column
   *
   * @return string
   *
   */
  public function getGeometryColumn()
  {
    $t = Doctrine::getTable($this->_table)->getGeometryColumn();
  }

  /**
   * update current record geometry 
   *
   * @param string $geometry
   * @param int $epsg
   */
  public function updateGeometry(Geometry $geometry, $epsg=null)
  {
    try
    {
      $t = $this->getTable();
      
      list($db_col, $db_epsg) = $t->getGeometryColumn();
      $epsg = (is_null($epsg))?$db_epsg:$epsg;

      $geometry = WKT::dump($geometry);

      $t->createQuery('a')
        ->update()
        ->set($db_col, 'GEOMETRYFROMTEXT(?, ?)', array($geometry, $epsg))
        ->where($t->getIdentifier().' = ?', $this->getPrimaryKey())
        ->execute();
        
      return true;
    }
    catch (Exception $e)
    {
      return false;
    }
  }
  
}
