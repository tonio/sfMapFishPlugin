<?php echo '
  /**
   * Deletes a '.$this->getModelClass().' 
   *
   * @param sfWebRequest $request
   */
';?>
  public function executeDelete(sfWebRequest $request)
  {
    $this->getRoute()->getObject()->delete();

    return $this->renderJSON('{"success": true}' ,204);
  }
