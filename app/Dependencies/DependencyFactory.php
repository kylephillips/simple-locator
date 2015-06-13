<?php 

namespace SimpleLocator\Dependencies;

use SimpleLocator\Dependencies\AdminDependencies;
use SimpleLocator\Dependencies\PublicDependencies;

/**
* Build up the necessary Dependencies
*/
class DependencyFactory 
{

	public function __construct()
	{
		$this->build();
	}

	/**
	* Build Dependencies
	*/
	public function build()
	{
		new AdminDependencies;
		new PublicDependencies;
	}

}