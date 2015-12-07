<?php
namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName
 */
class m151130_054002_calendar37_Subsets extends BaseMigration
{
	/**
	 * Any migration code in here is wrapped inside of a transaction.
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		
		// Create the craft_calendar37_Subsets table
		craft()->db->createCommand()->createTable('calendar37_Subsets', array(
				'handle'              => array(),
				'title'               => array(),
				'categoriesToInclude' => array(),
				'categoriesToExclude' => array(),
		), null, true);

		// Add indexes to craft_calendar37_Subsets
		craft()->db->createCommand()->createIndex('calendar37_Subsets', 'handle', true);


		// Create the craft_calendar37_Views table
		craft()->db->createCommand()->createTable('calendar37_Views', array(
				'subsetId'     => array(),
				'startDateYmd' => array(),
				'endDateYmd'   => array(),
				'htmlBefore'   => array(),
				'htmlAfter'    => array(),
		), null, true);

		// Add indexes to craft_calendar37_Views
		craft()->db->createCommand()->createIndex('calendar37_Views', 'subsetId,startDateYmd', false);



		return true;
	}
}
