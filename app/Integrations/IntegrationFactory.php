<?php

namespace SimpleLocator\Integrations;

use SimpleLocator\Integrations\ACF\AdvancedCustomFields;

class IntegrationFactory
{
	public function __construct()
	{
		$this->build();
	}

	/**
	* Build up the Integrations
	*/
	private function build()
	{
		new AdvancedCustomFields;
	}
}