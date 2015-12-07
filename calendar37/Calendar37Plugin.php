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
		return '0.10.15339';
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
		return 'http://calendar37.blogspot.com/';
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

	public function getMySetting() {
		$plugin = craft()->plugins->getPlugin('Calendar37');
		$settings = $plugin->getSettings();
		return $settings->mySetting;
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