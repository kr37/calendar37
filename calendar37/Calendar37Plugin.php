<?php
namespace Craft;

class Calendar37Plugin extends BasePlugin
{
	function getName()
	{
		 return Craft::t('Calendar37');
	}

	function getVersion()
	{
		return '0.11.15339';
	}
	
	function getSchemaVersion()
	{
    return '0.01.15338.2';
	}

	function getDeveloper()
	{
		return 'KR37';
	}

	function getDeveloperUrl()
	{
		return 'https://github.com/kr37/calendar37';
	}


	/* Settings */
	
	protected function defineSettings() {
		return array(
			'mySetting' => array(AttributeType::String, 'required' => true),
			'cpCssFile' => array(AttributeType::String, 'required' => false),
			'occurrenceFormat' => array(AttributeType::String, 'required' => true),
			'availableTimes' => array(AttributeType::String, 'required' => true),
			'status' => array(AttributeType::String, 'required' => true),
			'categoriesToExclude' => array(AttributeType::String, 'required' => false),
			'categoriesToInclude' => array(AttributeType::String, 'required' => false),
			'title' => array(AttributeType::String, 'required' => false),
			'showTails' => array(AttributeType::String, 'required' => false),
			'filler1' => array(AttributeType::String, 'required' => false),
			'filler2' => array(AttributeType::String, 'required' => false),
			'rowOfDaysFormat' => array(AttributeType::String, 'required' => false),
			'nodate' => array(AttributeType::String, 'required' => false),
			'dateformat' => array(AttributeType::String, 'required' => false),
			'dateformat1st' => array(AttributeType::String, 'required' => true),

			'categoryFieldHandle' => array(AttributeType::String, 'required' => true),
			'entryCalendarTextFieldHandle' => array(AttributeType::String, 'required' => true),
			'imageFieldHandle' => array(AttributeType::String, 'required' => false),
			'cssFieldHandle' => array(AttributeType::String, 'required' => true),
			'startDateFieldHandle' => array(AttributeType::String, 'required' => true),
			'urlFieldHandle' => array(AttributeType::String, 'required' => false),

			'timezone' => array(AttributeType::String, 'required' => false)
		);
	}	

	public function getSettingsHtml() {
	   return craft()->templates->render('Calendar37/_settings', array(
		   'settings' => $this->getSettings()
	   ));
   }
 
	public function prepSettings($settings) {
		$settings['status'] = $settings['status'] ?: 'live pending expired';
		$settings['showTails'] = $settings['showTails'] != 'no' ? 'yes' : 'no';
		$settings['dateformat'] = $settings['dateformat'] ?: 'D j';
		$settings['dateformat1st'] = $settings['dateformat1st'] ?: 'D, F j';
		$settings['occurrenceFormat'] = $settings['occurrenceFormat'] ?: '%s %s';
		return $settings;
	}

	public function onAfterInstall() {
		savePluginSettings($this, $this->getDefaultSettings());
	}

	public function getDefaultSettings() {
		return array (
			'cpCssFile' => '',
			'occurrenceFormat' => '%s %s',
			'availableTimes' => '<option value="">Choose the time...</option><option value="-1">All day/no time - top spot</option><option value="-2">All day/no time - 2nd spot</option><option value="-3">All day/no time - 3rd spot</option><option value="25">All day/no time - 3rd from bottom</option><option value="26">All day/no time - 2nd from bottom</option><option value="27">All day/no time - bottom spot</option><option value="06:30:00">6:30 am</option><option value="07:00:00">7:00 am</option><option value="07:30:00">7:30 am</option><option value="08:00:00">8:00 am</option><option value="08:30:00">8:30 am</option><option value="09:00:00">9:00 am</option><option value="09:30:00">9:30 am</option><option value="10:00:00">10:00 am</option><option value="10:30:00">10:30 am</option><option value="11:00:00">11:00 am</option><option value="11:30:00">11:30 am</option><option value="12:00:00">12:00 pm</option><option value="12:30:00">12:30 pm</option><option value="13:00:00">1:00 pm</option><option value="13:30:00">1:30 pm</option><option value="14:00:00">2:00 pm</option><option value="14:30:00">2:30 pm</option><option value="15:00:00">3:00 pm</option><option value="15:30:00">3:30 pm</option><option value="16:00:00">4:00 pm</option><option value="16:30:00">4:30 pm</option><option value="17:00:00">5:00 pm</option><option value="17:30:00">5:30 pm</option><option value="18:00:00">6:00 pm</option><option value="18:30:00">6:30 pm</option><option value="19:00:00">7:00 pm</option><option value="19:30:00">7:30 pm</option><option value="20:00:00">8:00 pm</option><option value="20:30:00">8:30 pm</option><option value="21:00:00">9:00 pm</option><option value="21:30:00">9:30 pm</option><option value="22:00:00">10:00 pm</option><option value="22:30:00">10:30 pm</option><option value="23:00:00">11:00 pm</option><option value="23:30:00">11:30 pm</option><option value="00:00:00">12:00 am</option><option value="00:30:00">12:30 am</option><option value="01:00:00">1:00 am</option><option value="01:30:00">1:30 am</option><option value="02:00:00">2:00 am</option><option value="02:30:00">2:30 am</option><option value="03:00:00">3:00 am</option><option value="03:30:00">3:30 am</option><option value="04:00:00">4:00 am</option><option value="04:30:00">4:30 am</option><option value="05:00:00">5:00 am</option><option value="05:30:00">5:30 am</option><option value="06:00:00">6:00 am</option>',
			'status' => 'live pending expired',
			'categoriesToExclude' => '',
			'categoriesToInclude' => '',
			'title' => '',
			'showTails' => 'yes',
			'filler1' => '',
			'filler2' => '',
			'rowOfDaysFormat' => 'l',
			'nodate' => 'no',
			'dateformat' => 'D j',
			'dateformat1st' => 'D, F j',
			'timezone' => '',

			'categoryFieldHandle' => '',
			'entryCalendarTextFieldHandle' => '',
			'imageFieldHandle' => '',
			'cssFieldHandle' => '',
			'startDateFieldHandle' => 'posted',
			'urlFieldHandle' => ''
		);
	}


	

	public function full($params = null) {
		if (is_array($params))
			$calendar = Calendar37_CalendarModel::populateModel($params);
		elseif (is_null($params)) 
			$calendar = new Calendar37_CalendarModel;
		else $calendar = $params;	
		return calendar_full($calendar);
	}		

	/* Calupdate */
 	public function hasCpSection() {
	// Tell the Craft CP we want our own section
		return true;
	}


}