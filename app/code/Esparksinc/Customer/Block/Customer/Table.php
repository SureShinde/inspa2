<?php
namespace Esparksinc\Customer\Block\Customer;

class Table extends \Magento\Framework\View\Element\Template
{
    private $curl1;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\HTTP\Adapter\Curl $curl1
    )
    {
        $this->curl1 = $curl1;
        parent::__construct($context);
    }
    
    
    public function sendCurlRequest() 
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://jsonplaceholder.typicode.com/posts/1",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache"
        ),
      ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        $response = json_decode($response, true);
        print_r($response['userId']);
    }
}
?>