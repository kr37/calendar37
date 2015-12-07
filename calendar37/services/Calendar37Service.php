<?php
namespace Craft;

class Calendar37_EventModel 
{
	public $title;
	public $css;
	public $eventHandle;
	public $url;
	public $imageAsTitle;
}


class Calendar37Service extends BaseApplicationComponent
{

	private $twigAtts;
	private $settings;
	private $imageFieldHandle;
	
	
	public function initCal($fromDateYmd=NULL, $toDateYmd=NULL, $atts=array()) {
	// Creates a new Calendar37_CalendarModel object, populating all the event occurrences
	// Parameters come from either the calling function (TWIG) or from GET/POST. GET/POST always override.
		$plugin = craft()->plugins->getPlugin('Calendar37');
		$this->settings = $plugin->getSettings();
		$this->twigAtts = $atts;

	
		$cal = new Calendar37_CalendarModel;
		$cal->calupdate = (array_key_exists('calupdate',$atts)) ? $atts['calupdate'] : false;
		
  		$cal->showTails        = $this->getParam('showTails');
		$cal->status           = $this->getParam('status');
		$cal->rowOfDaysFormat  = $this->getParam('rowOfDaysFormat');
		$cal->dateformat       = $this->getParam('dateformat');
		$cal->dateformat1st    = $this->getParam('dateformat1st');
		$cal->occurrenceFormat = $this->getParam('occurrenceFormat');
		$cal->title            = $this->getParam('title');
		$cal->filler1          = $this->getParam('filler1');
		$cal->filler2          = $this->getParam('filler2');
		$cal->nodate           = $this->getParam('nodate');
		$catsToInclude         = $this->getParam('categoriesToInclude');
		$catsToExclude         = $this->getParam('categoriesToExclude');
		$cal->categoriesToInclude = $catsToInclude ? explode(',', $catsToInclude) : null;
		$cal->categoriesToExclude = $catsToExclude ? explode(',', $catsToExclude) : null;

		$cal->desiredStartYmd( craft()->request->getParam('calstart') ?: $fromDateYmd ?: date("Y-m-01",time()) );
		$cal->desiredEndYmd( craft()->request->getParam('calend')?: $toDateYmd ?: date("Y-m-t",$cal->desiredStartNum) );

		$view = new Calendar37_ViewsRecord;
		$view = $view->findByAttributes(array('startDateYmd'=>$cal->desiredStartYmd(), 'endDateYmd'=>$cal->desiredEndYmd()));
		if ($view) {
			$cal->filler1 = $view->htmlBefore;
			$cal->filler2 = $view->htmlAfter;
		}

		// OK, pull all occurrences from the db
		$query = craft()->db->createCommand();
		$cal->occurrence = $query->select('*')
			->from('calendar37 c37')
			->where("c37.dateYmd >= '{$cal->actualStartYmd()}'")
			->andWhere("c37.dateYmd <= '{$cal->actualEndYmd()}'") 
			->order('dateYmd ASC, timestr ASC')
			->queryAll();

		// Filter based on category, 
		// and catalog event and category info for valid events (to save re-querying for repeated events)
		foreach($cal->occurrence as $key => $occurrence) {
			$eventID = $occurrence['event_id'];
			if ( !isset( $eventIsValid[$eventID] ) ) {
				// Collect info about this event. Especially, determine whether or not to display it.
				$entry    = craft()->entries->getEntryById($eventID);
				$category = $entry->mainCategory->last();

				$eventIsValid[$eventID] = 'no'; //We'll change it to yes if it turns out to be good.
				if ( empty($cal->categoriesToInclude) || in_array($category->id, $cal->categoriesToInclude) ) {
					if ( empty($cal->categoriesToExclude) || !in_array($category->id, $cal->categoriesToExclude) ) {
						$eventIsValid[$eventID] = 'yes'; 
						$event = new Calendar37_EventModel;
						$event->url         = $entry->getUrl();
						$event->css         = (isset($category)) ? $category->cal37Css : '';  
						$event->eventHandle = (isset($category)) ? $category->slug : ''; 
						$event->title       = $entry->{$this->settings->entryCalendarTextFieldHandle} ?: $entry->title;
						$image = $entry->calendarImage->first();
						if ($image) { 
							$event->title   = "<img src='{$image->url}' title='{$event->title}'>";
							$event->imageAsTitle = true;
						}
						$cal->event[$eventID] = $event;
					}
				}
			}
			if ($eventIsValid[$eventID] == 'no') unset($cal->occurrence[$key]);	
		}
		$cal->occurrence = array_values($cal->occurrence);
		
		// How will we create URLs?
		$cal->urlFieldHandle = $this->settings->categoryFieldHandle;
		return $cal;
	}

