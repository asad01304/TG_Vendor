<?php

class TG_Vendor_Model_FtpNotify extends TG_Vendor_Model_Abstract
{
	protected $_vendorType = 'ftp_notification';	
	
    public function _construct()
    {
        parent::_construct();
		$this->_init('vendor/vendor');
    }
	
	public function notifyVendor($order, $items)
	{
		#echo $this->getErrorLogEmail();
		$orderId  = $order->getId();		
		$realOrderId = $order->getIncrementId();
		
		$shipping = $order->getShippingAddress();
		$vendorItemLog = array();
		
		$filename = $this->getFileName();
		$remotePath = $this->getRemotePath();
		$localTmpPath = "/var/b2b/orders/";
		$localLogPath = "/var/b2b/errors/";
		
		$root =  Mage::getBaseDir();
		$hFilename = "{$filename}{$realOrderId}h.csv";
		$hFilePath = "{$root}{$localTmpPath}$hFilename";

		//Drop Shipping
		$priorityMailCode = 'PS1';
		$headerValues = array(
			'CustomerID' => $this->getCustomerId(),
			'PO' 		 => $realOrderId, //$shipping->getPostcode(),
			'ShipMethod' => $priorityMailCode,
			'DropShip'   => 1,
			'FirstName'  => $shipping->getFirstname(),
			'LastName'   => $shipping->getLastname(),
			'Address1'   => $shipping->getStreet(1),
			'Address2'   => $shipping->getStreet(2),
			'City'		 => $shipping->getCity(),
			'State'		 => $shipping->getRegionCode(),
			'Zip' 		 => $shipping->getCity(),
			'Country'	 => $shipping->getCountry(), );

		$headerContent = implode(',', array_keys($headerValues))."\n";

		foreach($headerValues as $key => $value){
			$headerContent .= '"'.$value.'",';
		}
		
		try{
		
			if(!is_dir("{$root}{$localTmpPath}")){
				mkdir("{$root}{$localTmpPath}", 0777, true);
			}
		
			$fp = fopen($hFilePath, "w");
			fputs($fp, $headerContent);
			fclose($fp);
			
			$dFilename = "{$filename}{$realOrderId}d.csv";
			$dFilePath = "{$root}{$localTmpPath}$dFilename";
		
			$detailsContent = "PO,UPC,Quantity"."\n";
			foreach($items as $item){
				$PO  = $realOrderId; //$shipping->getPostcode();
				$UPC = $item->getSku();
				$Quantity = $item->getQtyOrdered();
				
				$detailsContent .= '"'.$PO.'",';
				$detailsContent .= '"'.$UPC.'",';
				$detailsContent .= '"'.$Quantity.'",' ."\n";
				
				$vendorLog = Mage::getModel('vendor/log')
					->setProductId($item->getProductId())
					->setVendorId($this->getVendorId())
					->setItemId($item->getId())
					->setOrderId($orderId)
					->setStatus(0);
				$vendorItemLog [$item->getId()] = $vendorLog;
			}

			$fp = fopen($dFilePath, "w");
			fputs($fp, $detailsContent);
			fclose($fp);
			
			$ftp_server = $this->getFtpHost();
			$ftp_user_name = $this->getFtpUser();
			$ftp_user_pass = $this->getFtpPassword(); 
		
			$con_id = ftp_connect($ftp_server);
			$login_result = ftp_login($con_id, $ftp_user_name, $ftp_user_pass);

			if ((!$con_id) || (!$login_result)) {
			   throw new Exception("FTP connection has failed!");
		   	}
			$upload = ftp_put($con_id, $remotePath.$hFilename, $hFilePath, FTP_BINARY);
			$upload = ftp_put($con_id, $remotePath.$dFilename, $dFilePath, FTP_BINARY);

#			$upload = ftp_put($con_id, $remotePath.$remotePath.$hFilename, $hFilePath, FTP_BINARY);
#			$upload = ftp_put($con_id, $remotePath.$remotePath.$dFilename, $dFilePath, FTP_BINARY);
			
		}catch(Exception $e){
			foreach($vendorItemLog as $vendorLog){
				$vendorLog->setStatus(0)->save();
			}
			
			if($this->getErrorLogEmail()){
				$sendToName    = 'Site Admin';
				$sendToEmail   = $this->getErrorLogEmail();
				$sentFromEmail = 'info@nu.com';
				$msg = 	'Vendor notification can not be sent <br/>'.
						'Details:- <br/>'. $e->getMessage() ;
				
				$mail = Mage::getModel('core/email');
				$mail->setToName($sendToName);
				$mail->setToEmail($sendToEmail);
				$mail->setBody($msg);
				$mail->setSubject("Vendor Notification Error - Order Id {$realOrderId}");
				$mail->setFromEmail($sentFromEmail);
				$mail->setFromName('Site Admin');
				$mail->setType('html');
	
				try {
					$mail->send();
				}
				catch (Exception $e2) {
					//echo $e2->getMessage();
				}
			}
			if(!is_dir("{$root}{$localLogPath}")){
				mkdir("{$root}{$localLogPath}", 0777, true);
			}
			
			$errorFile = "{$root}{$localLogPath}error_log_{$realOrderId}.txt";
			$fp = fopen($errorFile, "w");
			fputs($fp, $e->getMessage());
			fclose($fp);
			return false;
		}
		
		foreach($vendorItemLog as $vendorLog){
			$vendorLog->setStatus(1)->save();
		}
	}	
}