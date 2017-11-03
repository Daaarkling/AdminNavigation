<?php
declare(strict_types=1);

namespace Kollarovic\Navigation;


class PanelControl extends BaseControl
{
	
	private $default = [
		'root' => null,
		'ajax' => false,
	];


	public function render(array $options = [])
	{
		$options += $this->default;
		$this->extractOptions($options);
		$item = $this->getItemByOptions($options);
		$this->template->items = $this->itemsInPanel($item->getItems());
		$this->template->render();
	}


	/**
	 * @param Item[] $items
	 * @return array
	 */
	private function itemsInPanel(array $items): array
	{
		$itemsInPanel = [];
		foreach($items as $item) {
			if ($item->getLink() === '#') {
				$itemsInPanel = array_merge($itemsInPanel, $this->itemsInPanel($item->getItems()));
			} elseif(!$item->isCurrent() and $item->isActive()) {
				$itemsInPanel[] = $item;
			}
		}
		return $itemsInPanel;
	}

}