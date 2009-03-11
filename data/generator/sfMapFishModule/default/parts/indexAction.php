<?php echo '
  /**
   * Returns GeoJSON list of '.$this->getModelClass().'
   *
   * @param sfWebRequest $request
   */
';?>
  public function executeIndex(sfWebRequest $request)
  {
    $<?php echo $this->getPluralName() ?> = Doctrine::getTable('<?php echo $this->getModelClass() ?>')->searchByProtocol($request);

    $features = GeoJSON::loadFrom($<?php echo $this->getPluralName() ?>, new GeoJSON_Doctrine_Adapter());

    return $this->renderText(GeoJSON::dump($features));
  }
