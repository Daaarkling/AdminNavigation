<?php
declare(strict_types=1);

namespace Kollarovic\Navigation;


class TitleControl extends BaseControl
{
	
	private $default = [
		'root' => null,
	];


	public function render(array $options = [])
	{
		$options += $this->default;
		$this->extractOptions($options);
		$item = $this->getItemByOptions($options);
		$this->template->item = $item->getCurrentItem();
		$this->template->render();
	}

}