<?php

namespace Devbatch\Zenoti\Model;

class Zenoti extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Devbatch\Zenoti\Model\ResourceModel\Zenoti::class);
    }
}
