<?php
namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName
 */
class m151130_071318_calendar37_dropCols extends BaseMigration
{
	/**
	 * Any migration code in here is wrapped inside of a transaction.
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		craft()->db->createCommand()->dropColumn('calendar37', 'note');
		craft()->db->createCommand()->dropColumn('calendar37', 'attendance');
		return true;
	}
}
