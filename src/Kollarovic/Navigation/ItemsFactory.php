<?php
declare(strict_types=1);

namespace Kollarovic\Navigation;

use Nette\InvalidArgumentException;
use Nette\SmartObject;


class ItemsFactory
{
	use SmartObject;

	/** @var array */
	private $config;

	/** @var array */
	private $default = [
		'label' => 'None',
		'link' => null,
		'linkArgs' => [],
		'icon' => null,
		'active' => true,
		'value' => null,
		'items' => null,
		'resource' => null,
		'privilege' => 'default',
		'options' => [],
	];

	/** @var array */
	private $items = [];


	function __construct(array $config)
	{
		$this->config = $config;
	}


	/**
	 * @param string $name
	 * @return Item
	 * @throws InvalidArgumentException
	 */
	public function create($name): Item
	{
		if (isset($this->items[$name])) {
			return $this->items[$name];
		}

		if (!array_key_exists($name, $this->config)) {
			throw new InvalidArgumentException("Navigation '$name' is not defined");
		}

		$config = (array)$this->config[$name] + $this->default;
		$rootItem = new Item($config['label'], $config['link'], $config['icon'], $config['resource'], $config['privilege']);
		$this->addItems($rootItem, $config['items']);

		$this->items[$name] = $rootItem;
		return $rootItem;
	}


	private function addItems(Item $rootItem, $items)
	{
		if (!is_array($items)) {
			return;
		}

		foreach ($items as $name => $data) {
			$data += $this->default;
			$item = $rootItem->addItem($name, $data['label'], $data['link'], $data['icon'], $data['resource'], $data['privilege']);
			$item->setLinkArgs($data['linkArgs']);
			$item->setActive($data['active']);
			$item->setValue($data['value']);
			$item->setOptions($data['options']);
			$this->addItems($item, $data['items']);
		}
	}
		
}