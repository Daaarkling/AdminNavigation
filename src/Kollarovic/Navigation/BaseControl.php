<?php
declare(strict_types=1);

namespace Kollarovic\Navigation;

use Nette\Application\UI\Control;
use Nette\Application\UI\Presenter;



abstract class BaseControl extends Control
{

	/** @var Item */
	private $rootItem;
	
	/** @var array */
	private $layout;


	function __construct(Item $rootItem, array $layout)
	{
		parent::__construct();
		$this->rootItem = $rootItem;
		$this->layout = $layout;
	}


	protected function createTemplate()
	{
		$template = parent::createTemplate();
		if (!array_key_exists('translate', $template->getLatte()->getFilters())) {
			$template->addFilter('translate', function($str){return $str;});
		}
		$file = __DIR__ . "/templates/{$this->layout['templates']['navigationDir']}/" . (new \ReflectionClass($this))->getShortName() . ".latte";
		$template->setFile($file);
		return $template;
	}


	public abstract function render(array $options = []);


	protected function getItemByOptions(array $options): Item
	{
		$item = $this->rootItem;
		if ($options['root']) {
			$item = $item[$options['root']];
		}
		return $item;
	}


	protected function extractOptions(array $options): void
	{
		foreach ($options as $key => $value) {
			$this->template->$key = $value;
		}
	}


	protected function attached($presenter)
	{
		parent::attached($presenter);
		if ($presenter instanceof Presenter) {
			foreach($this->rootItem->getItems(true) as $item) {
				!$item->isUrl() and $item->setCurrent($presenter->isLinkCurrent($item->getLink(), $item->getLinkArgs()));
			}
		}
	}

	/**
	 * @return Item
	 */
	public function getRootItem(): Item
	{
		return $this->rootItem;
	}

	/**
	 * @param Item $rootItem
	 */
	public function setRootItem(Item $rootItem)
	{
		$this->rootItem = $rootItem;
	}

	/**
	 * @return array
	 */
	public function getLayout(): array
	{
		return $this->layout;
	}

	/**
	 * @param array $layout
	 */
	public function setLayout(array $layout)
	{
		$this->layout = $layout;
	}
}