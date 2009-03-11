<?php echo '
  /**
   * Update a '.$this->getModelClass().' from passed feature
   *
   * @param sfWebRequest $request
   */
';?>
  public function executeUpdate(sfWebRequest $request)
  {
    $this->form = new <?php echo $this->getModelClass().'Form' ?>($this->getRoute()->getObject());

    if ($feature = $this->processForm(GeoJSON::load($request->getRawBody()), $this->form))
    {
      $statusCode = 201;
      $response = GeoJSON::dump($feature);
    }
    else
    {
      $statusCode = 500;
      $response = 'You die.';
    }
    
    $this->getResponse()->setStatusCode($statusCode);
    return $this->renderText($response);
  }
