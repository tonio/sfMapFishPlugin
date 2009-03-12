<?php

class sfMapFishActions extends sfActions
{

  public function renderJSON($JSON, $statusCode)
  {
    $r = $this->getResponse();
    $r->clearHttpHeaders();
    $r->setStatusCode($statusCode);
    $r->setContentType('application/json');
    
    return $this->renderText($JSON);
  }

}
