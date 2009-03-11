<?php echo '
  /**
   * Returns GeoJSON detail for one '.$this->getModelClass().'
   *
   * @param sfWebRequest $request
   */
';?>
  public function executeShow(sfWebRequest $request)
  {
    $this->forward404Unless($<?php echo $this->getSingularName() ?> = Doctrine::getTable('<?php echo $this->getModelClass() ?>')->searchByProtocol($request));

    $feature = GeoJSON::loadFrom($<?php echo $this->getSingularName() ?>, new GeoJSON_Doctrine_Adapter());

    return $this->renderText(GeoJSON::dump($feature));
  }
