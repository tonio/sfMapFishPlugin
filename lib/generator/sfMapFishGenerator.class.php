<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * MapFish generator.
 *
 * @package    symfony
 * @subpackage mapfish
 * @author     Camptocamp <info@camptocamp.com>
 */
class sfMapFishGenerator extends sfDoctrineGenerator
{

  /**
   * Initializes the current sfGenerator instance.
   *
   * @param sfGeneratorManager $generatorManager A sfGeneratorManager instance
   */
  public function initialize(sfGeneratorManager $generatorManager)
  {
    parent::initialize($generatorManager);

    $this->setGeneratorClass('sfMapFishModule');
  }

}
