<?php
declare(strict_types=1);

namespace Kollarovic\Navigation;


class MenuControl extends BaseControl
{
	
	private $default = [
		'root' => null,
		'class' => 'nav', 
		'subclass' => "nav",
		'activeClass' => "active",
		'openClass' => "open",
		'dropdownClass' => "dropdown dropdown-fw",
		'open' => false,
		'deep' => true,
		'ajax' => false,
	];


	public function render(array $options = [])
	{
		$options += $this->default;
		$this->extractOptions($options);
		$item = $this->getItemByOptions($options);		
		$this->template->items = $item->getItems();
		$this->template->render();
	}

}