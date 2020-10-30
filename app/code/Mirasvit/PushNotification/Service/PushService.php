<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-push-notification
 * @version   1.1.18
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\PushNotification\Service;

use Mirasvit\PushNotification\Api\Data\MessageInterface;
use Mirasvit\PushNotification\Api\Repository\MessageRepositoryInterface;
use Mirasvit\PushNotification\Api\Repository\SubscriberRepositoryInterface;
use Mirasvit\PushNotification\Api\Service\PushServiceInterface;
use Mirasvit\PushNotification\Model\Config;

class PushService implements PushServiceInterface
{
    const PUSH_SUCCESS = 'ok';
    const PUSH_404     = '404';
    const PUSH_FAILURE = 'error';

    /**
     * @var SubscriberRepositoryInterface
     */
    private $subscriberRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var MessageRepositoryInterface
     */
    private $messageRepository;

    public function __construct(
        SubscriberRepositoryInterface $subscriberRepository,
        MessageRepositoryInterface $messageRepository,
        Config $config
    ) {
        $this->subscriberRepository = $subscriberRepository;
        $this->messageRepository    = $messageRepository;
        $this->config               = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function pushMessages()
    {
        $collection = $this->messageRepository->getCollection();

        $collection->addFieldToFilter(MessageInterface::IS_PUSHED, false);

        foreach ($collection as $message) {
            $this->push($message);

            $this->messageRepository->setPushed($message);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function push(MessageInterface $message)
    {
        $endpoint = $message->getEndpoint();

        $t     = explode('/', $endpoint);
        $token = end($t);


        if (strpos($endpoint, 'android.googleapis.com') !== false) {
            return $this->sendChrome($token);
        } elseif (strpos($endpoint, 'fcm.googleapis.com') !== false) {
            return $this->sendChromeFcm($endpoint, $token);
        } elseif (strpos($endpoint, 'updates.push.services.mozilla.com')) {
            return $this->sendFirefox($token);
        }

        return self::PUSH_FAILURE;
    }

    /**
     * @param string $token
     * @return string
     */
    private function sendFirefox($token)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://updates.push.services.mozilla.com/wpush/" . $token);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["TTL: 86400"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        if ($result === "") {
            return self::PUSH_SUCCESS;
        } else {
            $result = \Zend_Json_Decoder::decode($result);

            if ($result['code'] == 404) {
                return self::PUSH_404;
            }
        }

        return self::PUSH_FAILURE;
    }

    /**
     * @param string $token
     * @return string
     */
    private function sendChrome($token)
    {
        $apiKey = $this->config->getGoogleApiKey(0);
        $url    = 'https://android.googleapis.com/gcm/send';

        $fields  = [
            'registration_ids' => [$token],
        ];
        $headers = [
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);

        $result = \Zend_Json_Decoder::decode($result);

        if ($result['success'] == true) {
            return self::PUSH_SUCCESS;
        } else {
            return self::PUSH_FAILURE;
        }
    }

    /**
     * @param string $endpoint
     * @param string $token
     * @return string
     */
    private function sendChromeFcm($endpoint, $token)
    {
        $apiKey = $this->config->getGoogleApiKey(0);
        $url    = $endpoint;

        $fields = [
            'registration_ids' => [
                $token,
            ],
            'data'             => [
                "message" => 'abc',
            ],
        ];
        $fields = json_encode($fields);

        $headers = [
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json',
            'TTL: 100',
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

        $result = curl_exec($ch);

        // print_r($url);
        // print_r($fields);
        // print_r($headers);
        // print_r($result);die();

        curl_close($ch);

        return self::PUSH_SUCCESS;
    }
}
