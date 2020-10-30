<?php

namespace Devbatch\Zenoti\Observer;

use Magento\Framework\Event\ObserverInterface;


class CheckLoginPersistentObserver implements ObserverInterface
{
    const KEYSIZE = 32;
    const IVSIZE = 16;
    private $key;
    private $iv;

    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Framework\UrlFactory $urlFactory,
        \Magento\Framework\App\Http\Context $context,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepo,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->customerRepo = $customerRepo;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->_response = $response;
        $this->_urlFactory = $urlFactory;
        $this->_context = $context;
        $this->_actionFlag = $actionFlag;
         $this->request = $request;
    }
    function decryption($input_text, $passphrase) {

        $encrypted_text = base64_decode($input_text);

        // The salt used in encryption is first extracted from the string.
        // Format-> "Salted__" + <8 byte salt> + <ciphertext>
        $salted = substr($encrypted_text, 0, 8) == "Salted__";

        if(!$salted)
            return null;

        $salt = substr($encrypted_text, 8, 8);
        $cipher_text = substr($encrypted_text, 16);

        $this->DeriveKeyAndIv($passphrase, $salt);

        return openssl_decrypt($cipher_text, 'aes-256-cbc', $this->key, true, $this->iv);
    }
    // Function used for derving key & IV. Used in both encryption & decryption.
    function DeriveKeyAndIv($passphrase, $salt) {

        $salted = $dx = "";
        $dx1 = md5($passphrase, true);
        $dx2 = md5($salt, true);

        for($i = 0; $i < (self::KEYSIZE + self::IVSIZE) / 16; $i++){
            $dx = md5($dx.$dx1.$dx2, true);
            $salted = $salted.$dx;
        }

        $this->key = substr($salted, 0, self::KEYSIZE);
        $this->iv = substr($salted, self::KEYSIZE, self::IVSIZE);
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $actionName = $observer->getEvent()->getRequest()->getFullActionName();

        $openActions = array(
            'cms_index_index',
            'onestepcheckout_index_index'
        );
        if (in_array($actionName,$openActions)) {


                $user_token = $observer->getRequest()->getParam('uid');

                // $passphase = "A3F42947-DE34-42B9-A4BB-321314039CAD";
                $passphase = "801F4CF3-05D6-4499-86F3-FC92CE44E7DE";

                $decrypted = $this->decryption($user_token,$passphase);

                $user_id = explode('#',$decrypted);

                if (count($user_id) > 1) {

                    $user_token_first = $user_id[0] . '#';
                    $user_token = $user_id[1];
                    $user = $user_id[2];

                    $decrypted_token = $user_token_first . $user_token;

                    $u = 'https://apiamrs02.zenoti.com/v1/guests/' . $user . '?expand=address_info';

//        $url = 'https://apioperations2.zenotibeta.com/v1/guests/$user';
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
                        return;
                    }

                    $customer_data = json_decode($data_customer);

//                    var_dump($user);
//                    var_dump($decrypted_token);
//                    die();
                    $address_info = $customer_data->address_info;
                    $personal_info = $customer_data->personal_info;
                    $phone_info = $personal_info->mobile_phone;
                    $phone = $phone_info->number;
                    $street = $address_info->address_1;
                    $street2 = $address_info->address_2;
                    $city = $address_info->city;
                    $getStateId = $address_info->state_id;
                    $StateName = $address_info->state_other;
                    $zip = $address_info->zip_code;
                    $country = $address_info->country_id;
                    // get country id
                    $u = 'https://apiamrs02.zenoti.com/v1/countries';
                    $ch = curl_init($u);
                    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
                    curl_setopt($ch,CURLOPT_USERAGENT,'YourScript/0.1 (contact@email)');
                    curl_setopt($ch,CURLOPT_HTTPHEADER,array(
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $decrypted_token
                    ));
//            RegionFactory::
                    $all_country = curl_exec($ch);
                    $info = curl_getinfo($ch);

                    $countries = json_decode($all_country);

                    foreach ($countries->countries as $one_country) {

                        if ($one_country->id == 95) {

                            $getStateId = $one_country->short_name;
                        }

                    }

                    $country_id = substr($getStateId,0,2);

                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

                    $appState = $objectManager->get('\Magento\Framework\App\State');
                    //$appState->setAreaCode('frontend'); // not needed if Area code is already set


                    $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
                    $websiteId = $storeManager->getStore()->getWebsiteId();

                    $firstName = $personal_info->first_name;
                    $lastName = $personal_info->last_name;

                    $email = $personal_info->user_name;
                    $password = 'Test1234';

                    // instantiate customer object
                    $customer = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();
                    $customer->setWebsiteId($websiteId);

                    // if customer is already created, show message
                    // else create customer and log in
                    if (empty($email)){
                        return;
                    }

                    if ($customer->loadByEmail($email)->getId()) {


                        $customerRepo = $this->customerRepo->get($email);

                        //load with email
                        $customer = $this->customerFactory->create()->load($customerRepo->getId());     //get the customer model by id

                        $this->customerSession->setCustomerAsLoggedIn($customer);

                        $this->customerSession->setZenotiToken($decrypted);
                        $gettoken = $this->customerSession->getZenotiToken();

                        if (empty($gettoken)) {

                            echo " <script>alert(\"There is something wrong, Please try Again!\")</script>";
                            return;
                        }

                        $addresses = $customer->getAddresses();
                        if (count($addresses)) {
                            foreach ($addresses as $addresse) {

                                if (isset($city)) {

                                    $city = "N/A";
                                }
                                if (isset($zip)) {

                                    $zip = 54000;
                                }
                                if (isset($street)) {

                                    $street = 'N/A                                                                                                                              ';
                                }

                                $addresse->setFirstname($firstName)
                                    ->setLastname($lastName)
                                    ->setCountryId($country_id)
                                    ->setPostcode($zip)
                                    ->setCity($city)
                                    ->setRegionId($getStateId) // optional, depends upon Country, e.g. USA
                                    ->setRegion($StateName) // optional, depends upon Country, e.g. USA
                                    ->setTelephone($phone)
                                    ->setCompany('inspa')
                                    ->setStreet(array(
                                        '0' => $street, // compulsory
                                        '1' => $street2 // optional
                                    ));

                                // save customer address
                                $addresse->save();


                            }
                        }

//                    die();
////                    die();
//                    $customerAddress = $objectManager->get('\Magento\Customer\Model\AddressFactory')->create();
//                    $customer->getAddresses();
//                    $customerAddress->load('parent_id',35);
//
//                    var_dump($customerAddress->getData());
//                    die();
//                    $customerAddress->setCustomerId($customer->getId())
//                        ->setFirstname($firstName)
//                        ->setLastname($lastName)
//
//                        ->setCountryId('US')
//                        ->setPostcode('90232')
//                        ->setCity('Culver City')
//                        ->setTelephone('888-888-8888')
//                        ->setCompany($decrypted)
//                        ->setStreet(array(
//                            '0' => 'Your Customer Address 1', // compulsory
//                            '1' => 'Your Customer Address 2' // optional
//                        ));
//                        ->setIsDefaultBilling('1')
//                        ->setIsDefaultShipping('1')
//                        ->setSaveInAddressBook('1');
//
//                    try {
//
//                        // save customer address
//                        $addresse->save();
//
//                    } catch (Exception $e) {
//                        echo 'Cannot save customer address.';
//                    }

//                    $customerRepo = $this->customerRepo->get($email);
//
//                    //load with email
//                    $customer = $this->customerFactory->create()->load($customerRepo->getId());     //get the customer model by id
//
//                    $this->customerSession->setCustomerAsLoggedIn($customer);

                    } else {
                        try {

                            // prepare customer data
                            $customer->setEmail($email);
                            $customer->setFirstname($firstName);
                            $customer->setLastname($lastName);
                            //
                            $customer->setPassword($password);
                            $customer->setForceConfirmed(true);

                            // set null to auto-generate password


                            // set the customer as confirmed
                            // this is optional
                            // comment out this line if you want to send confirmation email
                            // to customer before finalizing his/her account creation


                            // save data
                            $customer->save();

                            // save customer address
                            // this is optional
                            // you can skip saving customer address while creating the customer

                            $customerAddress = $objectManager->get('\Magento\Customer\Model\AddressFactory')->create();
                            $customerAddress->setCustomerId($customer->getId())
                                ->setFirstname($firstName)
                                ->setLastname($lastName)
                                ->setCountryId('US')
                                ->setPostcode('90232')
                                ->setCity('Culver City')
                                ->setTelephone('888-888-8888')
                                ->setFax('999')
                                ->setCompany('inspa')
                                ->setStreet(array(
                                    '0' => 'Your Customer Address 1', // compulsory
                                ))
                                ->setIsDefaultBilling('1')
                                ->setIsDefaultShipping('1')
                                ->setSaveInAddressBook('1');

                            try {
                                // save customer address
                                $customerAddress->save();
                            } catch (Exception $e) {
                                echo 'Cannot save customer address.';
                            }
                            // send welcome email to the customer
//                $customer->sendNewAccountEmail();
//                echo 'Customer with the emails ' . $email . ' is successfully created.';
                            //log user
                            $customerRepo = $this->customerRepo->get($email);                    //load with email
                            $customer = $this->customerFactory->create()->load($customerRepo->getId());     //get the customer model by id

                            $this->customerSession->setCustomerAsLoggedIn($customer);
                            $this->customerSession->setZenotiToken($decrypted);
                            $gettoken = $this->customerSession->getZenotiToken();
                            if (empty($gettoken)) {
                                echo " <script>alert(\"There is something wrong, Please try Again!\")</script>";
                                return;
                            }
                        } catch (Exception $e) {
                            echo $e->getMessage();
                        }
                    }
                    if ($actionName == 'cms_index_index') {
                        $this->_response->setRedirect($this->_urlFactory->create()->getUrl('customer/account/'));
                    }else{
                        $this->_response->setRedirect($this->_urlFactory->create()->getUrl('onestepcheckout/'));
                    }
                }




        }

    }

}