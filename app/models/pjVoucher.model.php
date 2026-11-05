<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
/**
 * Voucher (discount code).
 * type  : amount (fixed money off) | percent (percentage off the booking subtotal)
 * valid : fixed     - one date, between time_from and time_to
 *         period    - from date_from time_from until date_to time_to
 *         recurring - every weekday ("every"), between time_from and time_to
 * The date and time that are checked are the date and time of the BOOKING (the appointment), not of today.
 */
class pjVoucherModel extends pjAppModel
{
	protected $primaryKey = 'id';
	
	protected $table = 'vouchers';
	
	protected $schema = array(
		array('name' => 'id', 'type' => 'int', 'default' => ':NULL'),
		array('name' => 'code', 'type' => 'varchar', 'default' => ':NULL'),
		array('name' => 'type', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'discount', 'type' => 'decimal', 'default' => ':NULL'),
		array('name' => 'valid', 'type' => 'enum', 'default' => ':NULL'),
		array('name' => 'date_from', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'date_to', 'type' => 'date', 'default' => ':NULL'),
		array('name' => 'time_from', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'time_to', 'type' => 'time', 'default' => ':NULL'),
		array('name' => 'every', 'type' => 'enum', 'default' => ':NULL')
	);
	
	public static function factory($attr=array())
	{
		return new pjVoucherModel($attr);
	}
	
	/**
	 * @param string $code voucher code typed by the customer
	 * @param string $date booking date  (Y-m-d)
	 * @param string $time booking start time (H:i:s)
	 * @return array status OK (200, 'arr' = voucher row) | ERR (100 not found / 101 not valid at that date and time)
	 */
	public function getVoucher($code, $date, $time)
	{
		$result = array('status' => 'ERR', 'code' => '100');
		
		$code = trim((string) $code);
		if ($code === '')
		{
			return $result;
		}
		$arr = $this
			->select("t1.*, TIME_TO_SEC(`time_from`) AS `sec_from`, TIME_TO_SEC(`time_to`) AS `sec_to`")
			->where('t1.code', $code)
			->limit(1)
			->findAll()->getData();
		
		if (count($arr) !== 1)
		{
			return $result;
		}
		$arr = $arr[0];
		$sec = self::hoursToSeconds($time);
		$result['code'] = '101';
		
		switch ($arr['valid'])
		{
			case 'period':
				$timestamp_from = strtotime($arr['date_from'] . ' ' . $arr['time_from']);
				$timestamp_to = strtotime($arr['date_to'] . ' ' . $arr['time_to']);
				$timestamp_current = strtotime($date . ' ' . $time);
				if ($timestamp_current >= $timestamp_from && $timestamp_current <= $timestamp_to)
				{
					$result = array('status' => 'OK', 'code' => '200', 'arr' => $arr);
				}
				break;
			case 'fixed':
				if ($arr['date_from'] == $date && $sec >= (int) $arr['sec_from'] && $sec <= (int) $arr['sec_to'])
				{
					$result = array('status' => 'OK', 'code' => '200', 'arr' => $arr);
				}
				break;
			case 'recurring':
				if (strtolower(date("l", strtotime($date))) == strtolower((string) $arr['every']) && $sec >= (int) $arr['sec_from'] && $sec <= (int) $arr['sec_to'])
				{
					$result = array('status' => 'OK', 'code' => '200', 'arr' => $arr);
				}
				break;
		}
		if ($result['status'] == 'OK')
		{
			// services the voucher is limited to (empty = every service)
			$result['arr']['service_ids'] = self::getServiceIds($arr['id']);
		}
		return $result;
	}
	
	/**
	 * @param int $voucher_id
	 * @return array ids of the services the voucher is limited to; empty = valid for every service
	 */
	public static function getServiceIds($voucher_id)
	{
		$ids = array();
		$rows = pjVoucherServiceModel::factory()->where('t1.voucher_id', (int) $voucher_id)->findAll()->getData();
		foreach ($rows as $row)
		{
			$ids[] = (int) $row['service_id'];
		}
		return $ids;
	}
	
	/**
	 * Does the voucher (a row returned by getVoucher) apply to at least one of the selected services?
	 * A voucher that is not limited to services applies to everything.
	 *
	 * @param array $voucher_arr
	 * @param array $service_id_arr ids of the selected services
	 * @return bool
	 */
	public static function appliesToServices($voucher_arr, $service_id_arr)
	{
		if (empty($voucher_arr['service_ids']))
		{
			return true;
		}
		foreach ((array) $service_id_arr as $id)
		{
			if (in_array((int) $id, $voucher_arr['service_ids'], true))
			{
				return true;
			}
		}
		return false;
	}
	
	protected static function hoursToSeconds($hour)
	{
		$parse = array();
		if (!preg_match('#^(?<hours>\d{2}):(?<mins>\d{2}):(?<secs>\d{2})$#', (string) $hour, $parse))
		{
			return -1;
		}
		return (int) $parse['hours'] * 3600 + (int) $parse['mins'] * 60 + (int) $parse['secs'];
	}
}
?>
