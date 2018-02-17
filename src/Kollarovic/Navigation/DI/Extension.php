<?php
declare(strict_types=1);

namespace Kollarovic\Navigation\DI;

use Kollarovic\Navigation\IItemsFactory;
use Nette\DI\CompilerExtension;


class Extension extends CompilerExtension
{
	private function getDefaultConfig()
	{
		return [
			'itemsFactory' => 'Kollarovic\Navigation\ItemsFactory',
		];
	}


	public function loadConfiguration()
	{
		$config = $this->getConfig($this->getDefaultConfig());

		if (!is_subclass_of($config['itemsFactory'],IItemsFactory::class)) {
			throw new InvalidConfigException('Class ' . $config['itemsFactory'] . ' in ' . $this->prefix('itemsFactory') . ' must implement ' . IItemsFactory::class . ' interface.');
		}

		$builder = $this->getContainerBuilder();
		$builder->addDefinition($this->prefix('itemsFactory'))
			->setFactory($config['itemsFactory'], [$config]);
	}
}

class InvalidConfigException extends \Exception {}