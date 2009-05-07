<?php

interface GeoJSON_Adapter
{

  public function isMultiple($object);

  public function getIterable($object);

  public function getObjectGeometry($object);

  public function getObjectId($object);

  public function getObjectProperties($object);

}
