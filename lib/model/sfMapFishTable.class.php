<?php

class sfMapFishTable extends Doctrine_Table
{

  private $operators = array(
    'eq' => '=',
    'ne' => '!=',
    'lt' => '<',
    'lte' => '<=',
    'gt' => '>',
    'gte' => '>=',
    'like' => 'LIKE',
    'ilike' => 'ILIKE'
  );

  /**
   * filterByProtocol
   *   returns a collection of records, according to MapFish Protocol
   *
   * @param sfWebRequest $request
   * @param mfQuery $query
   *
   * @return Doctrine_Collection
   */
  public function filterByProtocol(sfWebRequest $request, mfQuery $query=null)
  {
    if (is_null($query))
    {
      $query = $this->createMfQuery()->select('*');
    }

    # Spatial Filters
    $this->spatialFilter($request, $query);

    # Attributes filters
    $this->attributesFilter($request, $query);

    # Limits, offsets filters
    if ($request->hasParameter('id'))
    {
      $query->addWhere($this->getIdentifier().'=?', $request->getParameter('id'));
    }

    if ($request->hasParameter('maxfeatures'))
    {
      $query->limit((int) $request->getParameter('maxfeatures'));
    }

    if ($request->hasParameter('limit'))
    {
      $query->limit((int) $request->getParameter('limit'));
    }

    if ($request->hasParameter('offset'))
    {
      $query->offset((int) $request->getParameter('offset'));
    }

    return $query;
  }

  /**
   *
   * @param sfWebRequest $request A Request Object
   * @param mfQuery $query A MapFish Query
   *
   * @return mfQuery The spatial filtered MapFish query
   */
  public function spatialFilter(sfWebRequest $request, mfQuery $query)
  {
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
        explode(',', $request->getParameter('box')),
        $request->getParameter('epsg', null),
        (int) $request->getParameter('tolerance', 0)
      );
    }

    return $query;
  }

  /**
   *
   * @param sfWebRequest $request A Request Object
   * @param mfQuery $query A MapFish Query
   *
   * @return mfQuery The spatial filtered MapFish query
   */
  public function attributesFilter(sfWebRequest $request, mfQuery $query)
  {
    if (!$request->hasParameter('queryable') || strpos($request->getParameter('queryable'), ',')===false)
    {
      return $query;
    }

    foreach (explode(',', $request->getParameter('queryable')) as $field)
    {
      if (!$this->hasColumn($field))
      {
        throw new sfException(sprintf(
          'Unable to filter : unknown attribute "%s" for model "%s"',
          $field,
          $this->name
        ));
      }
      foreach ($request->getGetParameters() as $key => $value)
      {
        if (substr($key, 0, strlen($field))===$field && strpos($key, '__')>-1)
        {
          $query->andWhere(
            $field.' '.$this->operators[substr($key, strlen($field)+2)].' ?',
            $value
          );
        }
      }
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
    return $this->filterByProtocol($request, $this->createMfQuery())->count();
  }

  /**
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
    if (!$this->getGeometryColumn())
    {
      throw new Exception('The class '.$this->getComponentName().' should specify its geometry column.');
    }
    return mfQuery::create($this->getGeometryColumnName(), $this->getGeometryColumnEPSG())
      ->from($this->getComponentName() . $alias);
  }

  /**
   * Find a object with geometry as text
   *
   * @param mixed $id
   *
   * @return sfMapFishRecord
   */
  public function geoFind($id)
  {
    return $this->createMfQuery()
      ->select('*')
      ->where($this->getIdentifier().'=?', $id)
      ->fetchOne();
  }

  /**
   * Returns the geometry column name & epsg, as an array
   *
   * @return array
   */
  public function getGeometryColumn()
  {
    $i = New ReflectionClass($this->_options['name']);
    $statics = $i->getStaticProperties();

    if (!isset($statics['geometryColumn']))
    {
      return false;
    }

    $geom = $statics['geometryColumn'];
    $col = array_flip($geom);

    return array(array_shift($col), array_shift($geom));
  }

  /**
   * Returns the geometry column name
   *
   * @return string
   */
  public function getGeometryColumnName()
  {
    list($name, $epsg) = $this->getGeometryColumn();
    return $name;
  }

  /**
   * Returns the geometry epsg
   *
   * @return integer
   */
  public function getGeometryColumnEPSG()
  {
    list($name, $epsg) = $this->getGeometryColumn();
    return $epsg;
  }
}
