<?php echo '
  /**
   * Returns GeoJSON list of '.$this->getModelClass().'
   *
   * @param sfWebRequest $request
   */
';?>
  public function executeIndex(sfWebRequest $request)
  {
    $features = GeoJSON::loadFrom($this->getRoute()->getObjects(), new GeoJSON_Doctrine_Adapter);

    return $this->renderText(GeoJSON::dump($features));
  }
