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

use Mirasvit\PushNotification\Api\Data\SubscriberInterface;
use Mobile_Detect;

// @codingStandardsIgnoreFile

/**
 * @SuppressWarnings(PHPMD)
 */
class FingerprintService
{
    private $ipAddress = null;
    private $ipUrl = null;
    private $ipInfo = null;
    private $ipInfoError = false;
    private $ipInfoSource = null;
    private $ipInfoHostname = null;
    private $ipInfoOrg = null;
    private $ipInfoCountry = null;
    private $detect = null;

    public function __construct()
    {
        $this->detect = new Mobile_Detect();
        self::getIp();
    }

    public function getIp()
    {
        if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
            $this->ipAddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $this->ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $this->ipAddress = $_SERVER['REMOTE_ADDR'];
        }
        if (in_array($this->ipAddress, ['::1', '127.0.0.1', 'localhost'])) {
            $this->ipUrl = '';
        } else {
            $this->ipUrl = '/' . $this->ipAddress;
        }

        return $this->ipAddress;
    }

    public function isMobile()
    {
        return $this->detect->isMobile();
    }

    public function isTablet()
    {
        return $this->detect->isTablet();
    }

    public function isPhone()
    {
        return ($this->detect->isMobile() ? ($this->detect->isTablet() ? false : true) : false);
    }

    public function isDesktop()
    {
        return ($this->detect->isMobile() ? false : true);
    }

    public function getDeviceType()
    {
        return $this->detect->isMobile() ?
            ($this->detect->isTablet()
                ? SubscriberInterface::DEVICE_TYPE_TABLET : SubscriberInterface::DEVICE_TYPE_MOBILE)
            : SubscriberInterface::DEVICE_TYPE_DESKTOP;
    }

    public function version($var)
    {
        return $this->detect->version($var);
    }

    public function isEdge()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/Edge\/\d+/', $agent)) {
            return true;
        } else {
            return false;
        }
    }

    public function __call($name, $arguments)
    {
        if (substr($name, 0, 2) != 'is') {
            return null;
        } else {
            return $this->detect->{$name}();
        }
    }

    public function getBrand()
    {
        $brand = 'Unknown Brand';
        switch (self::getDeviceType()) {
            case SubscriberInterface::DEVICE_TYPE_MOBILE:
                foreach ($this->detect->getPhoneDevices() as $name => $regex) {
                    $check = $this->detect->{'is' . $name}();
                    if ($check !== false) {
                        $brand = $name;
                    }
                }
                return $brand;
            case SubscriberInterface::DEVICE_TYPE_TABLET:
                foreach ($this->detect->getTabletDevices() as $name => $regex) {
                    $check = $this->detect->{'is' . $name}();
                    if ($check !== false) {
                        $brand = str_replace('Tablet', '', $name);
                    }
                }
                return $brand;
                break;
            case SubscriberInterface::DEVICE_TYPE_DESKTOP:
                return $brand;
                break;
        }
    }

    public function getOS()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $version = '';
        $codeName = '';
        $os = 'Unknown OS';
        foreach ($this->detect->getOperatingSystems() as $name => $regex) {
            $check = $this->detect->version($name);
            if ($check !== false) {
                $os = $name . ' ' . $check;
            }
            break;
        }
        if ($this->detect->isAndroidOS()) {
            if ($this->detect->version('Android') !== false) {
                $version = ' ' . $this->detect->version('Android');
                switch (true) {
                    case $this->detect->version('Android') >= 5.0:
                        $codeName = ' (Lollipop)';
                        break;
                    case $this->detect->version('Android') >= 4.4:
                        $codeName = ' (KitKat)';
                        break;
                    case $this->detect->version('Android') >= 4.1:
                        $codeName = ' (Jelly Bean)';
                        break;
                    case $this->detect->version('Android') >= 4.0:
                        $codeName = ' (Ice Cream Sandwich)';
                        break;
                    case $this->detect->version('Android') >= 3.0:
                        $codeName = ' (Honeycomb)';
                        break;
                    case $this->detect->version('Android') >= 2.3:
                        $codeName = ' (Gingerbread)';
                        break;
                    case $this->detect->version('Android') >= 2.2:
                        $codeName = ' (Froyo)';
                        break;
                    case $this->detect->version('Android') >= 2.0:
                        $codeName = ' (Eclair)';
                        break;
                    case $this->detect->version('Android') >= 1.6:
                        $codeName = ' (Donut)';
                        break;
                    case $this->detect->version('Android') >= 1.5:
                        $codeName = ' (Cupcake)';
                        break;
                    default:
                        $codeName = '';
                        break;
                }
            }
            $os = 'Android' . $version . $codeName;
        } elseif (preg_match('/Linux/', $agent)) {
            $os = 'Linux';
        } elseif (preg_match('/Mac OS X/', $agent)) {
            if (preg_match('/Mac OS X 10_11/', $agent) || preg_match('/Mac OS X 10.11/', $agent)) {
                $os = 'OS X (El Capitan)';
            } elseif (preg_match('/Mac OS X 10_10/', $agent) || preg_match('/Mac OS X 10.10/', $agent)) {
                $os = 'OS X (Yosemite)';
            } elseif (preg_match('/Mac OS X 10_9/', $agent) || preg_match('/Mac OS X 10.9/', $agent)) {
                $os = 'OS X (Mavericks)';
            } elseif (preg_match('/Mac OS X 10_8/', $agent) || preg_match('/Mac OS X 10.8/', $agent)) {
                $os = 'OS X (Mountain Lion)';
            } elseif (preg_match('/Mac OS X 10_7/', $agent) || preg_match('/Mac OS X 10.7/', $agent)) {
                $os = 'Mac OS X (Lion)';
            } elseif (preg_match('/Mac OS X 10_6/', $agent) || preg_match('/Mac OS X 10.6/', $agent)) {
                $os = 'Mac OS X (Snow Leopard)';
            } elseif (preg_match('/Mac OS X 10_5/', $agent) || preg_match('/Mac OS X 10.5/', $agent)) {
                $os = 'Mac OS X (Leopard)';
            } elseif (preg_match('/Mac OS X 10_4/', $agent) || preg_match('/Mac OS X 10.4/', $agent)) {
                $os = 'Mac OS X (Tiger)';
            } elseif (preg_match('/Mac OS X 10_3/', $agent) || preg_match('/Mac OS X 10.3/', $agent)) {
                $os = 'Mac OS X (Panther)';
            } elseif (preg_match('/Mac OS X 10_2/', $agent) || preg_match('/Mac OS X 10.2/', $agent)) {
                $os = 'Mac OS X (Jaguar)';
            } elseif (preg_match('/Mac OS X 10_1/', $agent) || preg_match('/Mac OS X 10.1/', $agent)) {
                $os = 'Mac OS X (Puma)';
            } elseif (preg_match('/Mac OS X 10/', $agent)) {
                $os = 'Mac OS X (Cheetah)';
            }
        } elseif ($this->detect->isWindowsPhoneOS()) {
            $icon = 'windowsphone8';
            if ($this->detect->version('WindowsPhone') !== false) {
                $version = ' ' . $this->detect->version('WindowsPhoneOS');
                /*switch (true) {
                    case $version >= 8: $icon = 'windowsphone8'; break;
                    case $version >= 7: $icon = 'windowsphone7'; break;
                    default: $icon = 'windowsphone8'; break;
                }*/
            }
            $os = 'Windows Phone' . $version;
        } elseif ($this->detect->version('Windows NT') !== false) {
            switch ($this->detect->version('Windows NT')) {
                case 10.0:
                    $codeName = ' 10';
                    break;
                case 6.3:
                    $codeName = ' 8.1';
                    break;
                case 6.2:
                    $codeName = ' 8';
                    break;
                case 6.1:
                    $codeName = ' 7';
                    break;
                case 6.0:
                    $codeName = ' Vista';
                    break;
                case 5.2:
                    $codeName = ' Server 2003; Windows XP x64 Edition';
                    break;
                case 5.1:
                    $codeName = ' XP';
                    break;
                case 5.01:
                    $codeName = ' 2000, Service Pack 1 (SP1)';
                    break;
                case 5.0:
                    $codeName = ' 2000';
                    break;
                case 4.0:
                    $codeName = ' NT 4.0';
                    break;
                default:
                    $codeName = ' NT v' . $this->detect->version('Windows NT');
                    break;
            }
            $os = 'Windows' . $codeName;
        }
        return $os;

    }

    public function getBrowserName()
    {
        $browser = $this->getBrowser();
        if (strpos($browser, 'Chrome') !== false) {
            return SubscriberInterface::BROWSER_NAME_CHROME;
        } elseif (strpos($browser, 'Firefox') !== false) {
            return SubscriberInterface::BROWSER_NAME_FIREFOX;
        }
    }

    public function getBrowser()
    {
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $browser = 'Unknown Browser';
        if (preg_match('/Edge\/\d+/', $agent)) {
            #$browser = 'Microsoft Edge ' . (floatval($this->detect->version('Edge')) + 8);
            $browser = 'Microsoft Edge ' . str_replace('12', '20', $this->detect->version('Edge'));
        } elseif ($this->detect->version('Trident') !== false && preg_match('/rv:11.0/', $agent)) {
            $browser = 'Internet Explorer 11';
        } else {
            $found = false;
            foreach ($this->detect->getBrowsers() as $name => $regex) {
                $check = $this->detect->version($name);
                if ($check !== false && !$found) {
                    $browser = $name . ' ' . $check;
                    $found = true;
                }
            }
        }
        return $browser;
    }

    public function getIeCountdown($prependHTML = '', $appendHTML = '')
    {
        $ieCountdownHTML = '';
        if ($this->detect->version('IE') !== false && $this->detect->version('IE') <= 9) {
            $ieCountdownHTML = $prependHTML . '<a href="';
            if ($this->detect->version('IE') <= 6) {
                $ieCountdownHTML .= 'http://www.ie6countdown.com';
            } elseif ($this->detect->version('IE') <= 7) {
                $ieCountdownHTML .= 'http://www.theie7countdown.com/ie-users-info';
            } elseif ($this->detect->version('IE') <= 8) {
                $ieCountdownHTML .= 'http://www.theie8countdown.com/ie-users-info';
            } elseif ($this->detect->version('IE') <= 9) {
                $ieCountdownHTML .= 'http://www.theie9countdown.com/ie-users-info';
            }
            $ieCountdownHTML .= '" target="_blank"><strong>YOU ARE USING AN OUTDATED BROWSER</strong><br />It is limiting your experience.<br />Please upgrade your browser,<br />or click this link to read more.</a>' . $appendHTML;
        }
        return $ieCountdownHTML;
    }


    private function getIpInfo()
    {
        try {
            $this->ipInfo = json_decode(file_get_contents('http://ipinfo.io' . $this->ipUrl . '/json'));
            $this->ipAddress = $this->ipInfo->ip;
            $this->ipInfoHostname = $this->ipInfo->hostname;
            $this->ipInfoOrg = $this->ipInfo->org;
            $this->ipInfoCountry = $this->ipInfo->country;
            #list($this->ipInfoLatitude, $this->ipInfoLongitude) = explode(',', $this->ipInfo->loc);
            /*try {
                $googleLocation = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $this->ipInfoLatitude . ',' . $this->ipInfoLongitude . '&sensor=false'));
                $this->ipInfoAddress = $googleLocation->results[2]->formatted_address;
            } catch (Exception  $e) {
                $googleLocation = null;
            }*/
            $this->ipInfoSource = 'ipinfo.io';
            $this->ipInfoError = false;
            return true;
        } catch (\Exception  $e) {
            try {
                $this->ipInfo = json_decode(file_get_contents('http://freegeoip.net/json' . $this->ipUrl));
                $this->ipAddress = $this->ipInfo->ip;
                $this->ipInfoCountry = $this->ipInfo->country_code;
                /*$this->ipInfoLatitude = $this->ipInfo->latitude;
                $this->ipInfoLongitude = $this->ipInfo->longitude;*/
                /*try {
                    $googleLocation = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng=' . $this->ipInfoLatitude . ',' . $this->ipInfoLongitude . '&sensor=false'));
                    $this->ipInfoAddress = $googleLocation->results[2]->formatted_address;
                } catch (Exception  $e) {
                    $googleLocation = null;
                }*/
                $this->ipInfoSource = 'freegeoip.net';
                $this->ipInfoError = false;
                return true;
            } catch (\Exception  $e) {
                $this->ipInfo = null;
                $this->ipInfoSource = null;
                $this->ipInfoError = true;
                return false;
            }
        }
    }

    public function getIpInfoSrc()
    {
        if (is_null($this->ipInfo) && !$this->ipInfoError) {
            self::getIpInfo();
        }
        return $this->ipInfoSource;
    }

    public function getIpHostname()
    {
        if (is_null($this->ipInfo) && !$this->ipInfoError) {
            self::getIpInfo();
        }
        return $this->ipInfoHostname;
    }

    public function getIpOrg()
    {
        if (is_null($this->ipInfo) && !$this->ipInfoError) {
            self::getIpInfo();
        }
        return $this->ipInfoOrg;
    }

    public function getIpCountry()
    {
        if (is_null($this->ipInfo) && !$this->ipInfoError) {
            self::getIpInfo();
        }
        return $this->ipInfoCountry;
    }

    /*public  function ipLatitude() {
        if (is_null($this->ipInfo) && !$this->ipInfoError) { self::getIpInfo(); }
        return $this->ipInfoLatitude;
    }

    public  function ipLongitude() {
        if (is_null($this->ipInfo) && !$this->ipInfoError) { self::getIpInfo(); }
        return $this->ipInfoLongitude;
    }

    public  function ipLocation() {
        if (is_null($this->ipInfo) && !$this->ipInfoError) { self::getIpInfo(); }
        return $this->ipInfoAddress;
    }*/

}
