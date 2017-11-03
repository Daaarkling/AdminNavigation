<?php
declare(strict_types=1);

namespace Kollarovic\Navigation;

use Nette\InvalidArgumentException;
use Nette\SmartObject;
use Nette\Utils\Validators;


class Item implements \ArrayAccess
{
	use SmartObject;

	/** @var string */
	private $name;

	/** @var string */
	private $label;

	/** @var string */
	private $link;

	/** @var mixed */
	private $linkArgs = [];

	/** @var string */
	private $icon;

	/** @var string */
	private $resource;
	
	/** @var string */
	private $privilege;

	/** @var string */
	private $value;

	/** @var boolean */
	private $active = true;

	/** @var boolean */
	private $current = false;

	/** @var array */
	private $items = [];

	/** @var array */
	private $options = [];



	public function __construct(string $label, string $link, string $icon = null, string $resource = null, string $privilege = 'default')
	{
		$this->label = $label;
		$this->icon = $icon;
		$this->resource = $resource;
		$this->link = $link ? $link : '#';
		$this->privilege = $privilege;
	}


	public function addItem(string $name, string $label, string $link, string $icon = null, string $resource = null, string $privilege = 'default'): Item
	{
		$item = new Item($label, $link, $icon, $resource, $privilege);
		return $this[$name] = $item;
	}


	/**
	 * @param bool $deep
	 * @return Item[]
	 */
	public function getItems($deep = false): array
	{
		$items = array_values($this->items);
		if ($deep) {
			foreach($this->items as $item) {
				$items = array_merge($items, $item->getItems(true));
			}
		}
		return $items;
	}
	
	
	/**
	 * @return Item[]
	 */
	public function getItemsActive(): array
	{
		$items = array_values($this->items);

		return array_filter($items, function ($item){
				return $item->isActive();
			});
	}
	


	/**
	 * @param string $name
	 * @return Item
	 */
	public function getItem($name): Item
	{
		if (!isset($this->items[$name])) {
			throw new InvalidArgumentException("Item with name '$name' does not exist.");
		}
		return $this->items[$name];
	}


	/**
	 * @return Item|null
	 */
	public function getCurrentItem(): ?Item
	{
		if ($this->isCurrent()) {
			return $this;
		}
		foreach($this->getItems(TRUE) as $item) {
			if ($item->isCurrent()) {
				return $item;
			}
		}
		return null;
	}


	public function isOpen(): bool
	{
		if ($this->isCurrent()) {
			return true;
		}
		foreach ($this->getItems() as $item) {
			if ($item->isCurrent() || $item->isOpen()) {
				return true;
			}
		}
		return false;
	}


	public function isDropdown(): bool
	{
		foreach ($this->getItems() as $item) {
			if ($item->isActive()) {
				return true;
			}
		}
		return false;
	}


	public function isUrl(): bool
	{
		return (Validators::isUrl($this->link) or preg_match('~^/[^/]~', $this->link) or $this->link[0] == '#');
	}


	/**
	 * @return Item[]
	 */
	public function getPath(): array
	{
		$items = [];
		foreach ($this->getItems(TRUE) as $item) {
			if ($item->isCurrent() or $item->isOpen()) {
				$items[$item->link] = $item;
			}
		}
		if ($items) {
			$items = [$this->link => $this] + $items;
		}
		return $items;
	}


	public function getValue()
	{
		return is_callable($this->value) ? call_user_func_array($this->value, [$this]) : $this->value;
	}


	/**
	 * @param string $name
	 * @param mixed $value
	 * @return self
	 */
	public function setOption(string $name, $value): Item
	{
		$this->options[$name] = $value;
		return $this;
	}


	/**
	 * @param string $name
	 * @param mixed|null $default
	 * @return mixed
	 */
	public function getOption(string $name, $default = null)
	{
		return isset($this->options[$name]) ? $this->options[$name] : $default;
	}


	/**
	 * @param string $name
	 * @return self
	 * @throws InvalidArgumentException
	 */
	public function setName(string $name): Item
	{
		if (!preg_match('~^[a-zA-Z0-9_]+~', $name)) {
			throw new InvalidArgumentException("Name must be non-empty alphanumeric string, '$name' given.");
		}
		$this->name = $name;
		return $this;
	}


	public function __toString()
	{
		return (string)$this->label;
	}


	public function offsetExists($offset)
	{
		return isset($this->items[$offset]);
	}


	public function offsetGet($name)
	{
		$item = $this;
		foreach (explode('-', $name) as $key) {
			$item = $item->getItem($key);
		}
		return $item;
	}


	public function offsetSet($name, $item)
	{
		if (!$item instanceof Item) {
			throw new InvalidArgumentException(sprintf('Value must be %s, %s given.', get_called_class(), gettype($item)));
		}
		$item->setName($name);
		$this->items[$name] = $item;
	}


	public function offsetUnset($offset)
	{
		unset($this->items[$offset]);
	}

	/**
	 * @return string
	 */
	public function getLabel(): string
	{
		return $this->label;
	}

	/**
	 * @param string $label
	 */
	public function setLabel(string $label)
	{
		$this->label = $label;
	}

	/**
	 * @return string
	 */
	public function getLink(): string
	{
		return $this->link;
	}

	/**
	 * @param string $link
	 */
	public function setLink(string $link)
	{
		$this->link = $link;
	}

	/**
	 * @return mixed
	 */
	public function getLinkArgs()
	{
		return $this->linkArgs;
	}

	/**
	 * @param mixed $linkArgs
	 */
	public function setLinkArgs($linkArgs)
	{
		$this->linkArgs = $linkArgs;
	}

	/**
	 * @return string
	 */
	public function getIcon(): ?string
	{
		return $this->icon;
	}

	/**
	 * @param string $icon
	 */
	public function setIcon(string $icon = null)
	{
		$this->icon = $icon;
	}

	/**
	 * @return string
	 */
	public function getResource(): ?string
	{
		return $this->resource;
	}

	/**
	 * @param string $resource
	 */
	public function setResource(string $resource = null)
	{
		$this->resource = $resource;
	}

	/**
	 * @return string
	 */
	public function getPrivilege(): ?string
	{
		return $this->privilege;
	}

	/**
	 * @param string $privilege
	 */
	public function setPrivilege(string $privilege = null)
	{
		$this->privilege = $privilege;
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		return $this->active;
	}

	/**
	 * @param bool $active
	 */
	public function setActive(bool $active)
	{
		$this->active = $active;
	}

	/**
	 * @return bool
	 */
	public function isCurrent(): bool
	{
		return $this->current;
	}

	/**
	 * @param bool $current
	 */
	public function setCurrent(bool $current)
	{
		$this->current = $current;
	}

	/**
	 * @return array
	 */
	public function getOptions(): array
	{
		return $this->options;
	}

	/**
	 * @param array $options
	 */
	public function setOptions(array $options)
	{
		$this->options = $options;
	}

	/**
	 * @param mixed $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}
}
