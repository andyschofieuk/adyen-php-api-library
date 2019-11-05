<?php
/**
 *                       ######
 *                       ######
 * ############    ####( ######  #####. ######  ############   ############
 * #############  #####( ######  #####. ######  #############  #############
 *        ######  #####( ######  #####. ######  #####  ######  #####  ######
 * ###### ######  #####( ######  #####. ######  #####  #####   #####  ######
 * ###### ######  #####( ######  #####. ######  #####          #####  ######
 * #############  #############  #############  #############  #####  ######
 *  ############   ############  #############   ############  #####  ######
 *                                      ######
 *                               #############
 *                               ############
 *
 * Adyen API Library for PHP
 *
 * Copyright (c) 2019 Adyen B.V.
 * This file is open source and available under the MIT license.
 * See the LICENSE file for more info.
 *
 */

namespace Adyen\Util;


use Adyen\AdyenException;

class HmacSignatureTest extends \PHPUnit_Framework_TestCase
{
    public function testNotificationRequestItemHmac()
    {
        $params = json_decode('{
	            "pspReference": "7914073381342284",
	            "merchantAccountCode": "TestMerchant",
	            "merchantReference": "TestPayment-1407325143704",
	            "amount": {
	                "value": 1130,
	                "currency": "EUR"
	            },
	            "eventCode": "AUTHORISATION",
	            "success": "true"
	        }', true);
        $key = "DFB1EB5485895CFA84146406857104ABB4CBCABDC8AAF103A624C8F6A3EAAB00";
        $hmac = new HmacSignature();
        try {
            $hmacCalculation = $hmac->calculateNotificationHMAC($key, $params);
            $this->assertNotEmpty($hmacCalculation);
            $this->assertEquals("Ny9gS2veKo5E4w8/OL6xz1/wvT/hYkAXy1xNc/QvO4I=", $hmacCalculation);
            $params['additionalData'] = array(
                'hmacSignature' => $hmacCalculation
            );
            $hmacValidate = $hmac->isValidNotificationHMAC($key, $params);
            $this->assertTrue($hmacValidate);
        } catch (AdyenException $e) {
            $this->fail('Unexpected exception');
        }
    }
    public function testHmacSignatureForRefundWithZeroValue()
    {
        $params = json_decode('{
	            "pspReference": "7914073381342284",
	            "merchantAccountCode": "TestMerchant",
	            "merchantReference": "TestPayment-1407325143704",
	            "amount": {
	                "value": 0,
	                "currency": "EUR"
	            },
	            "eventCode": "REFUND",
	            "success": "true"
	        }', true);
        $key = "44782DEF547AAA06C910C43932B1EB0C71FC68D9D0C057550C48EC2ACF6BA056";
        $hmac = new HmacSignature();
        try {
            $hmacCalculation = $hmac->calculateNotificationHMAC($key, $params);
            $this->assertNotEmpty($hmacCalculation);
            $this->assertEquals("J7HhsgZo5KwqdB7LFZJV6rfQgp+RqC2kuYyw/3x3w+8=", $hmacCalculation);
            $params['additionalData'] = array(
                'hmacSignature' => $hmacCalculation
            );
            $hmacValidate = $hmac->isValidNotificationHMAC($key, $params);
            $this->assertTrue($hmacValidate);
        } catch (AdyenException $e) {
            $this->fail('Unexpected exception');
        }
    }
}
