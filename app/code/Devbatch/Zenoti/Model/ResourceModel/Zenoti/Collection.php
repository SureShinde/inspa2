<?php

namespace Devbatch\Zenoti\Model\ResourceModel\Zenoti;

class Collection extends
\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {
	protected function _construct() {
		$this->_init(\Devbatch\Zenoti\Model\Zenoti::class,
			\Devbatch\Zenoti\Model\ResourceModel\Zenoti::class);
	}
}

