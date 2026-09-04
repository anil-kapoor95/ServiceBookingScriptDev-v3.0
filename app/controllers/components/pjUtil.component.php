<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjUtil extends pjToolkit
{
	public static function getReferer()
	{
		if (isset($_GET['_escaped_fragment_']))
		{
			if (isset($_SERVER['REDIRECT_URL']))
			{
				return $_SERVER['REDIRECT_URL'];
			}
		}
		
		if (isset($_SERVER['HTTP_REFERER']))
		{
			$pos = strpos($_SERVER['HTTP_REFERER'], "#");
			if ($pos !== FALSE)
			{
				return substr($_SERVER['HTTP_REFERER'], 0, $pos);
			}
			return $_SERVER['HTTP_REFERER'];
		}
	}
	
	public static function getClientIp()
	{
		if (isset($_SERVER['HTTP_CLIENT_IP']))
		{
			return $_SERVER['HTTP_CLIENT_IP'];
		} else if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else if(isset($_SERVER['HTTP_X_FORWARDED'])) {
			return $_SERVER['HTTP_X_FORWARDED'];
		} else if(isset($_SERVER['HTTP_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_FORWARDED_FOR'];
		} else if(isset($_SERVER['HTTP_FORWARDED'])) {
			return $_SERVER['HTTP_FORWARDED'];
		} else if(isset($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		}

		return 'UNKNOWN';
	}
	
	public static function textToHtml($content)
	{
		$content = preg_replace('/\r\n|\n/', '<br />', $content);
		return '<html><head><title></title></head><body>'.$content.'</body></html>';
	}
	public static function toMomemtJS($format)
	{
		$f = str_replace(
				array('Y', 'm', 'n', 'd', 'j'),
				array('yyyy', 'mm', 'm', 'dd', 'd'),
				$format
		);
	
		return $f;
	}
	static public function sortArrayByArray(Array $array, Array $orderArray) 
	{
		$ordered = array();
		foreach($orderArray as $key)
		{
			if(array_key_exists($key,$array))
			{
				$ordered[$key] = $array[$key];
				unset($array[$key]);
			}
		}
		return $ordered + $array;
	}
	
	static public function getPostMaxSize()
	{
		$post_max_size = ini_get('post_max_size');
		switch (substr($post_max_size, -1))
		{
			case 'G':
				$post_max_size = (int) $post_max_size * 1024 * 1024 * 1024;
				break;
			case 'M':
				$post_max_size = (int) $post_max_size * 1024 * 1024;
				break;
			case 'K':
				$post_max_size = (int) $post_max_size * 1024;
				break;
		}
		return $post_max_size;
	}
	
	public static function convertDateTime($date_time, $date_format, $time_format)
	{
		if(count(explode(" ", $date_time)) == 3)
		{
			list($_date, $_time, $_period) = explode(" ", $date_time);
			$iso_time = pjUtil::formatTime($_time . ' ' . $_period, $time_format);
		}else{
			list($_date, $_time) = explode(" ", $date_time);
			$iso_time = pjUtil::formatTime($_time, $time_format);
		}
		$iso_date = pjUtil::formatDate($_date, $date_format);
		$iso_date_time = $iso_date . ' ' . $iso_time;
		$ts = strtotime($iso_date_time);
	
		return compact('iso_date', 'iso_time', 'iso_date_time', 'ts');
	}
	
	public static function truncateDescription($string, $limit, $break=".", $pad="...")
	{
		if(strlen($string) <= $limit)
			return $string;
		if(false !== ($breakpoint = strpos($string, $break, $limit)))
		{
			if($breakpoint < strlen($string) - 1)
			{
				$string = substr($string, 0, $breakpoint) . $pad;
			}
		}
		return $string;
	}
	public static function convertToHoursMins($time)
	{
		if ($time < 1)
		{
			return;
		}
		$hours = floor($time / 60);
		$minutes = ($time % 60);
		return compact('hours', 'minutes');
	}
	public static function getWeekRange($date, $week_start)
	{
		$week_arr = array(
				0=>'sunday',
				1=>'monday',
				2=>'tuesday',
				3=>'wednesday',
				4=>'thursday',
				5=>'friday',
				6=>'saturday');
			
		$ts = strtotime($date);
		$start = date('w', $ts) == $week_start ? $ts : strtotime('last ' . $week_arr[$week_start], $ts);
		$week_start = ($week_start == 0 ? 6 : $week_start -1);
		return array(date('Y-m-d', $start), date('Y-m-d', strtotime('next ' . $week_arr[$week_start], $start)));
	}
	
	public static function getComingWhere($period, $week_start)
	{
		$where_str = '';
		switch ($period) {
			case 1:
				$where_str = "(CURDATE()=DATE(t1.start_dt))";
				break;
			;
			case 2:
				$where_str = "(DATE(DATE_ADD(NOW(), INTERVAL 1 DAY))=DATE(t1.start_dt))";
				break;
			;
			case 3:
				list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d'), $week_start);
				$where_str = "((t1.start_dt BETWEEN CURDATE() AND '$end_week') OR 
							   (t1.end_dt BETWEEN CURDATE() AND '$end_week') OR 
							   (t1.start_dt <= CURDATE() AND t1.end_dt >= '$end_week'))";
				break;
			;
			case 4:
				list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d', strtotime("+7 days")), $week_start);
				$where_str = "((t1.start_dt BETWEEN '$start_week' AND '$end_week') OR 
							   (t1.end_dt BETWEEN '$start_week' AND '$end_week') OR 
							   (t1.start_dt <= '$start_week' AND t1.end_dt >= '$end_week'))";
				break;
			;
			case 5:
				$end_month = date('Y-m-t',strtotime('this month'));
				$where_str = "((t1.start_dt BETWEEN CURDATE() AND '$end_month') OR 
							   (t1.end_dt BETWEEN CURDATE() AND '$end_month') OR 
							   (t1.start_dt <= CURDATE() AND t1.end_dt >= '$end_month'))";
				break;
			;
			case 6:
				$start_month = date("Y-m-d", mktime(0, 0, 0, date("m") + 1, 1, date("Y")));
				$end_month = date("Y-m-d", mktime(0, 0, 0, date("m") + 2, 0, date("Y")));
				$where_str = "((t1.start_dt BETWEEN '$start_month' AND '$end_month') OR 
							   (t1.end_dt BETWEEN '$start_month' AND '$end_month') OR 
							   (t1.start_dt <= '$start_month' AND t1.end_dt >= '$end_month'))";
				break;
			;
		}
		return $where_str;
	}
	
	public static function getMadeWhere($period, $week_start)
	{
		$where_str = '';
		switch ($period) {
			case 1:
				$where_str = "(DATE(t1.created) = CURDATE() OR DATE(t1.modified) = CURDATE())";
				break;
				;
			case 2:
				$where_str = "(DATE(t1.created) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)) OR DATE(t1.modified) = DATE(DATE_SUB(NOW(), INTERVAL 1 DAY)))";
				break;
				;
			case 3:
				list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d'), $week_start);
				$where_str = "((DATE(t1.created) BETWEEN '$start_week' AND '$end_week') OR (DATE(t1.modified) BETWEEN '$start_week' AND '$end_week'))";
				break;
				;
			case 4:
				list($start_week, $end_week) = pjUtil::getWeekRange(date('Y-m-d', strtotime("-7 days")), $week_start);
				$where_str = "((DATE(t1.created) BETWEEN '$start_week' AND '$end_week') OR (DATE(t1.modified) BETWEEN '$start_week' AND '$end_week'))";
				break;
				;
			case 5:
				$start_month = date('Y-m-01',strtotime('this month'));
				$end_month = date('Y-m-t',strtotime('this month'));
				$where_str = "((DATE(t1.created) BETWEEN '$start_month' AND '$end_month') OR (DATE(t1.modified) BETWEEN '$start_month' AND '$end_month'))";
				break;
				;
			case 6:
				$start_month = date("Y-m-d", mktime(0, 0, 0, date("m")-1, 1, date("Y")));
				$end_month = date("Y-m-d", mktime(0, 0, 0, date("m"), 0, date("Y")));
				$where_str = "((DATE(t1.created) BETWEEN '$start_month' AND '$end_month') OR (DATE(t1.modified) BETWEEN '$start_month' AND '$end_month'))";
				break;
				;
		}
		return $where_str;
	}
	
	public static function getTimezoneName($timezone)
	{
		$offset = $timezone / 3600;
		$timezone_name = timezone_name_from_abbr(null, $offset * 3600, true);
		if($timezone_name === false)
		{
			$timezone_name = timezone_name_from_abbr(null, $offset * 3600, false);
		}
		if($offset == -12)
		{
			$timezone_name = 'Pacific/Wake';
		}
		return $timezone_name;
	}
}
?>