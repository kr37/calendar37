<?php
namespace Craft;

class Calendar37_ViewsRecord extends BaseRecord
{
	public function getTableName() {
		return 'calendar37_views';
	}

	protected function defineAttributes() { 
		return array(
			// Craft automatically creates 'id' as an autoincrement
			'subsetId'     => AttributeType::String,
			'startDateYmd' => AttributeType::String,
			'endDateYmd'   => AttributeType::String,
			'htmlBefore'   => array(AttributeType::String, 'column' => ColumnType::Text, 'required' => false),
			'htmlAfter'    => array(AttributeType::String, 'column' => ColumnType::Text, 'required' => false),
		);
	}
	
	public function defineIndexes() {
		return array(
			array('columns' => array('subsetId', 'startDateYmd' ), 'key' => true),
		);
	}
	

}