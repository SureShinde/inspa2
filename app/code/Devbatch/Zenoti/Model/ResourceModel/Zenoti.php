<?php

namespace Devbatch\Zenoti\Model\ResourceModel;

class Zenoti extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {
	protected function _construct() {
		$this->_init('zenoti', 'zenoti_id');
	}
}
