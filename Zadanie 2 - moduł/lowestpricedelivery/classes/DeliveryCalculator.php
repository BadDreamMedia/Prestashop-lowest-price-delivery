<?php

declare(strict_types=1);

class DeliveryCalculator
{
    private Context $context;

    private const CACHE_PREFIX = 'lpd_';

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getLowestPriceForProduct(
        int $idProduct,
        int $quantity = 1
    ): ?float {
        $deliveryOptions = $this->getDeliveryOptionsForProduct(
            $idProduct,
            $quantity
        );

        if (empty($deliveryOptions)) {
            return null;
        }

        $lowestPrice = null;

        foreach ($deliveryOptions as $option) {
            if (
                $lowestPrice === null
                || $option['price'] < $lowestPrice
            ) {
                $lowestPrice = $option['price'];
            }
        }

        return $lowestPrice;
    }

    public function getDeliveryOptionsForProduct(
        int $idProduct,
        int $quantity = 1
    ): array {
        $cacheKey = $this->buildCacheKey(
            $idProduct,
            $quantity,
            'options'
        );

        if (Cache::isStored($cacheKey)) {
            return Cache::retrieve($cacheKey);
        }

        $cart = $this->createTemporaryCart();

        try {
            $cart->updateQty(
                $quantity,
                $idProduct
            );

            $deliveryOptions = $this->extractDeliveryOptions(
                $cart
            );

            if (!empty($deliveryOptions)) {
                Cache::store(
                    $cacheKey,
                    $deliveryOptions
                );
            }

            return $deliveryOptions;
        } finally {
            $this->cleanupCart($cart);
        }
    }

    private function extractDeliveryOptions(
        Cart $cart
    ): array {
        $deliveryOptions = $cart->getDeliveryOptionList();

        if (empty($deliveryOptions)) {
            return [];
        }

        $bestOption = null;

        foreach ($deliveryOptions as $addressOptions) {
            foreach ($addressOptions as $option) {
                $price = $option['total_price_with_tax']
                    ?? $option['total_price_without_tax']
                    ?? null;

                if ($price === null) {
                    continue;
                }

                if ($this->isPickupOption($option)) {
                    continue;
                }

                $carrierNames = $this->getCarrierNames($option);

                if (empty($carrierNames)) {
                    continue;
                }

                $priceValue = (float) $price;

                $candidate = [
                    'price' => $priceValue,
                    'carrier_name' => implode(', ', $carrierNames),
                    'carrier_names' => $carrierNames,
                    'is_free' => $priceValue <= 0,
                    'price_label' => $priceValue <= 0
                        ? 'Darmowa dostawa'
                        : sprintf('%.2f zł', $priceValue),
                ];

                if (
                    $bestOption === null
                    || $candidate['price'] < $bestOption['price']
                ) {
                    $bestOption = $candidate;
                }
            }
        }

        return $bestOption === null ? [] : [$bestOption];
    }

    private function getCarrierNames(array $option): array
    {
        $carrierNames = [];

        if (!isset($option['carrier_list']) || !is_array($option['carrier_list'])) {
            return $carrierNames;
        }

        foreach ($option['carrier_list'] as $carrierData) {
            if (!isset($carrierData['instance']) || !$carrierData['instance'] instanceof Carrier) {
                continue;
            }

            $carrierName = trim((string) $carrierData['instance']->name);

            if ($carrierName === '' || $this->isPickupCarrierName($carrierName)) {
                continue;
            }

            $carrierNames[] = $carrierName;
        }

        return array_values(array_unique($carrierNames));
    }

    private function isPickupOption(array $option): bool
    {
        if (!isset($option['carrier_list']) || !is_array($option['carrier_list'])) {
            return false;
        }

        foreach ($option['carrier_list'] as $carrierData) {
            if (!isset($carrierData['instance']) || !$carrierData['instance'] instanceof Carrier) {
                continue;
            }

            if ($this->isPickupCarrierName((string) $carrierData['instance']->name)) {
                return true;
            }
        }

        return false;
    }

