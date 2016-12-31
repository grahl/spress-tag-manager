<?php


use Yosymfony\Spress\Core\IO\IOInterface;
use Yosymfony\Spress\Core\Plugin\Event\ContentEvent;
use Yosymfony\Spress\Core\Plugin\Event\EnvironmentEvent;
use Yosymfony\Spress\Core\Plugin\EventSubscriber;
use Yosymfony\Spress\Core\Spress;
use Yosymfony\Spress\Plugin\CommandDefinition;
use Yosymfony\Spress\Plugin\CommandPlugin;

class GrahlSpressTagManager extends CommandPlugin {

  private static $tags = [];


  public function initialize(EventSubscriber $subscriber) {
    $subscriber->addEventListener('spress.before_convert', 'beforeConvert');
  }

  /**
   * Gets the command's definition.
   *
   * @return \Yosymfony\Spress\Plugin\CommandDefinition Definition of the command.
   */
  public function getCommandDefinition() {
    $definition = new CommandDefinition('tags:list');
    $definition->setDescription('List all tags in use');
    $definition->setHelp('No additional help provided.');

    return $definition;
  }

  /**
   * Executes the current command.
   *
   * @param \Yosymfony\Spress\Core\IO\IOInterface $io Input/output interface.
   * @param array $arguments Arguments passed to the command.
   * @param array $options Options passed to the command.
   *
   * @return null|int null or 0 if everything went fine, or an error code.
   */
  public function executeCommand(IOInterface $io, array $arguments, array $options) {
    $environment = $this->getCommandEnvironment();

    $environment->runCommand('site:build', []);

    $list = array_count_values(self::$tags);
    uksort($list, 'strcasecmp');
    foreach ($list as $tag => $count) {
      $io->write($tag  . ' (' . $count . ')');
    }
  }

  public function beforeConvert(ContentEvent $event) {
    $item = $event->getItem();
    if (isset($item->getAttributes()['tags'])) {
      foreach ($item->getAttributes()['tags'] as $tag) {
        self::$tags[] = $tag;
      }
    }
  }

    /**
     * Gets the metas of a plugin.
     * 
     * Standard metas:
     *   - name: (string) The name of plugin.
     *   - description: (string) A short description of the plugin.
     *   - author: (string) The author of the plugin.
     *   - license: (string) The license of the plugin.
     * 
     * @return array
     */
    public function getMetas()
    {
        return [
            'name' => 'grahl/spress-tag-manager',
            'description' => 'Provides command line functions to interact with tags.',
            'author' => 'Hendrik Grahl',
            'license' => 'MIT',
        ];
    }
}
