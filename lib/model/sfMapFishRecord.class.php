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
  public function updateGeometry($geometry, $epsg=null)
  {
    $t = Doctrine::getTable($this->_table);
    
    list($db_col, $db_epsg) = $t->getGeometryColumn();
    $epsg = (is_null($epsg))?$db_epsg:$epsg;
    
    $t->createQuery('a')
      ->update($col, 'GEOMETRYFROMTEXT(?, ?)', array($geometry, $epsg))
      ->execute();
  }
  
}
