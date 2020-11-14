<?php
class update
{

	public  function update()
	{
		$sql    = "ALTER TABLE `ps_ecm_newpost_cart` CHANGE `cost` `cost` DECIMAL( 10, 4 ) NOT NULL DEFAULT '25',
		CHANGE `width` `width` DECIMAL( 10, 4 ) NOT NULL ,
		CHANGE `height` `height` DECIMAL( 10, 4 ) NOT NULL ,
		CHANGE `depth` `depth` DECIMAL( 10, 4 ) NOT NULL ,
		CHANGE `weight` `weight` DECIMAL( 10, 4 ) NOT NULL";
		Db::getInstance()->Execute($sql);

		$sql    = "describe `"._DB_PREFIX_."ecm_newpost_orders`";
		$fields = Db::getInstance()->ExecuteS($sql);
		foreach($fields as $rec) $field[] = $rec['Field'];

		$sql = "ALTER TABLE `"._DB_PREFIX_."ecm_newpost_orders` ";
		if(!in_array('width', $field)) $sql .= "ADD `width` DECIMAL( 10, 4 ) NOT NULL ,";
		if(!in_array('height', $field)) $sql .= "ADD `height` DECIMAL( 10, 4 ) NOT NULL ,";
		if(!in_array('depth', $field)) $sql .= "ADD `depth` DECIMAL( 10, 4 ) NOT NULL ,";
		if(!in_array('weight', $field)) $sql .= "ADD `weight` DECIMAL( 10, 4 ) NOT NULL ,";
		if(!in_array('vweight', $field)) $sql .= "ADD `vweight` DECIMAL( 10, 4 ) NOT NULL ,";
		if(!in_array('insurance', $field)) $sql .= "ADD `insurance` DECIMAL( 10, 4 ) NOT NULL ,";
		if(!in_array('description', $field)) $sql .= "ADD `description` VARCHAR( 100 ) NOT NULL ,";
		if(!in_array('pack', $field)) $sql .= "ADD `pack` VARCHAR( 100 ) NOT NULL ,";
		if(!in_array('senderpaynal', $field)) $sql .= "ADD `senderpaynal` TINYINT NOT NULL ,";
		if(!in_array('msg', $field)) $sql .= "ADD `msg` text NOT NULL,";
		if(!in_array('seats_amount', $field)) $sql .= "ADD `seats_amount` INT NOT NULL ";
		Db::getInstance()->Execute($sql);
		if($sql != "ALTER TABLE `"._DB_PREFIX_."ecm_newpost_orders` ") p($sql);


		$sql    = "describe `"._DB_PREFIX_."ecm_newpost_cart`";
		$fields = Db::getInstance()->ExecuteS($sql);
		foreach($fields as $rec) $field[] = $rec['Field'];

		$sql = "ALTER TABLE `"._DB_PREFIX_."ecm_newpost_cart` ";
		if(!in_array('vweight', $field)) $sql .= "ADD `vweight` DECIMAL( 10, 4 ) NOT NULL ";
		Db::getInstance()->Execute($sql);
		if($sql != "ALTER TABLE `"._DB_PREFIX_."ecm_newpost_cart` ") p($sql);

		$module = new ecm_novaposhta();
		//Configuration::updateValue('_home_', 1);
		//Configuration::updateValue('_QTP_',1);
		//Configuration::updateValue('_FU_',1);
		$idTabs = array();
		$idTabs[] = Tab::getIdFromClassName('AdminNPMain');
		$idTabs[] = Tab::getIdFromClassName('AdminNP');
		$idTabs[] = Tab::getIdFromClassName('AdminNPLog');
		foreach($idTabs as $idTab)
		{
			if($idTab)
			{
				$tab = new Tab($idTab);
				$tab->delete();
			}
		}
		$parent_tab = new Tab();
		$parent_tab->class_name = 'AdminNPMain';
		$parent_tab->id_parent = 0;
		$parent_tab->module = $module->name;
		$parent_tab->name[(int) (Configuration::get('PS_LANG_DEFAULT'))] = $module->l('NP Delivery');
		$parent_tab->add();

		// settings
		$tab = new Tab();
		$tab->class_name = 'AdminNP';
		$tab->name[(int) (Configuration::get('PS_LANG_DEFAULT'))] = $module->l('Settings');
		$tab->id_parent = $parent_tab->id;
		$tab->module = $module->name;
		$tab->add();

		// log
		$tab = new Tab();
		$tab->class_name = 'AdminNPLog';
		$tab->name[(int) (Configuration::get('PS_LANG_DEFAULT'))] = $module->l('Log');
		$tab->id_parent = $parent_tab->id;
		$tab->module = $module->name;
		$tab->add();

		$module->hookDisplayBackOfficeHeader();
		Configuration::updateValue('ECM_NP_UP', $time);
		$sql = "UPDATE `" . _DB_PREFIX_ . "module`
		SET `version` = '2.0.2'
		WHERE `name`='ecm_novaposhta'";
		Db::getInstance()->Execute($sql);
		return true;

	}
}
