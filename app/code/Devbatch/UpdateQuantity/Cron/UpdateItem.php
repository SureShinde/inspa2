<?php


namespace Devbatch\UpdateQuantity\Cron;

/**
 * Class UpdateItem
 *
 * @package DevBatch\UpdateQuantity\Cron
 */
class UpdateItem
{

    protected $logger;
    protected $collection;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(\Psr\Log\LoggerInterface $logger,
     \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collection)
    {
        $this->logger = $logger;
        $this->collection=$collection;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
       $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/info.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
       
        $collection = $this->collection->create();
        $collection = $collection->addAttributeToSelect('*');

        foreach ($collection as $product) { 
            $product_Id=$product->getId();
            $item= $product->getItemNo();
            if($item){

            $stockInfo = $objectManager->get('Magento\CatalogInventory\Api\StockRegistryInterface')->getStockItem($product_Id);
            // $stockInfo = $stockInfo->getStockItem($product_Id);
            $stockqty = $stockInfo->getQty();

          // $this->logger->addInfo($stockqty);
           
         $this->logger->addInfo("Cronjob UpdateItem is executed.0000000000000000000000000000");

                $baseUrl = isset($wsdls["inspa"]["base"]) ? $wsdls["inspa"]["base"] : 'https://remote.salonservicesnw.com:7247/InSpa/WS/';
                $uri = isset($wsdls["inspa"]["uri"]["sales_order"]) ? $wsdls["inspa"]["uri"]["sales_order"] : 'Salon%20Service/Page/Item';
                $login = isset($wsdls["inspa"]["user"]["test"]) ? $wsdls["inspa"]["user"]["test"] : 'inspaecom';
                $password = isset($wsdls["inspa"]["pass"]["test"]) ? $wsdls["inspa"]["pass"]["test"] : 'tinyTr3e77';    
                $arguments = getopt(null, ["sellToCustomer:","item:","quantity:","crossReference:"]);
                

                // Build context and options for WSDL
                $context = stream_context_create([
                    'ssl' => [
                        // set some SSL/TLS specific options
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ]);

                $wsdlOptions = array(
                    "login" => $login, 
                    "password" => $password,
                    "exceptions" => true,
                    "trace" => 1,
                    "cache_wsdl" => WSDL_CACHE_NONE,
                    "features" => SOAP_SINGLE_ELEMENT_ARRAYS, 
                    "stream_context" => $context
                );

                $client = new \SoapClient($baseUrl.$uri, $wsdlOptions);
                $_read = new \stdClass();      
                $_read->No = $item;    
                $result = $client->Read($_read)->Item->Qty_Avail_to_Sell_WA;
                if($result <= 2)
                {
                    $result = 0;
                }
                $stockInfo->setData('qty',$result);
                $stockInfo->save(); //save stock of item
                $product->save(); 
                }      
        }   
    }
}

