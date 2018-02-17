<?php
declare(strict_types=1);

namespace Kollarovic\Navigation;

use Nette\InvalidArgumentException;


interface IItemsFactory
{
	/**
	 * @param string $name
	 * @return Item
	 * @throws InvalidArgumentException
	 */
	public function create($name): Item;
}