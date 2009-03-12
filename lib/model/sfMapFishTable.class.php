<?php

class sfMapFishTable extends Doctrine_Table 
{

  /**
   * searchByProtocol
   *   returns a collection of records, according to MapFish Protocol
   *
   * @param sfWebRequest $request
   * @param mfQuery $query
   *
   * @return Doctrine_Collection
   */
  public function searchByProtocol(sfWebRequest $request, mfQuery $query=null)
  {
    if (is_null($query))
    {
      $query = $this->createMfQuery()->select('*');
    }

    if ($request->hasParameter('lon') && $request->hasParameter('lat'))
    {
      $query->hasPoint(
        $request->getParameter('lon'),
        $request->getParameter('lat'),
        $request->getParameter('epsg', null),
        (int) $request->getParameter('tolerance', 0)
      );
    }
    else if ($request->hasParameter('box'))
    {
      $query->inBbox(
        explode(',', $request->hasParameter('box')),
        $request->getParameter('epsg', null),
        (int) $request->getParameter('tolerance', 0)
      );
    }

    if ($request->hasParameter('id'))
    {
      $query->addWhere($this->getIdentifier().'=?', $request->getParameter('id'));
    }

    if ($request->hasParameter('maxfeatures'))
    {
      $query->limit((int) $request->getParameter('maxfeatures'));
    }

    return $query;
  }

  /**
   * countByProtocol
   *   returns the number of records, according to MapFish Protocol
   *
   * @param sfWebRequest $request
   *
   * @return integer
   */
  public function countByProtocol(sfWebRequest $request)
  {
    return $this->searchByProtocol($request, $this->createMfQuery())->count();
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
    return mfQuery::create($this->getGeometryColumnName(), $this->getGeometryColumnEPSG())
      ->from($this->getComponentName() . $alias);
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
    list($name, $epsg) = $this->getGeometryColumn();
    return $epsg;
  }
}
