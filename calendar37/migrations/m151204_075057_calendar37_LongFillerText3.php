<?php
namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName
 */
class m151204_075057_calendar37_LongFillerText3 extends BaseMigration
{
	/**
	 * Any migration code in here is wrapped inside of a transaction.
	 *
	 * @return bool
	 */
	public function safeUp()
	{
	craft()->db->createCommand()->alterColumn('calendar37_Views', 'htmlBefore', 'text');
	craft()->db->createCommand()->alterColumn('calendar37_Views', 'htmlAfter', 'text');

		return true;
	}
}
