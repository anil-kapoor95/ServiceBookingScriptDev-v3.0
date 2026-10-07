<?php
if (!defined("ROOT_PATH"))
{
	header("HTTP/1.1 403 Forbidden");
	exit;
}
class pjAdminBookings extends pjAdmin
{
	public function pjActionDoubleCheck()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if($_POST['status'] == 'cancelled')
			{
				echo 'true';
			}else{
				$pjBookingModel = pjBookingModel::factory();
				
				$arr = $pjBookingModel->find($_POST['id'])->getData();
				
				if($arr['status'] == 'cancelled' && $arr['status'] != $_POST['status'])
				{
					$start_dt = $arr['start_dt'];
					$end_dt = $arr['end_dt'];
					
					if(isset($_POST['start_dt']) && isset($_POST['hour_iso']) && isset($_POST['minute_iso']))
					{
					    $start_dt = pjUtil::formatDate($_POST['start_dt'], $this->option_arr['o_date_format']) . ' ' . $_POST['hour_iso'] . ':' . $_POST['minute_iso'] . ':00';
					    $end_dt = date('Y-m-d H:i:s', strtotime($start_dt) + (60 * $_POST['duration']));
					}
					
					$cnt = $pjBookingModel->reset()->where("( (t1.start_dt BETWEEN '$start_dt' AND '$end_dt') OR (t1.end_dt BETWEEN '$start_dt' AND '$end_dt') OR ('$start_dt' BETWEEN t1.start_dt AND t1.end_dt)OR ('$end_dt' BETWEEN t1.start_dt AND t1.end_dt))")->where('t1.status <>', 'cancelled')->findCount()->getData();
					if($cnt > 0)
					{
						echo 'false';
					}else{
						echo 'true';
					}
				}else{
					echo 'true';
				}
			}
		}
		exit;
	}
	
	public function pjActionIndex()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			$service_arr = pjServiceModel::factory()
				->select('t1.*, t2.content as title')
				->join('pjMultiLang', "t2.model='pjService' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
				->where('status', 'T')
				->orderBy("id ASC")
				->findAll()
				->getData();
					
			$this->set('service_arr', $service_arr);
			
			$this->appendJs('jquery.datagrid.js', PJ_FRAMEWORK_LIBS_PATH . 'pj/js/');
			$this->appendJs('pjAdminBookings.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionGetBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$pjBookingModel = pjBookingModel::factory();
				
			if (isset($_GET['q']) && !empty($_GET['q']))
			{
				$q = pjObject::escapeString($_GET['q']);
				$pjBookingModel->where("(t1.id = '$q' OR t1.uuid = '$q' OR t1.c_name LIKE '%$q%' OR t1.c_email LIKE '%$q%')");
			}
			if (isset($_GET['status']) && !empty($_GET['status']) && in_array($_GET['status'], array('confirmed','cancelled','pending')))
			{
				$pjBookingModel->where('t1.status', $_GET['status']);
			}
			if (isset($_GET['service_id']) && (int) $_GET['service_id'] > 0)
			{
				$service_id = (int) $_GET['service_id'];
				$pjBookingModel->where("(t1.id IN(SELECT `TBS`.booking_id FROM `".pjBookingServiceModel::factory()->getTable()."` AS `TBS` WHERE `TBS`.service_id=".$service_id.") )");
			}
			if (isset($_GET['client']) && !empty($_GET['client']))
			{
				$client = pjObject::escapeString($_GET['client']);
				$pjBookingModel->where("(t1.c_name LIKE '%$client%' OR t1.c_email LIKE '%$client%' OR t1.c_phone LIKE '%$client%')");
			}
			if (isset($_GET['uuid']) && !empty($_GET['uuid']))
			{
				$uuid = pjObject::escapeString($_GET['uuid']);
				$pjBookingModel->where("(t1.uuid = '$uuid')");
			}
			if (isset($_GET['start_date']) && !empty($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['end_date']))
			{
				$start_date = pjUtil::formatDate($_GET['start_date'], $this->option_arr['o_date_format']);
				$end_date = pjUtil::formatDate($_GET['end_date'], $this->option_arr['o_date_format']);
				$pjBookingModel->where("(DATE(t1.start_dt) BETWEEN '$start_date' AND '$end_date')");
			}elseif(isset($_GET['start_date']) && !empty($_GET['start_date']) && isset($_GET['end_date']) && empty($_GET['end_date'])){
				$start_date = pjUtil::formatDate($_GET['start_date'], $this->option_arr['o_date_format']);
				$pjBookingModel->where("(DATE(t1.start_dt) >= '$start_date')");
			}elseif(isset($_GET['start_date']) && empty($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['end_date'])){
				$end_date = pjUtil::formatDate($_GET['end_date'], $this->option_arr['o_date_format']);
				$pjBookingModel->where("(DATE(t1.start_dt) <= '$end_date')");
			}
			
			$column = 'created';
			$direction = 'DESC';
			$allowed_columns = array('start_dt', 'c_name', 'services', 'status');
			if (isset($_GET['direction']) && isset($_GET['column']) && in_array($_GET['column'], $allowed_columns) && in_array(strtoupper($_GET['direction']), array('ASC', 'DESC')))
			{
				$column = $_GET['column'];
				$direction = strtoupper($_GET['direction']);
			}

			$total = $pjBookingModel->findCount()->getData();
			$rowCount = isset($_GET['rowCount']) && (int) $_GET['rowCount'] > 0 ? (int) $_GET['rowCount'] : 10;
			$pages = ceil($total / $rowCount);
			$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? intval($_GET['page']) : 1;
			$offset = ((int) $page - 1) * $rowCount;
			if ($page > $pages)
			{
				$page = $pages;
			}

			$data = $pjBookingModel
				->select("t1.*, AES_DECRYPT(t1.cc_type, '".PJ_SALT."') AS `cc_type`,
								AES_DECRYPT(t1.cc_num, '".PJ_SALT."') AS `cc_num`,
								AES_DECRYPT(t1.cc_exp_month, '".PJ_SALT."') AS `cc_exp_month`,
								AES_DECRYPT(t1.cc_exp_year, '".PJ_SALT."') AS `cc_exp_year`,
								AES_DECRYPT(t1.cc_code, '".PJ_SALT."') AS `cc_code`")
				->orderBy("$column $direction")
				->limit($rowCount, $offset)
				->findAll()
				->getData();
			
			$service_arr = array();
			$booking_id_arr = $pjBookingModel->findAll()->getDataPair(null, 'id');
			if(!empty($booking_id_arr))
			{
				$temp_service_arr = pjBookingServiceModel::factory()
					->select("t1.*, t2.content as title")
					->join('pjMultiLang', "t2.foreign_id = t1.service_id AND t2.model = 'pjService' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
					->whereIn('t1.booking_id', $booking_id_arr)
					->findAll()
					->getData();
				
				foreach($temp_service_arr as $k => $v)
				{
					$service_arr[$v['booking_id']][] = pjSanitize::html($v['title']);
				}	

				// extras booked with the services
				$temp_extra_arr = pjBookingExtraModel::factory()->whereIn('t1.booking_id', $booking_id_arr)->findAll()->getData();
				foreach($temp_extra_arr as $k => $v)
				{
					$service_arr[$v['booking_id']][] = '+ ' . pjSanitize::html($v['title']);
				}
			}
			
			foreach($data as $k => $v)
			{
				$temp_arr = pjUtil::convertToHoursMins((int) $v['duration']);
				$duration_arr = array();
				$name_arr = array();
				if((int) $temp_arr['hours'] > 0)
				{
					$duration_arr[] = $temp_arr['hours']. ' ' . ($temp_arr['hours'] != 1 ? __('front_hours', true) : __('front_hour', true));
				}
				if((int) $temp_arr['minutes'] > 0)
				{
					$duration_arr[] = $temp_arr['minutes'] . ' '. ($temp_arr['minutes'] != 1 ? __('front_minutes', true) : __('front_minute', true));
				}
				if(!empty($v['c_name']))
				{
					$name_arr[] = pjSanitize::html($v['c_name']);
				}
				if(!empty($v['c_email']))
				{
					$name_arr[] = pjSanitize::html($v['c_email']);
				}
				$v['start_dt'] = date($this->option_arr['o_date_format'], strtotime($v['start_dt'])) . ', ' . date($this->option_arr['o_time_format'], strtotime($v['start_dt'])) . '<br/>' . join(" ", $duration_arr);
				$v['services'] = isset($service_arr[$v['id']]) ? join("<br/>", $service_arr[$v['id']]) : '';
				$v['c_name'] = !empty($name_arr) ? join("<br/>", $name_arr) : '';
				$data[$k] = $v;
			}
			
			pjAppController::jsonResponse(compact('data', 'total', 'pages', 'page', 'rowCount', 'column', 'direction'));
		}
		exit;
	}
	
	public function pjActionSaveBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$editable = true;
			
			$pjBookingModel = pjBookingModel::factory();
			
			$arr = $pjBookingModel->find($_GET['id'])->getData();
			
			if($arr['status'] == 'cancelled' && $arr['status'] != $_POST['value'] && $_POST['column'] == 'status')
			{
				$start_dt = $arr['start_dt'];
				$end_dt = $arr['end_dt'];
					
				$cnt = $pjBookingModel->reset()->where("( (t1.start_dt BETWEEN '$start_dt' AND '$end_dt') OR (t1.end_dt BETWEEN '$start_dt' AND '$end_dt') OR ('$start_dt' BETWEEN t1.start_dt AND t1.end_dt)OR ('$end_dt' BETWEEN t1.start_dt AND t1.end_dt))")->where('t1.status <>', 'cancelled')->findCount()->getData();
				if($cnt > 0)
				{
					$editable = false;
				}
			}
			
			if($editable == true)
			{
				$pjBookingModel->reset()->where('id', $_GET['id'])->limit(1)->modifyAll(array($_POST['column'] => $_POST['value']));
			}
		}
		exit;
	}
	
	public function pjActionDeleteBooking()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			$response = array();
			if (pjBookingModel::factory()->setAttributes(array('id' => $_GET['id']))->erase()->getAffectedRows() == 1)
			{
				pjBookingServiceModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				pjBookingExtraModel::factory()->where('booking_id', (int) $_GET['id'])->eraseAll();
				pjBookingPaymentModel::factory()->where('booking_id', $_GET['id'])->eraseAll();
				$response['code'] = 200;
			} else {
				$response['code'] = 100;
			}
			pjAppController::jsonResponse($response);
		}
		exit;
	}
	
	public function pjActionDeleteBookingBulk()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['record']) && count($_POST['record']) > 0)
			{
				pjBookingModel::factory()->whereIn('id', $_POST['record'])->eraseAll();
				pjBookingServiceModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
				pjBookingExtraModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
				pjBookingPaymentModel::factory()->whereIn('booking_id', $_POST['record'])->eraseAll();
			}
		}
		exit;
	}
	
	public function pjActionGetHour()
	{
	    $this->setAjax(true);
	    
	    if ($this->isXHR())
	    {
	        $selected_date_iso = pjUtil::formatDate($_POST['start_dt'], $this->option_arr['o_date_format']);
	        $services_duration = isset($_POST['duration']) ? $_POST['duration'] : 0;
	        $booking_id = isset($_POST['id']) ? $_POST['id'] : 0;
	        
	        $wtime_data = $this->getWTimeData($selected_date_iso, $services_duration, $booking_id);
	        
	        $this->set('selected_date_iso', $selected_date_iso);
	        $this->set('services_duration', $services_duration);
	        $this->set('wt_arr', $wtime_data['wt_arr']);
	        $this->set('booking_arr', $wtime_data['booking_arr']);
	        $this->set('booking_id', $booking_id);
	    }
	}
	public function pjActionGetMinute()
	{
	    $this->setAjax(true);
	    
	    if ($this->isXHR())
	    {
	        $selected_date_iso = pjUtil::formatDate($_POST['start_dt'], $this->option_arr['o_date_format']);
	        $services_duration = isset($_POST['duration']) ? $_POST['duration'] : 0;
	        $selected_hour_iso = isset($_POST['hour_iso']) ? $_POST['hour_iso'] : NULl;
	        $booking_id = isset($_POST['id']) ? $_POST['id'] : 0;
	        
	        $wtime_data = $this->getWTimeData($selected_date_iso, $services_duration, $booking_id);
	        
	        $this->set('selected_date_iso', $selected_date_iso);
	        $this->set('selected_hour_iso', $selected_hour_iso);
	        $this->set('services_duration', $services_duration);
	        
	        $this->set('wt_arr', $wtime_data['wt_arr']);
	        $this->set('booking_arr', $wtime_data['booking_arr']);
	        $this->set('booking_id', $booking_id);
	    }
	}
	
	/**
	 * Extras of the add / edit booking forms: the active extras plus the ones already booked
	 * (a booked extra keeps the price / duration it was booked with).
	 */
	private function setExtraData($booking_id = 0)
	{
		$booked = array();
		if ((int) $booking_id > 0)
		{
			foreach (pjBookingExtraModel::factory()->where('t1.booking_id', (int) $booking_id)->findAll()->getData() as $row)
			{
				$booked[(int) $row['extra_id']] = $row;
			}
		}
		$extra_arr = array();
		$rows = pjExtraModel::factory()
			->select('t1.*, t2.content AS title')
			->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
			->orderBy('title ASC')
			->findAll()->getData();
		foreach ($rows as $row)
		{
			if (isset($booked[(int) $row['id']]))
			{
				$row['title'] = $booked[(int) $row['id']]['title'];
				$row['price'] = $booked[(int) $row['id']]['price'];
				$row['duration'] = $booked[(int) $row['id']]['duration'];
				$row['booked'] = true;
				$extra_arr[] = $row;
				unset($booked[(int) $row['id']]);
			} elseif ($row['status'] == 'T') {
				$row['booked'] = false;
				$extra_arr[] = $row;
			}
		}
		foreach ($booked as $row)
		{
			// the extra itself was deleted later on, the booking still keeps it
			$extra_arr[] = array('id' => $row['extra_id'], 'title' => $row['title'], 'price' => $row['price'], 'duration' => $row['duration'], 'status' => 'T', 'booked' => true);
		}
		$this->set('extra_arr', $extra_arr);
	}

	/**
	 * Replace the extras of a booking with the ticked ones. Extras that were already booked keep
	 * their price / duration; newly ticked extras take the current ones.
	 */
	private function saveBookingExtras($booking_id)
	{
		$booking_id = (int) $booking_id;
		$existing = array();
		foreach (pjBookingExtraModel::factory()->where('t1.booking_id', $booking_id)->findAll()->getData() as $row)
		{
			$existing[(int) $row['extra_id']] = $row;
		}
		pjBookingExtraModel::factory()->where('booking_id', $booking_id)->eraseAll();
		if (isset($_POST['extra_id']) && is_array($_POST['extra_id']))
		{
			foreach (array_keys($_POST['extra_id']) as $extra_id)
			{
				if (!ctype_digit((string) $extra_id) || (int) $extra_id <= 0)
				{
					continue;
				}
				$extra_id = (int) $extra_id;
				if (isset($existing[$extra_id]))
				{
					$data = $existing[$extra_id];
				} else {
					$extra = pjExtraModel::factory()
						->select('t1.*, t2.content AS title')
						->join('pjMultiLang', "t2.model='pjExtra' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->find($extra_id)->getData();
					if (empty($extra))
					{
						continue;
					}
					$data = $extra;
				}
				pjBookingExtraModel::factory()->setAttributes(array(
					'booking_id' => $booking_id,
					'extra_id' => $extra_id,
					'title' => $data['title'],
					'price' => $data['price'],
					'duration' => $data['duration']
				))->insert();
			}
		}
	}

	public function pjActionCreate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin())
		{
			if (isset($_POST['booking_create']))
			{
				$_POST = pjAppController::trimDeep($_POST);
				if (!pjAppController::validateClientDetails($_POST, $this->option_arr))
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminBookings&action=pjActionCreate");
				}
				$data = array();
				
				$data['uuid'] = time();
				$data['ip'] = pjUtil::getClientIp();
				$data['start_dt'] = pjUtil::formatDate($_POST['start_dt'], $this->option_arr['o_date_format']) . ' ' . $_POST['hour_iso'] . ':' . $_POST['minute_iso'] . ':00';
				$data['end_dt'] = date('Y-m-d H:i:s', strtotime($data['start_dt']) + (60 * $_POST['duration']));
				
				$id = pjBookingModel::factory(array_merge($_POST, $data))->insert()->getInsertId();
				if ($id !== false && (int) $id > 0)
				{
					if(isset($_POST['service_id']) && is_array($_POST['service_id']) && count($_POST['service_id']) > 0)
					{
						$pjBookingServiceModel = pjBookingServiceModel::factory();
						foreach($_POST['service_id'] as $service_id => $price)
						{
							$service_data = array();
							$service_data['booking_id'] = $id;
							$service_data['service_id'] = $service_id;
												
							$pjBookingServiceModel->reset()->setAttributes($service_data)->insert();
								}
							}
							$this->saveBookingExtras($id);
							$err = 'AR03';
				}else{
					$err = 'AR04';
				}
				
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminBookings&action=pjActionIndex&err=$err");
			}else{
				
				$service_arr = pjServiceModel::factory()
					->select('t1.*, t2.content as title')
					->join('pjMultiLang', "t2.model='pjService' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where('status', 'T')
					->orderBy("id ASC")
					->findAll()
					->getData();
					
				$country_arr = pjCountryModel::factory()
					->select('t1.id, t2.content AS country_title')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('`country_title` ASC')
					->findAll()
					->getData();
				
				$selected_date_iso = date('Y-m-d');
				$services_duration = isset($_POST['duration']) ? $_POST['duration'] : 0;
				$selected_hour_iso = isset($_POST['hour_iso']) ? $_POST['hour_iso'] : NULl;
				$booking_id = isset($_POST['id']) ? $_POST['id'] : 0;
				
				$wtime_data = $this->getWTimeData($selected_date_iso, $services_duration, $booking_id);
				
				$pjWorkingTimeModel = pjWorkingTimeModel::factory();
				$pjDateModel = pjDateModel::factory();
				$week_dayoff_arr = $pjWorkingTimeModel->getDaysOff(1);
				$date_arr = $pjDateModel->where("(t1.date > DATE(NOW()))")->orderBy('`date` ASC')->findAll()->getData();
				$this->set('week_dayoff_arr', $week_dayoff_arr);
				$this->set('date_arr', $date_arr);
				
				$this->set('service_arr', $service_arr);
				$this->setExtraData();
				$this->set('country_arr', $country_arr);
				
				$this->set('selected_date_iso', $selected_date_iso);
				$this->set('selected_hour_iso', $selected_hour_iso);
				$this->set('services_duration', $services_duration);
				
				$this->set('wt_arr', $wtime_data['wt_arr']);
				$this->set('booking_arr', $wtime_data['booking_arr']);
				$this->set('booking_id', $booking_id);
				
				$this->appendJs('jquery-ui-timepicker-addon.js', PJ_THIRD_PARTY_PATH . 'datetimepicker/');
				$this->appendCss('jquery-ui-timepicker-addon.css', PJ_THIRD_PARTY_PATH . 'datetimepicker/');
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminBookings.js');
			}
		} else {
			
			$this->set('status', 2);
		}
	}
	
	public function pjActionUpdate()
	{
		$this->checkLogin();
		
		if ($this->isAdmin() || $this->isEditor())
		{
			if (isset($_POST['booking_update']))
			{
				$_POST = pjAppController::trimDeep($_POST);
				if (!pjAppController::validateClientDetails($_POST, $this->option_arr))
				{
					pjUtil::redirect(PJ_INSTALL_URL . "index.php?controller=pjAdminBookings&action=pjActionUpdate&id=" . (int) @$_POST['id']);
				}
				$pjBookingModel = pjBookingModel::factory();
				
				$arr = pjBookingModel::factory()->find($_POST['id'])->getData();
				if (empty($arr))
				{
					pjUtil::redirect($_SERVER['PHP_SELF'] . "?controller=pjAdminOrders&action=pjActionIndex&err=AR08");
				}
				
				$data = array();
				
				$data['ip'] = pjUtil::getClientIp();
				$data['start_dt'] = pjUtil::formatDate($_POST['start_dt'], $this->option_arr['o_date_format']) . ' ' . $_POST['hour_iso'] . ':' . $_POST['minute_iso'] . ':00';
				$data['end_dt'] = date('Y-m-d H:i:s', strtotime($data['start_dt']) + (60 * $_POST['duration']));
				
				$pjBookingModel->reset()->where('id', $_POST['id'])->limit(1)->modifyAll(array_merge($_POST, $data));
				
				$pjBookingServiceModel = pjBookingServiceModel::factory();
				$pjBookingServiceModel->where('booking_id', $_POST['id'])->eraseAll();
				$this->saveBookingExtras($_POST['id']);
				
				if(isset($_POST['service_id']) && is_array($_POST['service_id']) && count($_POST['service_id']) > 0)
				{
					$pjBookingServiceModel = pjBookingServiceModel::factory();
					foreach($_POST['service_id'] as $service_id => $price)
					{
						$service_data = array();
						$service_data['booking_id'] = $_POST['id'];
						$service_data['service_id'] = $service_id;
				
						$pjBookingServiceModel->reset()->setAttributes($service_data)->insert();
					}
				}
				
				$err = 'AR01';
				pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminBookings&action=pjActionIndex&err=$err");
			}else{
				
				$arr = pjBookingModel::factory()->find($_GET['id'])->getData();
				if(count($arr) <= 0)
				{
					pjUtil::redirect(PJ_INSTALL_URL. "index.php?controller=pjAdminBookings&action=pjActionIndex&err=AR08");
				}
				$this->set('arr', $arr);
				
				$service_arr = pjServiceModel::factory()
					->select('t1.*, t2.content as title')
					->join('pjMultiLang', "t2.model='pjService' AND t2.foreign_id=t1.id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->where('status', 'T')
					->orderBy("id ASC")
					->findAll()
					->getData();
					
				$country_arr = pjCountryModel::factory()
					->select('t1.id, t2.content AS country_title')
					->join('pjMultiLang', "t2.model='pjCountry' AND t2.foreign_id=t1.id AND t2.field='name' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->orderBy('`country_title` ASC')
					->findAll()
					->getData();
				
				$service_id_arr = pjBookingServiceModel::factory()->where('t1.booking_id', $_GET['id'])->findAll()->getDataPair(null, 'service_id');
				
				$selected_date_iso = date('Y-m-d', strtotime($arr['start_dt']));
				$services_duration = $arr['duration'];
				$selected_hour_iso = date('H', strtotime($arr['start_dt']));
				$selected_minute_iso = date('i', strtotime($arr['start_dt']));
				$booking_id = $arr['id'];
				
				$wtime_data = $this->getWTimeData($selected_date_iso, $services_duration, $booking_id);
				
				$pjWorkingTimeModel = pjWorkingTimeModel::factory();
				$pjDateModel = pjDateModel::factory();
				$week_dayoff_arr = $pjWorkingTimeModel->getDaysOff(1);
				$date_arr = $pjDateModel->where("(t1.date > DATE(NOW()))")->orderBy('`date` ASC')->findAll()->getData();
				$this->set('week_dayoff_arr', $week_dayoff_arr);
				$this->set('date_arr', $date_arr);
				
				$this->set('service_arr', $service_arr);
				$this->setExtraData($booking_id);
				$this->set('country_arr', $country_arr);
				$this->set('service_id_arr', $service_id_arr);
				
				$this->set('selected_date_iso', $selected_date_iso);
				$this->set('selected_hour_iso', $selected_hour_iso);
				$this->set('selected_minute_iso', $selected_minute_iso);
				$this->set('services_duration', $services_duration);
				
				$this->set('wt_arr', $wtime_data['wt_arr']);
				$this->set('booking_arr', $wtime_data['booking_arr']);
				$this->set('booking_id', $booking_id);
				
				$this->appendJs('jquery-ui-timepicker-addon.js', PJ_THIRD_PARTY_PATH . 'datetimepicker/');
				$this->appendCss('jquery-ui-timepicker-addon.css', PJ_THIRD_PARTY_PATH . 'datetimepicker/');
				$this->appendJs('tinymce.min.js', PJ_THIRD_PARTY_PATH . 'tinymce/');
				$this->appendJs('chosen.jquery.js', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendCss('chosen.css', PJ_THIRD_PARTY_PATH . 'chosen/');
				$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
				$this->appendJs('pjAdminBookings.js');
			}
		} else {
			$this->set('status', 2);
		}
	}
	public function pjActionConfirmation()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['send_confirm']) && !empty($_POST['to']) && !empty($_POST['from']) &&
					!empty($_POST['subject']) && !empty($_POST['message']))
			{
				$Email = new pjEmail();
				$Email->setContentType('text/html');
				pjAppController::applyEmailTransport($Email, $this->option_arr);
	
				$subject = $_POST['subject'];
				$message = $_POST['message'];
				if (function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc())
				{
					$subject = stripslashes($_POST['subject']);
					$message = stripslashes($_POST['message']);
				}
	
				$r = $Email
					->setTo($_POST['to'])
					->setFrom($_POST['from'])
					->setSubject($subject)
					->send($message);
					
				if ($r)
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Email has been sent.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Email failed to send.'));
			}
	
			if (isset($_GET['booking_id']) && (int) $_GET['booking_id'] > 0)
			{
				$pjMultiLangModel = pjMultiLangModel::factory();
				$lang_message = $pjMultiLangModel->reset()->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $this->getLocaleId())
					->where('t1.field', 'o_email_confirmation_message')
					->limit(0, 1)
					->findAll()->getData();
				$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
					->where('t1.model','pjOption')
					->where('t1.locale', $this->getLocaleId())
					->where('t1.field', 'o_email_confirmation_subject')
					->limit(0, 1)
					->findAll()->getData();
	
				if (count($lang_message) === 1 && count($lang_subject) === 1)
				{
					$booking_arr = pjBookingModel::factory()->find($_GET['booking_id'])->getData();
					$tokens = pjAppController::getTokens($_GET['booking_id'], $this->option_arr, PJ_SALT, $this->getLocaleId());
					
					$subject_client = !empty($lang_subject[0]['content']) ? str_replace($tokens['search'], $tokens['replace'], $lang_subject[0]['content']) : '';
					$message_client = !empty($lang_message[0]['content']) ? str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']) : '';
	
					$this->set('arr', array(
							'to' => $booking_arr['c_email'],
							'from' => $this->getFromEmail(),
							'message' => $message_client,
							'subject' => $subject_client
					));
				}
			} else {
				exit;
			}
		}
	}
	public function pjActionCancellation()
	{
		$this->setAjax(true);
	
		if ($this->isXHR())
		{
			if (isset($_POST['send_cancellation']) && !empty($_POST['to']) && !empty($_POST['from']) &&
					!empty($_POST['subject']) && !empty($_POST['message']))
			{
				$Email = new pjEmail();
				$Email->setContentType('text/html');
				pjAppController::applyEmailTransport($Email, $this->option_arr);
	
				$subject = $_POST['subject'];
				$message = $_POST['message'];
				if (function_exists("get_magic_quotes_gpc") && get_magic_quotes_gpc())
				{
					$subject = stripslashes($_POST['subject']);
					$message = stripslashes($_POST['message']);
				}
	
				$r = $Email
				->setTo($_POST['to'])
				->setFrom($_POST['from'])
				->setSubject($subject)
				->send($message);
					
				if ($r)
				{
					pjAppController::jsonResponse(array('status' => 'OK', 'code' => 200, 'text' => 'Email has been sent.'));
				}
				pjAppController::jsonResponse(array('status' => 'ERR', 'code' => 100, 'text' => 'Email failed to send.'));
			}
	
			if (isset($_GET['booking_id']) && (int) $_GET['booking_id'] > 0)
			{
				$pjMultiLangModel = pjMultiLangModel::factory();
				$lang_message = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $this->getLocaleId())
				->where('t1.field', 'o_email_cancel_message')
				->limit(0, 1)
				->findAll()->getData();
				$lang_subject = $pjMultiLangModel->reset()->select('t1.*')
				->where('t1.model','pjOption')
				->where('t1.locale', $this->getLocaleId())
				->where('t1.field', 'o_email_cancel_subject')
				->limit(0, 1)
				->findAll()->getData();
	
				if (count($lang_message) === 1 && count($lang_subject) === 1)
				{
					$booking_arr = pjBookingModel::factory()->find($_GET['booking_id'])->getData();
					$tokens = pjAppController::getTokens($_GET['booking_id'], $this->option_arr, PJ_SALT, $this->getLocaleId());
						
					$subject_client = !empty($lang_subject[0]['content']) ? str_replace($tokens['search'], $tokens['replace'], $lang_subject[0]['content']) : '';
					$message_client = !empty($lang_message[0]['content']) ? str_replace($tokens['search'], $tokens['replace'], $lang_message[0]['content']) : '';
	
					$this->set('arr', array(
							'to' => $booking_arr['c_email'],
							'from' => $this->getFromEmail(),
							'message' => $message_client,
							'subject' => $subject_client
					));
				}
			} else {
				exit;
			}
		}
	}
	public function pjActionExport()
	{
		$this->checkLogin();
	
		if ($this->isAdmin()|| $this->isEditor())
		{
			if(isset($_POST['booking_export']))
			{
				$pjBookingModel = pjBookingModel::factory();
	
				if($_POST['period'] == 'next')
				{
					$column = '`start_dt`';
					$direction = 'ASC';
	
					$where_str = pjUtil::getComingWhere($_POST['coming_period'], $this->option_arr['o_week_start']);
					if($where_str != '')
					{
						$pjBookingModel->where($where_str);
					}
				}else{
					$column = 'created';
					$direction = 'ASC';
					$where_str = pjUtil::getMadeWhere($_POST['made_period'], $this->option_arr['o_week_start']);
					if($where_str != '')
					{
						$pjBookingModel->where($where_str);
					}
				}
	
				$arr = array();
				$_arr= $pjBookingModel
					->select("t1.id, t1.uuid, t1.start_dt, t1.end_dt, t1.duration, t1.status, t1.subtotal,t1.tax,t1.total,
							  t1.deposit, t1.c_name, t1.c_email, t1.c_phone, t1.c_notes, t1.c_address, t1.c_city, t1.c_country, t1.c_state, t1.c_zip, t1.ip,
							  t1.payment_method, t1.created, t1.modified")
				  	->orderBy("$column $direction")
				  	->findAll()
				  	->getData();
				
				$service_arr = array();
				$booking_id_arr = $pjBookingModel->findAll()->getDataPair(null, 'id');
				if(!empty($booking_id_arr))
				{
					$temp_service_arr = pjBookingServiceModel::factory()
						->select('t1.*, t2.content as title')
						->join('pjMultiLang', "t2.model='pjService' AND t2.foreign_id=t1.service_id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
						->findAll()
						->getData();
					
					foreach($temp_service_arr as $k => $v)
					{
						$service_arr[$v['booking_id']][] = pjSanitize::html($v['title']);
					}
					// extras booked with the services
					$temp_extra_arr = pjBookingExtraModel::factory()->findAll()->getData();
					foreach($temp_extra_arr as $k => $v)
					{
						$service_arr[$v['booking_id']][] = '+ ' . pjSanitize::html($v['title']);
					}
				}
				
				foreach($_arr as $v)
				{
					$v['services'] = isset($service_arr[$v['id']]) ? join(" | ", $service_arr[$v['id']]) : NULL;
					$arr[] = $v;
				}
	
				if($_POST['type'] == 'file')
				{
					$this->setLayout('pjActionEmpty');
	
					if($_POST['format'] == 'csv')
					{
						$csv = new pjCSV();
						$csv
							->setHeader(true)
							->setName("Export-".time().".csv")
							->process($arr)
							->download();
					}
					if($_POST['format'] == 'xml')
					{
						$xml = new pjXML();
						$xml
							->setEncoding('UTF-8')
							->setName("Export-".time().".xml")
							->process($arr)
							->download();
					}
					if($_POST['format'] == 'ical')
					{
						$ical = new pjICal();
						$ical
							->setName("Export-".time().".ics")
							->setProdID('Service Booking')
							->setSummary('c_name')
							->setDateFrom('start_dt')
							->setDateTo('end_dt')
							->setLocation('uuid')
							->setTimezone(pjUtil::getTimezoneName($this->option_arr['o_timezone']))
							->process($arr)
							->download();
					}
					exit;
				}else{
					$pjPasswordModel = pjPasswordModel::factory();
					$password = md5($_POST['password'].PJ_SALT);
					$arr = $pjPasswordModel
						->where("t1.password", $password)
						->limit(1)
						->findAll()
						->getData();
					if (count($arr) != 1)
					{
						$pjPasswordModel->setAttributes(array('password' => $password))->insert();
					}
					$this->set('password', $password);
				}
			}
	
			$this->appendJs('jquery.validate.min.js', PJ_THIRD_PARTY_PATH . 'validate/');
			$this->appendJs('pjAdminBookings.js');
		} else {
			$this->set('status', 2);
		}
	}
	
	public function pjActionExportFeed()
	{
		$this->setLayout('pjActionEmpty');
		$access = true;
		if(isset($_GET['p']))
		{
			$pjPasswordModel = pjPasswordModel::factory();
			$arr = $pjPasswordModel
			->where('t1.password', $_GET['p'])
			->limit(1)
			->findAll()
			->getData();
			if (count($arr) != 1)
			{
				$access = false;
			}
		}else{
			$access = false;
		}
		if($access == true)
		{
			$arr = $this->pjGetFeedData($_GET);
			if(!empty($arr))
			{
				if($_GET['format'] == 'xml')
				{
					$xml = new pjXML();
					echo $xml
					->setEncoding('UTF-8')
					->process($arr)
					->getData();
	
				}
				if($_GET['format'] == 'csv')
				{
					$csv = new pjCSV();
					echo $csv
					->setHeader(true)
					->process($arr)
					->getData();
	
				}
				if($_GET['format'] == 'ical')
				{
					$ical = new pjICal();
					echo $ical
					->setProdID('Service Booking')
					->setSummary('c_name')
					->setDateFrom('start_dt')
					->setDateTo('end_dt')
					->setLocation('uuid')
					->setTimezone(pjUtil::getTimezoneName($this->option_arr['o_timezone']))
					->process($arr)
					->getData();
	
				}
			}
		}else{
			__('lblNoAccessToFeed');
		}
		exit;
	}
	public function pjGetFeedData($get)
	{
		$arr = array();
		$status = true;
		$type = '';
		$period = '';
		if(isset($get['period']))
		{
			if(!ctype_digit($get['period']))
			{
				$status = false;
			}else{
				$period = $get['period'];
			}
		}else{
			$status = false;
		}
		if(isset($get['type']))
		{
			if(!ctype_digit($get['type']))
			{
				$status = false;
			}else{
				$type = $get['type'];
			}
		}else{
			$status = false;
		}
		if($status == true && $type != '' && $period != '')
		{
			$pjBookingModel = pjBookingModel::factory();
				
			if($type == '1')
			{
				$column = '`start_dt`';
				$direction = 'ASC';
					
				$where_str = pjUtil::getComingWhere($period, $this->option_arr['o_week_start']);
				if($where_str != '')
				{
					$pjBookingModel->where($where_str);
				}
			}else{
				$column = 'created';
				$direction = 'DESC';
				$where_str = pjUtil::getMadeWhere($period, $this->option_arr['o_week_start']);
				if($where_str != '')
				{
					$pjBookingModel->where($where_str);
				}
			}
			$_arr = $pjBookingModel
				->select('t1.id, t1.uuid, t1.start_dt, t1.end_dt, t1.duration, t1.status, t1.subtotal,t1.tax,t1.total,
							  t1.deposit, t1.c_name, t1.c_email, t1.c_phone, t1.c_notes, t1.c_address, t1.c_city, t1.c_country, t1.c_state, t1.c_zip, t1.ip,
							  t1.payment_method, t1.created, t1.modified')
				  ->orderBy("$column $direction")
				  ->findAll()
				  ->getData();
			
			$service_arr = array();
			$booking_id_arr = $pjBookingModel->findAll()->getDataPair(null, 'id');
			if(!empty($booking_id_arr))
			{
				$temp_service_arr = pjBookingServiceModel::factory()
					->select('t1.*, t2.content as title')
					->join('pjMultiLang', "t2.model='pjService' AND t2.foreign_id=t1.service_id AND t2.field='title' AND t2.locale='".$this->getLocaleId()."'", 'left outer')
					->findAll()
					->getData();
					
				foreach($temp_service_arr as $k => $v)
				{
					$service_arr[$v['booking_id']][] = pjSanitize::html($v['title']);
				}
				// extras booked with the services
				$temp_extra_arr = pjBookingExtraModel::factory()->findAll()->getData();
				foreach($temp_extra_arr as $k => $v)
				{
					$service_arr[$v['booking_id']][] = '+ ' . pjSanitize::html($v['title']);
				}
			}
			
			foreach($_arr as $v)
			{
				$v['services'] = isset($service_arr[$v['id']]) ? join(" | ", $service_arr[$v['id']]) : NULL;
				$arr[] = $v;
			}
		}
		return $arr;
	}
	
	public function pjActionPrint()
	{
		$this->setLayout('pjActionPrint');
		
		if ($this->isAdmin()|| $this->isEditor())
		{
			$pjBookingModel = pjBookingModel::factory();
			$arr = $pjBookingModel->where("(DATE(t1.start_dt) = DATE(NOW()))")->orderBy('t1.start_dt ASC')->findAll()->getData();
			
			$service_arr = array();
			$booking_id_arr = $pjBookingModel->findAll()->getDataPair(null, 'id');
			if(!empty($booking_id_arr))
			{
				$temp_service_arr = pjBookingServiceModel::factory()
					->select("t1.*, t2.content as title")
					->join('pjMultiLang', "t2.foreign_id = t1.service_id AND t2.model = 'pjService' AND t2.locale = '".$this->getLocaleId()."' AND t2.field = 'title'", 'left')
					->whereIn('t1.booking_id', $booking_id_arr)
					->findAll()
					->getData();
			
				foreach($temp_service_arr as $k => $v)
				{
					$service_arr[$v['booking_id']][] = pjSanitize::html($v['title']);
				}
				// extras booked with the services
				$temp_extra_arr = pjBookingExtraModel::factory()->findAll()->getData();
				foreach($temp_extra_arr as $k => $v)
				{
					$service_arr[$v['booking_id']][] = '+ ' . pjSanitize::html($v['title']);
				}
			}
			foreach($arr as $k => $v)
			{
				$v['services'] = isset($service_arr[$v['id']]) ? join("<br/>", $service_arr[$v['id']]) : NULL;
				$arr[$k] = $v;
			}
			
			$this->set('arr', $arr);
		}
	}
}
?>