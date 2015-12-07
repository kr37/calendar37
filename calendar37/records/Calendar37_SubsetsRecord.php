<?php
namespace Craft;

class Calendar37_SubsetsRecord extends BaseRecord
{
	public function getTableName() {
		return 'calendar37_Subsets';
	}

	protected function defineAttributes() { 
		return array(
			// Craft automatically creates 'id' as an autoincrement
			'handle'              => AttributeType::String,
			'title'               => AttributeType::String,
			'categoriesToInclude' => AttributeType::String,
			'categoriesToExclude' => AttributeType::String,
			);
	}
	
	public function defineIndexes() {
		return array(
			array('columns' => array('handle'), 'unique' => true),
		);
	}
	

}