	private function getParam($param) {
		//Returns the first value of param from: GET/POST, $atts (from TWIG), or plugin $settings 
		return craft()->request->getParam($param) 
			?: (array_key_exists($param,$this->twigAtts) ? $this->twigAtts[$param] : $this->settings[$param]);
	}

	public function calendar_full($cal) {
	// This is the main function for displaying a calendar

		// Display title with month and year, such as Meditate in Fort Collins ~ December 2006-----
		$br = $cal->title ? '<br>' : '';
		$out = "<h2 class='cal37_mainheader'>{$cal->title}$br{$cal->dateHeader()}</h2>\n";

		// Display links to previous and next month-----
		// Figure out the URLs
		$host = $_SERVER['HTTP_HOST'];
		$uri = $_SERVER['REQUEST_URI'];
		$total_url = "http://$host$uri";
		$argv = array_key_exists('argv', $_SERVER) ? $_SERVER['argv'] : '';
		$segments = craft()->request->segments;

		$querystart = strpos($total_url,"?");
		$out .= "<!-- segments: ".print_r($segments,true)."\ntotal_url: $total_url\nquerystart: $querystart\n";
		if (!$querystart) {
			$querystart = strlen($total_url);
		}
		if ($querystart) {
			$base_url = substr($total_url,0,$querystart);
			$queries = substr($total_url,$querystart+1);
			$fn = $base_url . "?";
			$out .= "base_url: $base_url\nqueries: $queries\nfn: $fn\n";
		} else {
			$fn = $total_url . "?";
		}
		$out .= "-->\n\n";
	
		// Display PREV & NEXT
		$prevnum = strtotime("-1 month",$cal->desiredStartNum);
		$nextnum = strtotime("+1 day",$cal->desiredEndNum);  //was last day of the month, now first of the next month
		$out .= "<div class='cal37_nextprev'>\n"
		      . "	<a href='{$fn}calstart=".date("Y-m-01",$prevnum)."' >".date("F",$prevnum)."</a>\n"
		      . "	".date("F", $cal->desiredStartNum)." \n"
		      . "	<a href='{$fn}calstart=".date("Y-m-01",$nextnum)."' >".date("F",$nextnum)."</a>\n"
		      . "	<br>\n"
		      . "</div>\n"
			  . "<input type='hidden' name='desiredStartYmd' value='{$cal->desiredStartYmd()}'>\n"
			  . "<input type='hidden' name='desiredEndYmd' value='{$cal->desiredEndYmd()}'>\n";

		// Output the calendar-----
		$next_date_to_display = $this->thisSunday($cal->desiredStartNum);
		$out .= "<div id='cal37'>\n\n"
			. "	<table class='cal37'>\n";
	
		//Output a top row of days of the week, if desired
		if ($cal->rowOfDaysFormat!='') {	 
			$out .= "		<tr>\n";
			//BUG ALERT: These constants for the length of a day or week need to be changed 
			//because on daylight savings days, the length of the day is different.
			for ($i=$next_date_to_display; $i<$next_date_to_display+7*86400; $i+=86400) {
				$out .= "			<th>".date($cal->rowOfDaysFormat,$i)."</td>\n";
			}
			$out .= "		</tr>\n";
		}
		//Output the remaining calendar
		while ($next_date_to_display <= $cal->actualEndNum) {
			$next_date_to_display=$this->lineofevents($cal, $next_date_to_display, $out);
		}
		$out .= "	</table>\n</div><!--cal37-->\n";
	
		return $out;
	}

	public function thisMonth($timestmp) {
	//Returns the unix timestamp for the first day of the month that $timestmp falls in
	  $dayofmonth = date("d",$timestmp) - 1;  
	  return strtotime("-".$dayofmonth." day", $timestmp);
	}
	
	public function thisSunday($timestmp) {
	//Returns the unix timestamp for Noon on Sunday of the week that $timestmp falls in (same time of day as timestamp)
	//There is still some daylight savings time glitch here maybe?
	  $daynum = date("w", $timestmp);
	  return strtotime("-".$daynum." day", $timestmp);
	}

