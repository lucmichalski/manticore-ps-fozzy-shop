<?php
class upgrade
{
	public function ExtractDomain($domain,$IgnoreWWW = false)
	{

		$urlMap = array('com','info','org','biz','net','su','ua','org.ua','com.ua','net.ua','kiev.ua','cn.ua','od.ua','in.ua','biz.ua','forforce.com','loc');

		$hostData = explode('.', $domain);
		if($IgnoreWWW and $hostData[0] == 'www') unset($hostData[0]);
		$hostData = array_reverse($hostData);

		if(array_search($hostData[1] . '.' . $hostData[0], $urlMap) !== FALSE){
			$domain = $hostData[2];
		}
		elseif(array_search($hostData[0], $urlMap) !== FALSE){
			$domain = $hostData[1];
		}

		return $domain;
	}
	public function load()
	{
		$domain     = $_SERVER['HTTP_HOST'];
		$target_url = 'http://update.elcommerce.com.ua/updates/novaposhta/'.self::ExtractDomain($_SERVER['HTTP_HOST'],true).'/ecm_novaposhta.zip';
		if(function_exists('curl_init'))
		{
			$ch = curl_init($target_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
			$output = curl_exec($ch);
			if(!file_exists(_PS_MODULE_DIR_.'ecm_novaposhta/upgrade/updates/'))
			{
				mkdir(_PS_MODULE_DIR_.'ecm_novaposhta/upgrade/updates/');
			}
			$zip = _PS_MODULE_DIR_.'ecm_novaposhta/upgrade/updates/ecm_novaposhta.zip';
			$fh  = fopen($zip, 'w');
			fwrite($fh, $output);
			fclose($fh);
			self::unzip($zip);
			//$bak = self::recursiveCopy(_PS_MODULE_DIR_.'ecm_novaposhta',_PS_MODULE_DIR_.'ecm_novaposhta/backup',true);
			$copy= self::recursiveCopy(_PS_MODULE_DIR_.'ecm_novaposhta/upgrade/updates/ecm_novaposhta',_PS_MODULE_DIR_.'ecm_novaposhta');
			if($copy)
			{
				self::deleteDir(_PS_MODULE_DIR_.'ecm_novaposhta/upgrade/updates/');
				require_once(_PS_MODULE_DIR_.'ecm_novaposhta/update.php');
				$update = New update();
				$update->update();
			}

		}
		else
		{
			return $this->_html .= '
			<div class="bootstrap">
			<div class="alert alert-alert">
			<btn btn-default button type="btn btn-default button" class="close" data-dismiss="alert">×</btn btn-default button>
			Enable CURL on your hosting!!!
			</div>
			</div>
			';
		}


		return true;
	}
	public function unzip($file)
	{
		$zip = new ZipArchive;
		if(!$zip)
		return $this->_html .= '
		<div class="bootstrap">
		<div class="alert alert-alert">
		<btn btn-default button type="btn btn-default button" class="close" data-dismiss="alert">×</btn btn-default button>
		Enable ZIP on your hosting!!!
		</div>
		</div>
		';
		$res = $zip->open($file);
		if($res === TRUE)
		{
			$zip->extractTo(_PS_MODULE_DIR_.'ecm_novaposhta/upgrade/updates/');
			$zip->close();

		}
		else
		{
			return $this->_html .= '
			<div class="bootstrap">
			<div class="alert alert-alert">
			<btn btn-default button type="btn btn-default button" class="close" data-dismiss="alert">×</btn btn-default button>
			Do not unpack updates!!!
			</div>
			</div>
			';
		}
		return true;
	}
	public function recursiveCopy($from, $to, $copy = null)
	{
		if(!file_exists($to))
		{
			mkdir($to);
		}
		if($objs = glob($from."/*"))
		{
			foreach($objs as $obj)
			{
				$forto = $to.str_replace($from, '', $obj);
				// echo $to.' < br > ';
				if(is_dir($obj))
				{
					self::recursiveCopy($obj, $forto);
				}
				else
				{
					if (is_null($copy))
					rename($obj, $forto);
					else
					copy($obj, $forto);
				}
			}
		}
		return true;
	}
	public static function deleteDir($dirPath)
	{
		if(! is_dir($dirPath))
		{
			throw new InvalidArgumentException("$dirPath must be a directory");
		}
		if(substr($dirPath, strlen($dirPath) - 1, 1) != '/')
		{
			$dirPath .= '/';
		}
		$files = glob($dirPath . '*', GLOB_MARK);
		foreach($files as $file)
		{
			if(is_dir($file))
			{
				self::deleteDir($file);
			}
			else
			{
				unlink($file);
			}
		}
		rmdir($dirPath);
	}


}

