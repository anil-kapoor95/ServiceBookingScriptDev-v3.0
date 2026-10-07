<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjBookingExtraModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'bookings_extras';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'booking_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'extra_id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'title', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'price', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'duration', 'type' => 'int', 'default' => '0')
	);
	
	public static function factory($attr=array())
	{
		return new pjBookingExtraModel($attr);
	}
}
?>