<?php echo '
  /**
   * Returns GeoJSON detail for one '.$this->getModelClass().'
   *
   * @param sfWebRequest $request
   */
';?>
  public function executeShow(sfWebRequest $request)
  {
    $<?php echo $this->getSingularName() ?> = Doctrine::getTable('<?php echo $this->getModelClass() ?>')->find(<?php echo $this->getRetrieveByPkParamsForAction(49) ?>);
    $this->forward404Unless($this-><?php echo $this->getSingularName() ?>);
    
    return $this->renderText(GeoJSON::encode($<?php echo $this->getSingularName() ?>, '<?php echo $this->getModelClass() ?>'), true);
  }
