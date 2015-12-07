<?php
namespace Craft;

class Calendar37Variable
{

	public function getMySetting() {
		return craft()->calendar37_mySettings->getMySetting();
	}
	
	
	public function getCpCssFile() {
		return craft()->calendar37_mySettings->getCpCssFile();
	}
	
	public function getEventClickDestination() {
		return craft()->calendar37_mySettings->getEventClickDestination();
	}

	public function getStyleFromCategory() {
		return craft()->calendar37_mySettings->getStyleFromCategory();
	}

	public function calendar_full($fromDateYmd = null, $toDateYmd = null, $atts = array()) {
		$cal = craft()->calendar37->initCal($fromDateYmd, $toDateYmd, $atts);
		return craft()->calendar37->calendar_full($cal);
	}
	

	// *** CalUpdate stuff ***

	public function calupdate() {
		return craft()->calendar37->calupdate();
	}

	public function events($fromDateYmd = null, $toDateYmd = null) {
		return craft()->calendar37->eventsArray($fromDateYmd, $toDateYmd);
	}
	
	public function calUpdateEventsOptions(Calendar37_CalendarModel $cal) {
		return craft()->calendar37->calUpdateEventsOptions($cal);
	}
	
	public function calUpdateAvailableTimes() {
		return craft()->calendar37_mySettings->getAvailableTimes();
	}
		
	public function calUpdateCalendarFull(Calendar37_CalendarModel $cal) {
		return craft()->calendar37->calendar_full($cal);
	}

	public function htmlBefore($startYmd = null, $endYmd = null, $subsetId = null) {
		return craft()->calendar37->htmlBefore($startYmd, $endYmd, $subsetId);
	}

	public function htmlAfter($startYmd = null, $endYmd = null, $subsetId = null) {
		return craft()->calendar37->htmlAfter($startYmd, $endYmd, $subsetId);
	}

	public function desiredStartYmd(Calendar37_CalendarModel $cal) {
		return $cal->desiredStartYmd();
	}

	public function desiredEndYmd(Calendar37_CalendarModel $cal) {
		return $cal->desiredEndYmd();
	}

	

	// *** Main Calendar Stuff ***

	
	public function init($fromDateYmd = null, $toDateYmd = null, $atts) {
		return craft()->calendar37->initCal($fromDateYmd, $toDateYmd, $atts); 
	}

	public function miniCalInit(&$fromDateYmd = null, &$toDateYmd = null, $atts = null) {
		$fromDateYmd = ($fromDateYmd > 0) ? $fromDateYmd : date("Y-m-d");
		$toDateYmd   = ($toDateYmd > 0)   ? $toDateYmd : date("Y-m-d", strtotime($fromDateYmd." +1day"));
		$atts['showTails'] = false; 
		return craft()->calendar37->initCal($fromDateYmd, $toDateYmd, $atts);
	}

	public function dump($cal) {
		return "<pre>\n" . print_r($cal->occurrence, true) . "\n</pre>";
	}

	public function oneDay($reqDateYmd = null, $atts = null, $cal = null) {
		$plugin = craft()->plugins->getPlugin('Calendar37');
		$settings = $plugin->getSettings();
		date_default_timezone_set($settings->timezone);
		$reqDateYmd = ($reqDateYmd) ?: date('Y-m-d');
		$cal = (isset($cal)) ? $cal : $this->miniCalInit($reqDateYmd, $reqDateYmd, $atts);
		return craft()->calendar37->events1day_all($cal, strtotime($reqDateYmd), 'miniCal');
	} 
	

	// *** Occurrences Field stuff ***

	
	public function miniCal($fromDateYmd = null, $toDateYmd = null) {
		$cal = $this->miniCalInit($fromDateYmd, $toDateYmd);
		$out = '';
		for ($dateNum = strtotime($fromDateYmd); $dateNum <= strtotime($toDateYmd); $dateNum = strtotime("+1 day", $dateNum) ) {
			$out .= craft()->calendar37->events1day_all($cal, $dateNum, 'miniCal');
		}
		return $out;
	}
	
	
}