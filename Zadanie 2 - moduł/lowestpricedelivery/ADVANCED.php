<?php
/**
 * Dokumentacja dla modułu Lowest Price Delivery.
 *
 * Moduł pokazuje najtańszą dostawę dla produktu na karcie produktu.
 * Pomija odbiory osobiste i wybiera jedną najtańszą opcję.
 */

/**
 * Przykład użycia w kodzie.
 */
/*
$context = Context::getContext();
$calculator = new DeliveryCalculator($context);

$idProduct = 42;
$lowestPrice = $calculator->getLowestPriceForProduct($idProduct);
$deliveryOptions = $calculator->getDeliveryOptionsForProduct($idProduct);

if ($lowestPrice !== null) {
    echo 'Najtańsza dostawa: ' . $lowestPrice . ' zł';
}

if (!empty($deliveryOptions)) {
    echo 'Przewoźnik: ' . $deliveryOptions[0]['carrier_name'];
}
*/

/**
 * Co robi moduł.
 *
 * 1. Tworzy tymczasowy koszyk.
 * 2. Dodaje wybrany produkt.
 * 3. Pobiera dostępne opcje dostawy.
 * 4. Pomija odbiory osobiste.
 * 5. Wybiera najtańszą opcję.
 * 6. Wyświetla komunikat na karcie produktu.
 */

/**
 * Hook używany przez moduł.
 *
 * Moduł używa hooka displayProductAdditionalInfo.
 * Jeśli chcesz dodać kolejny hook, zarejestruj go w metodzie install():
 *
 * $this->registerHook('displayProductPriceBlock');
 */

/**
 * Szablon widoku.
 *
 * Plik widoku znajduje się pod ścieżką:
 * modules/lowestpricedelivery/views/templates/hook/product_delivery_info.tpl
 */

/**
 * Debugowanie.
 *
 * Jeśli nie działa poprawnie:
 * 1. Włącz tryb debugowania w PrestaShop.
 * 2. Sprawdź logi w var/logs/.
 * 3. Upewnij się, że dla produktu są aktywni przewoźnicy i działają strefy dostawy.
 */

/**
 * Uwagi.
 *
 * - Moduł opiera się na aktualnej konfiguracji sklepu.
 * - Jeżeli nie ma żadnej sensownej opcji dostawy, nic nie wyświetla.
 * - Odbiory osobiste nie są uwzględniane w tej informacji.
 */
