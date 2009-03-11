<?php echo '
  /**
   * Saves '.$this->getModelClass().' and its geometry
   *
   * @param sfWebRequest $request
   * @param sfForm $form
   *
   * @return Doctrine_Record
   */
';?>
  protected function processForm(Feature $feature, sfForm $form)
  {
    $c = Doctrine_Manager::getInstance()->getCurrentConnection();
    $c->beginTransaction();

    if (!$form->bindAndSave($feature->getProperties()) || !$form->getObject()->updateGeometry($feature->getGeometry()))
    {
      $c->rollback();
      return false;
    }
    
    return GeoJSON::loadFrom($form->getObject(), new GeoJSON_Doctrine_Adapter);
  }