	function lineofevents($cal, $datenum, &$out) {
	//Produces a week's worth of lists of daily events and increment $datenum
	
		$out .= "		<tr>\n";
		$i = 0;
		while ($i<7) {
			if ($cal->showTails=='yes' || ($datenum >= $cal->desiredStartNum && $datenum <= $cal->desiredEndNum)) {
				// Display a regular day of the month
				$out .= "			<td>\n";
				$out .= $this->events1day_all($cal, $datenum, 'cal37Tail');
				$out .= "			</td>\n";
				$datenum = strtotime('+1 day', $datenum);
				$i++; 
			} else {
				// Display the blank boxes for the start or end of the month
				if ($datenum < $cal->desiredStartNum):
					$filler = $cal->filler1;
					$class = 'cal37_tail1';
					$days = ($cal->desiredStartNum - $datenum)/86400;
				else:
					$filler = $cal->filler2;
					$class = 'cal37_tail2';
					$days = 7 - date('w', $datenum);
				endif;
				$out .= "			<td colspan='$days' class='cal37_calendar $class cal37_colspan$days'>$filler</td>\n";
				$datenum = strtotime("+$days day", $datenum);
				$i += $days; 
			}
		}
		$out .= "		</tr>\n";
		return ($datenum);
	}
	
	
	function events1day_all($cal, $datenum, $otherCss) {
	//Produces a list of all the occurrences on this day
		static $monthCss;
		
		$date = date("Y-m-d",$datenum);
		$dayofmonth = date("j",$datenum);
		if ($dayofmonth=='1') {
			$monthCss =  date("F ",$datenum) . ((date("m",$datenum) & 1) ? 'month-odd' : 'month-even');  
			$newMonthCss = 'cal37-newmonth';
			$pDate = date($cal->dateformat1st, $datenum);
		} else {
			$newMonthCss = '';
			$pDate = date($cal->dateformat, $datenum); 
		}			 
	
		// Ouput the date info
		$out = '';
		if ($cal->nodate != 'yes') {
			$out .= "				<p class='date $newMonthCss $monthCss $otherCss'>";
			if ('' != $cal->calupdate) $out .= "<input type='checkbox' name='add$date'>";
			$out .= "$pDate</p>\n";
		}
	
		// Output the occurrences
		$out .= "				<ul class='cal37 cal37_calendar $otherCss' >\n";
		$siteUrl = craft()->getSiteUrl();
		while ( isset( $cal->occurrence[0] )  &&  $cal->occurrence[0]['dateYmd'] <= $date ) {
			$minCount = 1;
	
			//Take the first event off of $cal->occurrence and use it, shortening the array
			$row      = array_shift($cal->occurrence);  
			$entryID  = $row['event_id'];
			$event    = $cal->event[$entryID];
			$class    = $row['css_class'] ?: $event->css;
			$time     = $this->nicetime($row['timestr']);
			if ($row['alt_text']) {
				$program  = $row['alt_text'];
			} else {
				$program  = $event->title;
				if ($event->imageAsTitle) $time='';
			}
			if ($cal->calupdate) {
				$program .= " {$entryID}";
				$updateCheckbox = "<input type='checkbox' name='del{$row['id']}' >\n						";
			} else 
				$updateCheckbox = '';

			$out .= <<<ONEOCCURRENCE
					<li data-instance_id='{$row['id']}' data-event_id='$entryID' data-category='{$event->eventHandle}' class='$class {$row['timestr']} $cal->tailStyle'>        
						$updateCheckbox<a href='{$event->url}'>
ONEOCCURRENCE;
			$out .= sprintf($cal->occurrenceFormat, $time, $program) . "</a>\n					</li>\n";
		} //while
		$out     .= "				</ul>\n";
		if (!isset($minCount)) 
			$out .= "				<br>\n";  //if there were no events, still put a blank line.
		return $out;	
	} //function events1day_all


	private function nicetime($time0) {
	//$time0 is a time string like 14:00:00
	//Returns null if $time0 is less than 0 or greater than 24.
	//Returns time with AM/PM, and if possible without minutes:  10AM, 4PM, 2:45PM, etc.
		if ($time0 < 0 or $time0 > 24) $time = null;
		else {
			$date = '';
			$timestamp=strtotime($date." ".$time0);
			$minutes=date("i",$timestamp);
			if ($minutes>0) $time = date("g:ia",$timestamp);
			else $time=date("ga",$timestamp); 
		}
		return $time;
	}


