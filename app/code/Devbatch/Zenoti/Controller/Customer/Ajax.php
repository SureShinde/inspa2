<?php

namespace Devbatch\Zenoti\Controller\Customer;

class Ajax extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepo,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession
    ){
        $this->customerRepo = $customerRepo;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context);
    }
    public function execute()
    {

            $email = $this->getRequest()->getParam('email');
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customerSession = $objectManager->create('Magento\Customer\Model\Session');
            $customer_entity = $customerSession->getData();
            $customer_id =  $customer_entity['customer_id'];

            $model = $objectManager->create('Devbatch\Zenoti\Model\Zenoti');
            $collection = $model->getCollection();
            foreach($collection as $item){

                $data = $item->getData();

                if($data['customer_id'] == $customer_id) {
                    $zenoti_details = $item->getData();
                    $token =  $zenoti_details['zenoti_token'];
                }
            }
            $user_id = explode('#',$token);
            $user_token_first = $user_id[0] . '#';
            $user_token = $user_id[1];
            $user = $user_id[2];
            $decrypted_token = $user_token_first . $user_token;
            $u = 'https://apiamrs02.zenoti.com/v1/guests/' . $user;
            $ch = curl_init($u);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch,CURLOPT_USERAGENT,'YourScript/0.1 (contact@email)');
            curl_setopt($ch,CURLOPT_HTTPHEADER,array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $decrypted_token
            ));
            $data_customer = curl_exec($ch);
            $info = curl_getinfo($ch);
            if ($info['http_code'] == "404") {
                return ;
            }
            $customer_details = json_decode($data_customer);

            $post_dataa = $customer_details;
            $personal_info = $post_dataa->personal_info;

            $firstname = $this->getRequest()->getParam('pageData');
            $lastname = $this->getRequest()->getParam('lastname');
            $email = $this->getRequest()->getParam('email');
            $gender = $this->getRequest()->getParam('gender');

if($gender == 1) {
                $zenoti_gender = 1;
            }
            if($gender == 2) {
                
                $zenoti_gender = 0;
            }

            if($gender == 3) {
                $zenoti_gender = 3;
            }

        if (isset ($firstname,$lastname)) {

                $personal_info->first_name = $firstname;
                $personal_info->last_name = $lastname;
                $personal_info->user_name = $email;
                $personal_info->email = $email;
                $personal_info->gender = $zenoti_gender;
            }

            $post_data = json_encode($post_dataa);
            $ur = 'https://apiamrs02.zenoti.com/v1/guests/' . $user;
            $ch = curl_init($ur);
            curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"PUT");
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            curl_setopt($ch,CURLOPT_HEADER,false);
            curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
            curl_setopt($ch,CURLOPT_HTTPHEADER,array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $decrypted_token
            ));
            $output = curl_exec($ch);
var_dump($output);
            curl_close($ch);

    }
}
