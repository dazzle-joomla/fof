<?php
/**
 * @package     FOF
 * @copyright   Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 3 or later
 */

/**
 * @package     FOF
 * @copyright   Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF40\Factory\Scaffolding\Layout;

use FOF40\Model\DataModel;

/**
 * Interface ErectorInterface
 * @package FOF40\Factory\Scaffolding\Layout
 * @deprecated 3.1  Support for XML forms will be removed in FOF 4
 */
interface ErectorInterface
{
	/**
	 * Construct the erector object
	 *
	 * @param   \FOF40\Factory\Scaffolding\Layout\Builder  $parent    The parent builder
	 * @param   \FOF40\Model\DataModel              $model     The model we're erecting a scaffold against
	 * @param   string                              $viewName  The view name for this model
	 */
	public function __construct(Builder $parent, DataModel $model, $viewName);

	/**
	 * Erects a scaffold. It then uses the parent's setXml and setStrings to assign the erected scaffold and the
	 * additional language strings to the parent which will decide what to do with that.
	 *
	 * @return  void
	 */
	public function build();
}
