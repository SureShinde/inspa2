<?php
//namespace Devbatch\Zenoti\Block\Zenoti;
//
//
//class Api extends \Magento\Framework\View\Element\Template
//{
//    const KEYSIZE = 32;
//    const IVSIZE = 16;
//    private $key;
//    private $iv;
//
//    public function __construct(
//        \Magento\Framework\App\Action\Context $context
//    )
//    {
//
//        return parent::__construct($context);
//    }
//
//    function decryption($input_text, $passphrase) {
//
//        $encrypted_text = base64_decode($input_text);
//
//        // The salt used in encryption is first extracted from the string.
//        // Format-> "Salted__" + <8 byte salt> + <ciphertext>
//        $salted = substr($encrypted_text, 0, 8) == "Salted__";
//
//        if(!$salted)
//            return null;
//
//        $salt = substr($encrypted_text, 8, 8);
//        $cipher_text = substr($encrypted_text, 16);
//
//        $this->DeriveKeyAndIv($passphrase, $salt);
//
//        return openssl_decrypt($cipher_text, 'aes-256-cbc', $this->key, true, $this->iv);
//    }
//    // Function used for derving key & IV. Used in both encryption & decryption.
//    function DeriveKeyAndIv($passphrase, $salt) {
//
//        $salted = $dx = "";
//        $dx1 = md5($passphrase, true);
//        $dx2 = md5($salt, true);
//
//        for($i = 0; $i < (self::KEYSIZE + self::IVSIZE) / 16; $i++){
//            $dx = md5($dx.$dx1.$dx2, true);
//            $salted = $salted.$dx;
//        }
//
//        $this->key = substr($salted, 0, self::KEYSIZE);
//        $this->iv = substr($salted, self::KEYSIZE, self::IVSIZE);
//    }
//
//    public function execute()
//    {
//        die('123');
//        // getting the encrypted token from param and decrypting it.
//        $user_token = $this->getRequest()->getParam('uid');
//        echo "This is the encrypted token: " .  $user_token ;
//        echo "<br>";
//        echo "<br>";
//        echo "\n" . 'Hello Magento 2! We will change the worldd! ' . "\n";
//        echo "<br>";
//        echo "<br>";
//        // decryption code aes256 rijndael algorithm
//        $passphase = "A3F42947-DE34-42B9-A4BB-321314039CAD";
//        $decrypted = $this->decryption($user_token, $passphase);
//
//        echo "This is the Decrypted token: " . $decrypted;
//        echo "<br>";
//        echo "<br>";
//        echo "\n";
//        $user_id =  explode('#', $decrypted);
//        $user =  $user_id[2];
//        print_r($user_id);
//        echo "<br>";
//        echo "<br>";
//        echo "\n";
//        echo $user;
//        // consuming user details/search api
//        $u = 'https://apioperations2.zenotibeta.com/v1/guests/' . $user . '/gift_cards';
////        $url = 'https://apioperations2.zenotibeta.com/v1/guests/$user';
//        $ch = curl_init($u);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_USERAGENT, 'YourScript/0.1 (contact@email)');
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//            'Content-Type: application/json',
//            'Authorization: Bearer AN:operations2|$ARD#3V33lCobqGVzbd4U34aEkoWWJABMNoMJtl4aosfdeXcCpQ+D/NH48bqKMc4xaDqrv+XjTiEAiSx8KM3a27gQCSoJG2Pak5gdDw0izMYrw8MCCls+VDQc+goFNSnIf5I9+jhNr4ITMhPLr7IzqawLKWYPTLBJoqrIirGl5Fie/zugPXTXSzcCCo2kPTefZVoKmvaX7oxHqwQxbrLpbzmyfw6krK8iZLts3QXIH7Cbl6FFnoAYwm3LtEXj'
//        ));
//        $data = curl_exec($ch);
//        $info = curl_getinfo($ch);
//        echo "<br>";
//        echo "<br>";
//        echo "\n";
//        print_r($data);
//        // consuming appoitments api
//        $u = 'https://apioperations2.zenotibeta.com/v1/guests/' . $user . '/appointments';
//        print_r($u);
//
////        $url = 'https://apioperations2.zenotibeta.com/v1/guests/$user/appointments';
//        $ch = curl_init($u);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_USERAGENT, 'YourScript/0.1 (contact@email)');
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//            'Content-Type: application/json',
//            'Authorization: Bearer AN:operations2|$ARD#3V33lCobqGVzbd4U34aEkoWWJABMNoMJtl4aosfdeXcCpQ+D/NH48bqKMc4xaDqrv+XjTiEAiSx8KM3a27gQCSoJG2Pak5gdDw0izMYrw8MCCls+VDQc+goFNSnIf5I9+jhNr4ITMhPLr7IzqawLKWYPTLBJoqrIirGl5Fie/zugPXTXSzcCCo2kPTefZVoKmvaX7oxHqwQxbrLpbzmyfw6krK8iZLts3QXIH7Cbl6FFnoAYwm3LtEXj'
//        ));
//        $data_appointment = curl_exec($ch);
//        $info = curl_getinfo($ch);
//        echo "<br>";
//        echo "<br>";
//        echo "\n";
//        print_r($data_appointment);
//        exit;
//    }
//
//}