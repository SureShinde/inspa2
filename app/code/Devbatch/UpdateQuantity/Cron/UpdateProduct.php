<?php


namespace Devbatch\UpdateQuantity\Cron;

/**
 * Class UpdateItem
 *
 * @package DevBatch\UpdateQuantity\Cron
 */
class UpdateProduct
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

        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $this->collection->create();
        $collection->addAttributeToSelect('*');
        $UPC;
       


        foreach ($collection as $product) { 

            $UPC=$product->getUpc();
            $id=$product->getId();

           
            if($UPC != null ){


                if ($product->getItemNo() == null){

                    
//                  
                    $baseUrl = isset($wsdls["inspa"]["base"]) ? $wsdls["inspa"]["base"] : 'https://remote.salonservicesnw.com:7247/InSpa/WS/';
                    $uri = isset($wsdls["inspa"]["uri"]["sales_order"]) ? $wsdls["inspa"]["uri"]["sales_order"] : 'Salon%20Service/Page/ItemCrossRef';
                    $login = isset($wsdls["inspa"]["user"]["test"]) ? $wsdls["inspa"]["user"]["test"] : 'inspaecom';
                    $password = isset($wsdls["inspa"]["pass"]["test"]) ? $wsdls["inspa"]["pass"]["test"] : 'tinyTr3e77';    
                    // $arguments = getopt(null, ["sellToCustomer:","item:","quantity:","crossReference:"]);    
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

                    // Create new PHP SOAP client
                 
                    $client = new \SoapClient($baseUrl.$uri, $wsdlOptions);           
                    $read = new \stdClass();              
                    $filter = new \stdClass();
                    $filter->Field = "Cross_Reference_No";
                    $filter->Criteria = $UPC;
                    $read->setSize =0;
                    $read->bookmarkKey="";
                    $read->filter = $filter;

                
                    $array = json_decode(json_encode($client->ReadMultiple($read)->ReadMultiple_Result), true);
                   
                    if(!count($array)){
                       
                        continue;
                    }
                    else {
                      
                        $item_no = $client->ReadMultiple($read)->ReadMultiple_Result->ItemCrossRef[0]->Item_No;
                        $product->setItemNo($item_no);
                        $product->save();
                    }
                }
            }
        }
    }
}

