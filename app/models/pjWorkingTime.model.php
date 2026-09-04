<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjWorkingTimeModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'working_times';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'monday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'monday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'monday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'tuesday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'tuesday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'tuesday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'wednesday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'wednesday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'wednesday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'thursday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'thursday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'thursday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'friday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'friday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'friday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'saturday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'saturday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'saturday_dayoff', 'type' => 'enum', 'default' => 'F'),
		array('name' => 'sunday_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'sunday_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'sunday_dayoff', 'type' => 'enum', 'default' => 'F')
	);
	
	public static function factory($attr=array())
	{
		return new pjWorkingTimeModel($attr);
	}
	
	public function getDaysOff($id)
	{
		$_arr = array();
		$arr = $this->find($id)->getData();
		foreach ($arr as $k => $v)
		{
			if (is_null($v) && (strpos($k, "_from") !== false || strpos($k, "_to") !== false))
			{
				list($key) = explode("_", $k);
				$_arr[$key] = 1;
			}
		}
		return $_arr;
	}
	
	public function getWorkingTime($id, $date)
	{
		$day = strtolower(date("l", strtotime($date)));
		$arr = $this->find($id)->getData();

		if (count($arr) == 0)
		{
			return false;
		}
	
		if ($arr[$day . '_dayoff'] == 'T')
		{
			return array();
		}
		
		$wt = array();
		foreach ($arr as $k => $v)
		{
			if (strpos($k, $day . '_from') !== false && !is_null($v))
			{
				$d = getdate(strtotime($v));
				$wt['start_hour'] = $d['hours'];
				$wt['start_minutes'] = $d['minutes'];
				$wt['start_ts'] = strtotime($date . " " . $v);
				continue;
			}
		
			if (strpos($k, $day . '_to') !== false && !is_null($v))
			{
				$d = getdate(strtotime($v));
				$wt['end_hour'] = $d['hours'];
				$wt['end_minutes'] = $d['minutes'];
				$wt['end_ts'] = strtotime($date . " " . $v);
				continue;
			}
		}
		return $wt;
	}
	
	public function checkDateOff($date)
	{
		$is_date_off = false;
		$pjDateModel = pjDateModel::factory();
		$pjWorkingTimeModel = pjWorkingTimeModel::factory();
		$custom_date = $pjDateModel->reset()->getWorkingTime($date);
		
		if($custom_date !== false)
		{
			if(empty($custom_date))
			{
				$is_date_off = true;
			}
		}else{
			$wt_arr = $pjWorkingTimeModel->reset()->getWorkingTime(1, $date);
			if(empty($wt_arr))
			{
				$is_date_off = true;
			}
		}
		return $is_date_off;
	}
	public function getWTime($date)
	{
		$wt_arr = array();
		$pjDateModel = pjDateModel::factory();
		$pjWorkingTimeModel = pjWorkingTimeModel::factory();
		$custom_date = $pjDateModel->reset()->getWorkingTime($date);
	
		if($custom_date !== false)
		{
			if(!empty($custom_date))
			{
				$wt_arr = $custom_date;
			}
		}else{
			$_wt_arr = $pjWorkingTimeModel->reset()->getWorkingTime(1, $date);
			if(!empty($_wt_arr))
			{
				$wt_arr = $_wt_arr;
			}
		}
		return $wt_arr;
	}
}
?>