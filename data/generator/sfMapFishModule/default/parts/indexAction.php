<?php echo '
  /**
   * Returns GeoJSON list of '.$this->getModelClass().'
   *
   * @param sfWebRequest $request
   */
';?>
  public function executeIndex(sfWebRequest $request)
  {
    $<?php echo $this->getPluralName() ?> = mfQuery::create()
      ->select('*')
      ->from('<?php echo $this->getModelClass() ?>')
      ->limit(20)
      ->fetchArray();
  
    return $this->renderText(GeoJSON::encode($<?php echo $this->getPluralName() ?>, '<?php echo $this->getModelClass() ?>'));
  }
