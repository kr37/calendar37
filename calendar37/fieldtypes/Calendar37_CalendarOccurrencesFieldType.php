<?php
namespace Craft;

class Calendar37_CalendarOccurrencesFieldType extends BaseFieldType
{
	public function getName() {
		return Craft::t('Calendar Occurrences');
	}


	public function getInputHtml($name, $value) {

		// Include our Javascript
		craft()->templates->includeJsResource('calendar37/occurrencesField.js');
		$settings = craft()->plugins->getPlugin('calendar37')->getSettings();
		$startDateFieldHandle = craft()->templates->namespaceInputId($settings['startDateFieldHandle']) . '-date';
		$calendarText = craft()->templates->namespaceInputId($settings['entryCalendarTextFieldHandle']);
		$js = "var startDateFieldHandle = '$startDateFieldHandle';\n"
			. "var entryCalendarTextFieldHandle = '$calendarText';";
		craft()->templates->includeJs($js);
		
		// Include CSS
		craft()->templates->includeCssResource('calendar37/occurrencesField.css');

		// Find out how many times this post is already in the calendar (for the button caption)
		$count = craft()->calendar37_calendarOccurrencesField->eventOccurrencesCount($this->element->id); 

		return craft()->templates->render('calendar37/calendaroccurrences/input1', array(
			'name'  => $name,
			'value' => $value,
			'count' => $count,
			'event_times' => craft()->calendar37_mySettings->getAvailableTimes()
		));   
	}


	public function defineContentAttribute() {
		// This field does not store anything in the 'content' database table,
		// because the info is stored in a separate table.
		return false;
	}

	public function prepValue($value) {
		// Modify $value here...

		return $value;
	}

	
}