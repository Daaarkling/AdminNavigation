<?php
declare(strict_types=1);

namespace Kollarovic\Navigation;

use Nette\Application\UI\Control;


class NavigationControl extends Control
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


	public function renderMenu(array $options = [])
	{
		$this['menu']->render($options);
	}


	public function renderBreadcrumb(array $options = [])
	{
		$this['breadcrumb']->render($options);
	}


	public function renderPanel(array $options = [])
	{
		$this['panel']->render($options);
	}


	public function renderTitle(array $options = [])
	{
		$this['title']->render($options);
	}


	protected function createComponentMenu(): MenuControl
	{
		return new MenuControl($this->rootItem, $this->layout);
	}


	protected function createComponentBreadcrumb(): BreadcrumbControl
	{
		return new BreadcrumbControl($this->rootItem, $this->layout);
	}


	protected function createComponentPanel(): PanelControl
	{
		return new PanelControl($this->rootItem, $this->layout);
	}


	protected function createComponentTitle(): TitleControl
	{
		return new TitleControl($this->rootItem, $this->layout);
	}

}