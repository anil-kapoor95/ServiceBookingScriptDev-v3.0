<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjServiceCategoryModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'service_categories';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'status', 'type' => 'enum', 'default' => 'T')
	);
	
	public $i18n = array('title');
	
	public static function factory($attr=array())
	{
		return new pjServiceCategoryModel($attr);
	}
}
?>