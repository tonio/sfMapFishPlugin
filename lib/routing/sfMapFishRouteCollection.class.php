<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfMapFishRouteCollection represents a collection of routes bound to Doctrine objects.
 *
 * @package    symfony
 * @subpackage mapfish
 * @author     Camptocamp <info@camptocamp.com>
 * @version    SVN: $Id: sfDoctrineRouteCollection.class.php 11475 2008-09-12 11:07:23Z fabien $
 */
class sfMapFishRouteCollection extends sfObjectRouteCollection
{
  protected
    $routeClass = 'sfMapFishRoute';
}