    private function isPickupCarrierName(string $carrierName): bool
    {
        $normalizedName = strtolower(trim($carrierName));

        if ($normalizedName === '') {
            return false;
        }

        $keywords = [
            'odbiór osobisty',
            'odbior osobisty',
            'odbiór',
            'pickup',
            'self pickup',
            'self-pickup',
            'personal pickup',
            'punkt odbioru',
            'collection',
            'collect',
            'click and collect',
            'click-and-collect',
            'click & collect',
        ];

        foreach ($keywords as $keyword) {
            if (strpos($normalizedName, $keyword) !== false) {
                return true;
            }
        }

        return false;
    }

    private function createTemporaryCart(): Cart
    {
        $cart = new Cart();

        $cart->id_shop = (int)$this->context->shop->id;
        $cart->id_lang = (int)$this->context->language->id;
        $cart->id_currency = (int)$this->getCurrencyId();
        $cart->id_customer = (int)$this->getCustomerId();

        $address = $this->createTemporaryAddress();

        $cart->id_address_delivery = (int)$address->id;
        $cart->id_address_invoice = (int)$address->id;

        if (!$cart->add()) {
            throw new \RuntimeException('Failed to create temporary cart');
        }

        return $cart;
    }

    private function createTemporaryAddress(): Address
    {
        $defaultCountry = (int) Configuration::get('PS_COUNTRY_DEFAULT');
        $countryId = (int) Configuration::get(
            'LPD_DEFAULT_COUNTRY',
            null,
            null,
            null,
            $defaultCountry
        );

        if ($countryId <= 0) {
            $countryId = $defaultCountry;
        }

        $address = new Address();

        $address->alias = 'LowestPriceDelivery';
        $address->firstname = 'Guest';
        $address->lastname = 'Guest';
        $address->address1 = 'Temporary';
        $address->postcode = '00000';
        $address->city = 'Temporary';
        $address->id_country = $countryId;

        if (Country::containsStates($countryId)) {
            $states = State::getStatesByIdCountry($countryId, true, 'name', 'ASC');
            if (empty($states)) {
                $states = State::getStatesByIdCountry($countryId, false, 'name', 'ASC');
            }
            if (!empty($states) && isset($states[0]['id_state'])) {
                $address->id_state = (int)$states[0]['id_state'];
            }
        }

        if (!$address->add()) {
            throw new \RuntimeException('Failed to create temporary address');
        }

        return $address;
    }

    private function cleanupCart(
        Cart $cart
    ): void {
        if ($cart->id_address_delivery) {

            $address = new Address(
                (int)$cart->id_address_delivery
            );

            if ($address->id) {
                $address->delete();
            }
        }

        if ($cart->id) {
            $cart->delete();
        }
    }

    private function buildCacheKey(
        int $idProduct,
        int $quantity,
        string $suffix = ''
    ): string {
        $cacheKey = self::CACHE_PREFIX
            . $idProduct
            . '_'
            . $quantity
            . '_'
            . $this->context->currency->id
            . '_'
            . $this->context->shop->id;

        if ($suffix !== '') {
            $cacheKey .= '_' . $suffix;
        }

        return $cacheKey;
    }

    private function getCustomerId(): int
    {
        if (
            isset($this->context->customer)
            && Validate::isLoadedObject(
                $this->context->customer
            )
        ) {
            return (int)$this->context->customer->id;
        }

        return 0;
    }

    private function getCurrencyId(): int
    {
        if (
            isset($this->context->currency)
            && Validate::isLoadedObject(
                $this->context->currency
            )
        ) {
            return (int)$this->context->currency->id;
        }

        return (int)Configuration::get(
            'PS_CURRENCY_DEFAULT'
        );
    }

    public function clearProductCache(
        int $idProduct
    ): void {
        Cache::clean(
            self::CACHE_PREFIX . $idProduct . '_*'
        );
    }
}
