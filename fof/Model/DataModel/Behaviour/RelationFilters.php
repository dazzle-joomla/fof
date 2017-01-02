<?php
/**
 * @package     FOF
 * @copyright   2010-2017 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license     GNU GPL version 2 or later
 */

namespace FOF30\Model\DataModel\Behaviour;

use FOF30\Event\Observer;
use FOF30\Model\DataModel;
use JRegistry;

defined('_JEXEC') or die;

class RelationFilters extends Observer
{
	/**
	 * This event runs after we have built the query used to fetch a record list in a model. It is used to apply
	 * automatic query filters based on model relations.
	 *
	 * @param   DataModel  &$model  The model which calls this event
	 * @param   \JDatabaseQuery      &$query  The query we are manipulating
	 *
	 * @return  void
	 */
	public function onAfterBuildQuery(&$model, &$query)
	{
		$relationFilters = $model->getRelationFilters();

		foreach ($relationFilters as $filterState)
		{
			$relationName = $filterState['relation'];

			$tableAlias = $model->getBehaviorParam('tableAlias', null);
			$subQuery = $model->getRelations()->getCountSubquery($relationName, $tableAlias);

			// Callback method needs different handling
			if (isset($filterState['method']) && ($filterState['method'] == 'callback'))
			{
				call_user_func_array($filterState['value'], array(&$subQuery));
				$filterState['method'] = 'search';
				$filterState['operator'] = '>=';
				$filterState['value'] = '1';
			}

			$options = new JRegistry($filterState);

			$filter = new DataModel\Filter\Relation($model->getDbo(), $relationName, $subQuery);
			$methods = $filter->getSearchMethods();
			$method = $options->get('method', $filter->getDefaultSearchMethod());

			if (!in_array($method, $methods))
			{
				$method = 'exact';
			}

			switch ($method)
			{
				case 'between':
				case 'outside':
					$sql = $filter->$method($options->get('from', null), $options->get('to'));
					break;

				case 'interval':
					$sql = $filter->$method($options->get('value', null), $options->get('interval'));
					break;

				case 'search':
					$sql = $filter->$method($options->get('value', null), $options->get('operator', '='));
					break;

				default:
					$sql = $filter->$method($options->get('value', null));
					break;
			}

			if ($sql)
			{
				$query->where($sql);
			}
		}
	}
} 