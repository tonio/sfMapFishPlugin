<?php

/**
 * sfMapFishPlugin configuration.
 *
 * @package     sfMapFishPlugin
 * @subpackage  config
 * @author      Camptocamp <info@camptocamp.com>
 * @version     SVN: $Id: PluginConfiguration.class.php 12675 2008-11-06 08:07:42Z Kris.Wallsmith $
 */
class sfMapFishPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    $this->dispatcher->connect(
      'request.method_not_found',
     array('sfMapFishRequest', 'listenToMethodNotFound')
    );
    $this->configureDoctrine();
  }

  /**
   * Change the base class name for records
   */
  public function configureDoctrine()
  {
    $options = array(
      'baseClassName' => 'sfMapFishRecord' ,
#      'baseTableName' => 'sfMapFishTable' # as soon as patch for #1976 pass ( http://trac.doctrine-project.org/ticket/1976 )
    );
    sfConfig::set('doctrine_model_builder_options', $options);
  }

}

class sfMapFishRequest
{

  static public function listenToMethodNotFound(sfEvent $event)
  {
    /**
     * retrieve raw post data
     */
    if ($event['method']==='getRawBody')
    {
      $event->setProcessed(true);
      $event->setReturnValue(file_get_contents('php://input'));
    }

    /**
     * remove parameter from request parameterHolder
     */
    if ($event['method']==='removeParameter')
    {
      $ph = $event->getSubject()->getParameterHolder();
      foreach ($event['arguments'] as $parameter)
      {
        $ph->remove($parameter);
      }
      $event->setProcessed(true);
    }
  }

}
