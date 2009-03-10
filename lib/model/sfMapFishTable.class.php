<?php

class sfMapFishTable extends Doctrine_Table 
{

  /**
   * searchByProtocol
   *   returns a collection of records, according to MapFish Protocol
   *
   * @param sfWebRequest $request
   * @param int $hydrationMode        Doctrine::HYDRATE_ARRAY or Doctrine::HYDRATE_RECORD
   *
   * @return Doctrine_Collection
   */
  public function searchByProtocol(sfWebRequest $request)
  {
    $query = $this->createMfQuery()->select('*');
    
    if ($request->hasParameter('lon') && $request->hasParameter('lat'))
    {
      $query->hasPoint(
        $request->getParameter('lon'),
        $request->getParameter('lat'),
        (int) $request->getParameter('tolerance', 0),
        $request->getParameter('epsg', null)
      );
    }
    else if ($request->hasParameter('box'))
    {
      $query->inBbox(
        explode(',', $request->hasParameter('box')),
        (int) $request->getParameter('tolerance', 0),
        $request->getParameter('epsg', null)
      );
    }
    
    if ($request->hasParameter('maxfeatures'))
    {
      $query->limit((int) $request->getParameter('maxfeatures'));
    }
    
    return $query->execute();
  }
  
  /**
   * createQuery
   * creates a new mfQuery object and adds the component name
   * of this table as the query 'from' part
   *
   * @param string Optional alias name for component aliasing.
   *
   * @return mfQuery
   */
  public function createMfQuery($alias = '')
  {
    if ( ! empty($alias)) {
      $alias = ' ' . trim($alias);
    }
    return mfQuery::create($this->_conn)->from($this->getComponentName() . $alias);
  }

  public function getGeometryColumn()
  {
    $i = New ReflectionClass($this->_options['name']);
    $statics = $i->getStaticProperties();
    $geom = $statics['geometryColumn'];
    $col = array_flip($geom);
    
    return array(array_shift($col), array_shift($geom));
  }
  
  public function getGeometryColumnName()
  {
    list($name, $epsg) = $this->getGeometryColumn();
    return $name;
  }
  
  public function getGeometryColumnEPSG()
  {
    list($name, $epsg) = $this->getGeometryColumnName();
    return $epsg;
  }
}
