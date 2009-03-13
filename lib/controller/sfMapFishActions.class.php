<?php

class sfMapFishActions extends sfActions
{

  /**
   * Returns JSON response with correct content-type and passed status code
   */
  public function renderJSON($JSON, $statusCode)
  {
    $r = $this->getResponse();
    $r->clearHttpHeaders();
    $r->setStatusCode($statusCode);
    $r->setContentType('application/json');
    
    return $this->renderText($JSON);
  }

  /**
   * Return HTTP code 204 (No-content)
   */
  public function forward204()
  {
    $r = $this->getResponse();
    $r->clearHttpHeaders();
    $r->setStatusCode(204);
    
    return sfView::NONE;
  }

}
