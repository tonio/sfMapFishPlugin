<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Generates a Doctrine module.
 *
 * @package    symfony
 * @subpackage mapfish
 * @author     Camptocamp <info@camptocamp.com>
 */
class sfMapFishGenerateModuleTask extends sfDoctrineBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('application', sfCommandArgument::REQUIRED, 'The application name'),
      new sfCommandArgument('module', sfCommandArgument::REQUIRED, 'The module name'),
      new sfCommandArgument('model', sfCommandArgument::REQUIRED, 'The model class name'),
    ));

    $this->addOptions(array(
      new sfCommandOption('theme', null, sfCommandOption::PARAMETER_REQUIRED, 'The theme name', 'default'),
      new sfCommandOption('generate-in-cache', null, sfCommandOption::PARAMETER_NONE, 'Generate the module in cache'),
      new sfCommandOption('non-verbose-templates', null, sfCommandOption::PARAMETER_NONE, 'Generate non verbose templates'),
      new sfCommandOption('with-show', null, sfCommandOption::PARAMETER_NONE, 'Generate a show method'),
      new sfCommandOption('singular', null, sfCommandOption::PARAMETER_REQUIRED, 'The singular name', null),
      new sfCommandOption('plural', null, sfCommandOption::PARAMETER_REQUIRED, 'The plural name', null),
      new sfCommandOption('route-prefix', null, sfCommandOption::PARAMETER_REQUIRED, 'The route prefix', null),
      new sfCommandOption('with-doctrine-route', null, sfCommandOption::PARAMETER_NONE, 'Whether you will use a Doctrine route'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    ));

    $this->aliases = array('mapfish-generate-crud', 'mapfish:generate-crud');
    $this->namespace = 'mapfish';
    $this->name = 'generate-module';
    $this->briefDescription = 'Generates a MapFish module';

    $this->detailedDescription = <<<EOF
The [mapfish:generate-module|INFO] task generates a Doctrine module:

  [./symfony mapfish:generate-module frontend article Article|INFO]

The task creates a [%module%|COMMENT] module in the [%application%|COMMENT] application
for the model class [%model%|COMMENT].

You can also create an empty module that inherits its actions and templates from
a runtime generated module in [%sf_app_cache_dir%/modules/auto%module%|COMMENT] by
using the [--generate-in-cache|COMMENT] option:

  [./symfony doctrine:generate-module --generate-in-cache frontend article Article|INFO]

The generator can use a customized theme by using the [--theme|COMMENT] option:

  [./symfony doctrine:generate-module --theme="custom" frontend article Article|INFO]

This way, you can create your very own module generator with your own conventions.
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);
    
    $properties = parse_ini_file(sfConfig::get('sf_config_dir').'/properties.ini', true);

    $this->constants = array(
      'PROJECT_NAME'   => isset($properties['symfony']['name']) ? $properties['symfony']['name'] : 'symfony',
      'APP_NAME'       => $arguments['application'],
      'MODULE_NAME'    => $arguments['module'],
      'UC_MODULE_NAME' => ucfirst($arguments['module']),
      'MODEL_CLASS'    => $arguments['model'],
      'AUTHOR_NAME'    => isset($properties['symfony']['author']) ? $properties['symfony']['author'] : 'Your name here',
    );

    $this->executeGenerate($arguments, $options);
  }

  protected function executeGenerate($arguments = array(), $options = array())
  {
    // generate module
    $tmpDir = sfConfig::get('sf_cache_dir').'/'.'tmp'.'/'.md5(uniqid(rand(), true));
    $generatorManager = new sfGeneratorManager($this->configuration, $tmpDir);
    $generatorManager->generate('sfMapFishGenerator', array(
      'model_class'           => $arguments['model'],
      'moduleName'            => $arguments['module'],
      'theme'                 => $options['theme'],
      'non_verbose_templates' => $options['non-verbose-templates'],
      'singular'              => $options['singular'],
      'plural'                => $options['plural'],
      'route_prefix'          => $options['route-prefix'],
      'with_doctrine_route'     => $options['with-doctrine-route'],
    ));

    $moduleDir = sfConfig::get('sf_app_module_dir').'/'.$arguments['module'];

    // copy our generated module
    $this->getFilesystem()->mirror($tmpDir.'/'.'auto'.ucfirst($arguments['module']), $moduleDir, sfFinder::type('any'));

    // change module name
    $finder = sfFinder::type('file')->name('*.php');
    $this->getFilesystem()->replaceTokens($finder->in($moduleDir), '', '', array('auto'.ucfirst($arguments['module']) => $arguments['module']));

    // customize php and yml files
    $finder = sfFinder::type('file')->name('*.php', '*.yml');
    $this->getFilesystem()->replaceTokens($finder->in($moduleDir), '##', '##', $this->constants);

    // create basic test
    $this->getFilesystem()->copy(sfConfig::get('sf_symfony_lib_dir').'/task/generator/skeleton/module/test/actionsTest.php', sfConfig::get('sf_test_dir').'/functional/'.$arguments['application'].'/'.$arguments['module'].'ActionsTest.php');

    // customize test file
    $this->getFilesystem()->replaceTokens(sfConfig::get('sf_test_dir').'/functional/'.$arguments['application'].'/'.$arguments['module'].'ActionsTest.php', '##', '##', $this->constants);

    // delete temp files
    $this->getFilesystem()->remove(sfFinder::type('any')->in($tmpDir));
  }

}
