<?php

namespace Devbatch\Zenoti\Controller\Customer;

class BillingAjax extends \Magento\Framework\App\Action\Action
{
    protected $jsonFactory;

//    protected $regionFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepo,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
//        \Magento\Directory\Model\RegionFactory\RegionFactory $regionFactory,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->customerRepo = $customerRepo;
        $this->customerFactory = $customerFactory;
//        $this->regionFactory = $regionFactory;
        $this->customerSession = $customerSession;
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {


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
        $u = 'https://apiamrs02.zenoti.com/v1/guests/' . $user . '?expand=address_info';
        $ch = curl_init($u);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERAGENT,'YourScript/0.1 (contact@email)');
        curl_setopt($ch,CURLOPT_HTTPHEADER,array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $decrypted_token
        ));
//            RegionFactory::
        $data_customer = curl_exec($ch);
        $info = curl_getinfo($ch);

        $customer_details = json_decode($data_customer);

        $customer_info = $customer_details;
        $personal_info = $customer_info->personal_info;
        $phone_info = $personal_info->mobile_phone;
        $address_info = $customer_info->address_info;
        $firstname = $this->getRequest()->getParam('firstnameb');
        $lastname = $this->getRequest()->getParam('lastnameb');
        $phone = $this->getRequest()->getParam('phoneb');
        $city = $this->getRequest()->getParam('city');
        $stateId = $this->getRequest()->getParam('state');
        $zip = $this->getRequest()->getParam('zip');
        $country = $this->getRequest()->getParam('country');
        $street = $this->getRequest()->getParam('street');
        $street2 = $this->getRequest()->getParam('street2');
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
            $personal_info->gender = $zenoti_gender;
            $phone_info->number = $phone;
        }

//        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
//        $region = $objectManager->create('Magento\Directory\Model\ResourceModel\Region\Collection');
//       $da =  $region->loadByCode('AU', 'US');
//        var_dump($da);
//        die();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $region = $objectManager->create('Magento\Directory\Model\Region')
            ->load($stateId);
        $region_data = $region->getData();

//            /*Get alll cieits*/
        $u = 'https://apiamrs02.zenoti.com/v1/countries/225/states';
        $ch = curl_init($u);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_USERAGENT,'YourScript/0.1 (contact@email)');
        curl_setopt($ch,CURLOPT_HTTPHEADER,array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $decrypted_token
        ));
//            RegionFactory::
        $data_customer = curl_exec($ch);
        $info = curl_getinfo($ch);
        $states = json_decode($data_customer);

        foreach ($states->states as $state) {

            if ($state->short_name == $region_data['name']) {
                $getStateId = $state->id;

            }

        };

//            foreach ( Cit)

        if (isset ($phone,$city)) {

            $address_info->address_1 = $street;
            $address_info->address_2 = $street2;
            $address_info->city = $city;
            $address_info->state_other = $getStateId;
            $address_info->zip_code = $zip;
            //225 id is for usa
            $address_info->country_id = 225;
        }


        $post_data = json_encode($customer_details);

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