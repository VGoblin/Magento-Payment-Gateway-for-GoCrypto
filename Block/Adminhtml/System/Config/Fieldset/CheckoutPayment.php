<?php
/*
 * Copyright (C) 2017 beGateway
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * @author      beGateway
 * @copyright   2017 beGateway
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2 (GPL-2.0)
 */

namespace GoCryptoPay\GoCryptoPay\Block\Adminhtml\System\Config\Fieldset;

/**
 * Renderer for beGateway Checkout Panel in System Configuration
 *
 * Class CheckoutPayment
 * @package BeGateway\BeGateway\Block\Adminhtml\System\Config\Fieldset
 */
class CheckoutPayment extends \GoCryptoPay\GoCryptoPay\Block\Adminhtml\System\Config\Fieldset\Base\Payment
{
    /**
     * Retrieves the Module Panel Css Class
     * @return string
     */
    protected function getBlockHeadCssClass()
    {
        return "GoCryptoPayCheckout";
    }
}
