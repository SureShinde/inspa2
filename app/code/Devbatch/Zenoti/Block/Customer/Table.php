<?php
namespace Devbatch\Zenoti\Block\Customer;
use Magento\Framework\Controller\Result\RedirectFactory;

class Table extends \Magento\Framework\View\Element\Template  implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    protected $http;
    const KEYSIZE = 32;
    const IVSIZE = 16;
    private $key;
    private $iv;
    private $curl1;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\HTTP\Adapter\Curl $curl1,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepo,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Response\Http $http,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\Message\ManagerInterface $messageManager

    )
    {
        $redirectFactory = 'https://google.com';
        $this->redirectFactory = $redirectFactory;
        $this->customerRepo = $customerRepo;
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->http = $http;
        $this->redirect = $redirect;
        $this->messageManager = $messageManager;
        $this->curl1 = $curl1;

        parent::__construct($context);
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
    function getCenter() {

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
        $user_id =  explode('#', $token);
        $user_token_first =  $user_id[0] . '#';
        $user_token =  $user_id[1];
        $user =  $user_id[2];
        $decrypted_token = $user_token_first . $user_token;

        $customer_detail = $this->getCustomer($decrypted_token, $user);

        $center = $customer_detail->center_id;

        //old
        $u = 'https://apiamrs02.zenoti.com/v1/Centers/' ;
//        $url = 'https://apioperations2.zenotibeta.com/v1/guests/$user';
        $ch = curl_init($u);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'YourScript/0.1 (contact@email)');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $decrypted_token
        ));
        $data = curl_exec($ch);
        $info = curl_getinfo($ch);
        $center_data = json_decode($data, true);

        foreach ($center_data['centers'] as $location)
        {
            if($location['id'] == $center){

                $address_info =  $location['address_info'];
            }
        }

        return $address_info;

    }
    function getTechnition() {

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
//        }

        $user_id =  explode('#', $token);
        $user_token_first =  $user_id[0] . '#';

        $user_token =  $user_id[1];
        $user =  $user_id[2];
        $decrypted_token = $user_token_first . $user_token;

        $customer_detail = $this->getCustomer($decrypted_token, $user);

      if (empty($customer_detail)){

          return;
      }
        $center = $customer_detail->center_id;
        //old
        $u = 'https://apiamrs02.zenoti.com/v1/centers/' . $center . '/therapists/' ;
        $ch = curl_init($u);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'YourScript/0.1 (contact@email)');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $decrypted_token
        ));
        $data = curl_exec($ch);
        $info = curl_getinfo($ch);
        $center_data = json_decode($data, true);
        foreach ($center_data['therapists'] as $therapist)
        {
                $tec_info =  $therapist['personal_info'];

        }
        if (empty($tec_info)){
            $tec_info = 'N/A';
        }

        return $tec_info['name'];

    }
    function getAppointments()
    {


//        $this->messageManager->addError(__("Something went wrong, please try again!"));
//        return;
        //cms_index_index
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');

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



         try{
        if ($customerSession->isLoggedIn()) {



//        //        var_dump($email);zenoti/account/edit
//        //        die();
//
//        $customerRepo = $this->customerRepo->get('ah1@test.com');
//
//        $customer = $this->customerFactory->create()->load($customerRepo->getId());
//
//        //        $customer->setZenotiToken('hello2');
//        //        $customer->save();
//        print_r($customer->getZenotiToken());
//        //        $customer->save();
//        die();
//        var_dump($customer->getData());
//        die();
//        foreach ($customerRepo->getAddresses() as $address)
//        {
//            echo "hello";
//            var_dump($address);
//            die();
//            $user_tokens =  $address->getCompany();
//
////            $zenoti =  $address->getZenotiToken();
//
//        }
//        $cad = $customerRepo->getAddresses();



        $user_id =  explode('#', $token);

        if(empty($user_id[0])){

        }
        $user_token_first =  $user_id[0] . '#';
        $user_token =  $user_id[1];
        $user =  $user_id[2];
        $decrypted_token = $user_token_first . $user_token;

//        $user = 'd19fa187-9043-413b-9efb-e30490c263b4';
//        $decrypted_token = 'AN:operations2|$ARD#gFFni7ivSk5XtfAtLaGIZhvRxfF8IWGuxmpM+NHVNhsqJwHaMAA3T44bS10EWybCXab52Bj/zwJMdQVqiGxrz3lxkmorppA1VEme+XYkLSlnZWLetHfAzZSXLYi+4+2CJU8uBMgIpVe9/xK41eOn12Q2RqQjaIyhKKnYvjaWf1JfGI23SHUp3bGCghQN9pcLBKnZZmCfIIlb0y9QJtFjYFfbRhPtv4dMIt23yZr1QoYQ6potafnXq4JG';

        // consuming appoitments api
        $u = 'https://apiamrs02.zenoti.com/v1/guests/' . $user . '/appointments';
        //        $url = 'https://apioperations2.zenotibeta.com/v1/guests/$user/appointments';
        $ch = curl_init($u);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'YourScript/0.1 (contact@email)');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $decrypted_token
        ));
        $data_appointment = curl_exec($ch);
        $info = curl_getinfo($ch);

        if ($info['http_code'] == "404" ||$info['http_code'] == "504" ) {

            return ;

        }
        }else{
            $this->redirect->redirect($this->http, '/inspa');
//            return;
        }
          } catch (Exception $e) {
                                echo 'Cannot save customer address.';
                            }
        $customer_appointments = json_decode($data_appointment)->appointments;

        return $customer_appointments;
    }
    function getCustomer($decrypted_token,$user)
    {

        $u = 'https://apiamrs02.zenoti.com/v1/guests/' . $user;
//        $url = 'https://apioperations2.zenotibeta.com/v1/guests/$user';
        $ch = curl_init($u);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'YourScript/0.1 (contact@email)');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $decrypted_token
        ));
        $data_customer = curl_exec($ch);
        $info = curl_getinfo($ch);

        $customer_details = json_decode($data_customer);

        return $customer_details;

    }

}
?>

