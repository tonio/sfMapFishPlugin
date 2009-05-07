<?php

/**
 * Doctrine adapter to load Doctrine_Collection or Doctrine_Record
 *  in FeatureCollection or Feature
 */
class GeoJSON_Doctrine_Adapter implements GeoJSON_Adapter
{

  /**
   * Returns object properties
   *
   * @param Doctrine_Record $object
   *
   * @return array
   */
  public function isMultiple($object)
  {
    return (get_class($object)==='Doctrine_Collection');
  }

  /**
   * Returns an iterable object of features
   *
   * @param Doctrine_Collection $object
   *
   * @return Doctrine_Collection
   */
  public function getIterable($object)
  {
    return $object;
  }

  /**
   * Returns object geometry
   *
   * @param Doctrine_Record $object
   *
   * @return string The geometry in WKT
   */
  public function getObjectGeometry($object)
  {
    $geometry = Doctrine::getTable(get_class($object))->getGeometryColumnName();
    return $object->$geometry;
  }

  /**
   * Returns object bounding box
   *
   * @param Doctrine_Record $object
   *
   * @return string The bounding box as an array
   */
  public function getObjectBBox($object)
  {
    if (is_null($object->bbox))
    {
      return null;
    }

    $matches = array();
    preg_match_all('/(\d+)(?:\.\d+)?/', $object->bbox, $matches);
    return $matches[0];
  }

  /**
   * Returns object id
   *
   * @param Doctrine_Record $object
   *
   * @return mixed
   */
  public function getObjectId($object)
  {
    $id = Doctrine::getTable(get_class($object))->getIdentifier();
    return $object->$id;
  }

  /**
   * Returns object properties
   *
   * @param Doctrine_Record $object
   *
   * @return array
   */
  public function getObjectProperties($object)
  {
    $array = $object->toArray();
    $t = Doctrine::getTable(get_class($object));
    unset(
      $array[$t->getGeometryColumnName()],
      $array[$t->getIdentifier()],
      $array['bbox']
    );

    return $array;
  }

}
