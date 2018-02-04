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
		new AdminDependencies;
		new PublicDependencies;
	}
}