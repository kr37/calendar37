<?php
namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName
 */
class m151204_074257_calendar37_LongFillerText2 extends BaseMigration
{
	/**
	 * Any migration code in here is wrapped inside of a transaction.
	 *
	 * @return bool
	 */
	public function safeUp()
	{
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
