<?php

class sfMapFishRecord extends sfDoctrineRecord
{

  /**
   * List of properties to export (all by default)
   *
   */
  private $__exportedProperties = null;

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

  /**
   * Sets properties which will be exported with toArray method
   *
   * @param array $fields An array of keys
   */
  public function setExportedProperties($fields)
  {
    $this->__exportedProperties = $fields;
  }

  /**
   * Overrides toArray
   *
   * @param boolean $deep
   * @param string $prefixkey
   *
   * @return array The filtered array
   */
  public function toArray($deep = true, $prefixKey = false)
  {
    $original = parent::toArray($deep, $prefixKey);
    if (is_null($this->__exportedProperties))
    {
      return $original;
    }

    $filtered = array();
    foreach ($original as $key => $value)
    {
      if (in_array($key, $this->__exportedProperties))
      {
        $filtered[$key] = $value;
      }
    }
    return $filtered;
  }

}