	public function possibleEvents($cal) {
		// Get list of current events to choose from
		$plugin = craft()->plugins->getPlugin('Calendar37');
		$this->settings = $plugin->getSettings();
		$criteria = craft()->elements->getCriteria(ElementType::Entry);
		$criteria->section = 'events';
		$criteria->startDate = array("<={$cal->actualEndYmd()}", NULL);
		$criteria->expiryDate = array(">={$cal->actualStartYmd()}", NULL);
		$criteria->enabled = '1';
		$criteria->order = $this->settings->entryCalendarTextFieldHandle . ' ASC';
		return $criteria->find();
	}	

	public function calUpdateEventsOptions(Calendar37_CalendarModel $cal) {
		// Put the possible events into a <SELECT><OPTION>
		$events = $this->possibleEvents($cal);
		$eventsOptions = '';
		foreach ($events as $row) {
			$name = $row->{$this->settings->entryCalendarTextFieldHandle} ?: $row->title ?: "Entry ID: {$row->id}";
			$eventsOptions .= "<OPTION VALUE='{$row->id}'>$name &nbsp; | &nbsp; {$row->mainCategory->last()->title}";
			if ($row->expiryDate > '0000-00-00') {
				$eventsOptions .= " &nbsp; | &nbsp; " . substr($row->startDate,0,10) . " - " . substr($row->expiryDate,0,10);
			}
			$eventsOptions .= "</OPTION>\n					";
		}
		return $eventsOptions;
	}

	
	public function calUpdateTimesOptions() {
		// get <OPTIONS> for times
		$settings = craft()->plugins->getPlugin('calendar37')->getSettings();
		return $settings['availableTimes'];
	}


	public function htmlBefore($startYmd = null, $endYmd = null, $subsetId = null) {
		$view = new Calendar37_ViewsRecord;
		$view = $view->findByAttributes(array('startDateYmd'=>$startYmd, 'endDateYmd'=>$endYmd));
		return ($view) ? $view->htmlBefore : '';
	}
	
	public function htmlAfter($startYmd = null, $endYmd = null, $subsetId = null) {
		$view = new Calendar37_ViewsRecord;
		$view = $view->findByAttributes(array('startDateYmd'=>$startYmd, 'endDateYmd'=>$endYmd));
		return ($view) ? $view->htmlAfter : '';
	}
	
	
	
	public function calupdate__NOTUSED() {
		$cal = $this->initCal(null, null, array('calupdate' => true));	
		$token = craft()->request->getCsrfToken();
 		
		// Put the possible events into a <SELECT><OPTION>
		$events = $this->possibleEvents($cal);
		$eventsOptions = '';
		foreach ($events as $row) {
			$id = $row->id;
			$name = $row->{$this->settings->entryCalendarTextFieldHandle} ?: $row->title ?: "Entry ID: $id";
			$eventsOptions .= "<OPTION VALUE='$id'>$name &nbsp; | &nbsp; {$row->mainCategory->last()->title}";
			if ($row->expiryDate > '0000-00-00') {
				$eventsOptions .= " &nbsp; | &nbsp; " . substr($row->startDate,0,10) . " - " . substr($row->expiryDate,0,10);
			}
			$eventsOptions .= "</OPTION>\n					";
		}

		// get <OPTIONS> for times
		$settings = craft()->plugins->getPlugin('calendar37')->getSettings();
		$timesOptions = $settings['availableTimes'];

		// OK, output the whole page.
		$out = <<<WHOLECALUPDATE
	<p><strong>Instructions:</strong></p>
	<ul>
		<li>To add events, put a check by the date of each cell. </li>
		<li>To delete events, put checks in front of the particular event instances that need to be deleted.</li>
	</ul>
	<p>Then press update.  You can add and delete at the same time.</p>

	<form method="post" action="" accept-charset="UTF-8">
		<input type="hidden" name="action" value="calendar37/calupdate/AddAndDeleteInstances">
		<input type='hidden' name="CRAFT_CSRF_TOKEN" value='$token'>
		<div id="calupdatetable">
			<fieldset>
				<SELECT NAME="post_id">
					<OPTION VALUE="">Choose a Program...</option>
					$eventsOptions
				</SELECT>
		
				<SELECT name="time1">
				$timesOptions
				</SELECT>
	
				<br><label for="AltText">Enter alternate text, if any:</label>
				<input id="alt_text" type="text" size="30" name="alt_text">
				<input type="submit" value="update">
			</fieldset>
		</div>

		{$this->calendar_full($cal)}
	</form>
WHOLECALUPDATE;

		return $out;
	}

	
}