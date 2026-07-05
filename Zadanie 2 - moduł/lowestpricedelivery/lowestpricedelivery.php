<?php
/**
 * 2024 Najniższy koszt dostawy dla produktu
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 *
 * @author    PrestaShop
 * @copyright 2024
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/classes/DeliveryCalculator.php';

class LowestPriceDelivery extends Module {
    public function __construct() {
        $this->name = 'lowestpricedelivery';
        $this->tab = 'pricing';
        $this->version = '1.0.0';
        $this->author = 'Bartosz Katulski';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Najniższy koszt dostawy');
        $this->description = $this->l('Wyświetla najniższy możliwy koszt dostawy na karcie produktu');
        $this->confirmUninstall = $this->l('Czy na pewno chcesz odinstalować ten moduł?');
    }

    public function install() {
        return parent::install()
            && $this->registerHook('displayProductAdditionalInfo');
    }

    public function uninstall() {
        return parent::uninstall();
    }

    public function hookDisplayProductAdditionalInfo(array $params) {
        $idProduct = (int)($params['product']['id_product'] ?? 0);

        if (!$idProduct) {
            return '';
        }

        $calculator = new DeliveryCalculator(
            $this->context
        );

        $lowestPrice = $calculator->getLowestPriceForProduct(
            $idProduct
        );

        if ($lowestPrice === null) {
            return '';
        }

        $deliveryOptions = $calculator->getDeliveryOptionsForProduct(
            $idProduct
        );

        $pickupInfo = $this->buildPickupInfo();

        $this->context->smarty->assign([
            'lowest_delivery_price' => $lowestPrice,
            'lowest_delivery_options' => $deliveryOptions,
            'lowest_delivery_carrier_name' => !empty($deliveryOptions)
                ? $deliveryOptions[0]['carrier_name']
                : '',
            'pickup_delivery_info' => $pickupInfo,
        ]);

        return $this->fetch(
            'module:' . $this->name . '/views/templates/hook/product_delivery_info.tpl'
        );
    }

    private function buildPickupInfo(): string
    {
        if (!isset($this->context->shop)) {
            return '';
        }

        $shopAddress = $this->context->shop->getAddress();
        $shopName = trim((string) Configuration::get('PS_SHOP_NAME', null, null, (int) $this->context->shop->id));
        $shopCity = trim((string) $shopAddress->city);
        $shopAddressLine = trim(implode(', ', array_filter([
            $shopAddress->address1,
            $shopAddress->address2,
            $shopAddress->postcode,
            $shopCity,
        ])));

        $pickupLabel = $this->l('Odbiór osobisty możliwy w sklepie');
        $details = array_filter([
            $shopName !== '' ? $shopName : null,
            $shopAddressLine !== '' ? $shopAddressLine : null,
        ]);

        if (empty($details)) {
            return '';
        }

        return $pickupLabel . ': ' . implode(' | ', $details);
    }
}
