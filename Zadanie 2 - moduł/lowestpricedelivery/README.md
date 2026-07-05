# Moduł Najniższy koszt dostawy (Lowest Price Delivery)

## Opis

Moduł `lowestpricedelivery` dla PrestaShop'a wyświetla najniższy możliwy koszt dostawy dla produktu na karcie produktu. Obliczenia uwzględniają wiele warunków dostępnych w systemie PrestaShop.

## Wersje PrestaShop'a

- PrestaShop 1.7.x
- PrestaShop 8.0.x
- PrestaShop 9.0.x

## Cechy

Moduł bierze pod uwagę następujące warunki:

### 1. **Nośniki dostawy (Carriers)**
- Automatycznie pobiera wszystkie aktywne nośniki
- Obsługuje nośniki darmowe (free carriers)

### 2. **Waga produktu**
- Oblicza zakres wagowy (weight range) dla każdego nośnika
- Uwzględnia minimalną i maksymalną wagę

### 3. **Cena produktu**
- Oblicza zakres cenowy (price range) dla dostawy
- Bierze pod uwagę różne ceny w zależności od wartości zakupu

### 4. **Strefy dostawy (Zones)**
- Automatycznie określa aktualną strefę dostawy
- Pobiera informacje z wybranego kraju dostawy

### 5. **Kraj dostawy**
- Pobiera informacje z adresu dostawy w koszyku
- Jeśli brak - używa kraju domyślnego

### 6. **Reguły dostawy (Delivery Rules)**
- Uwzględnia reguły wagowe definiowane dla nośników
- Obsługuje przedziały wagowe z konkretnymi cenami

### 7. **Kombinacje produktów (Combinations)**
- Oblicza cenę dla najmniejszej ceny kombinacji
- Stosuje odpowiednie zakresy cenowe

### 8. **Opłata za obsługę (Shipping Handling)**
- Dodaje konfiguracyjną opłatę za obsługę dostawy
- Pobiera wartość z konfiguracji PrestaShop'a

### 9. **Podatek od dostawy**
- Automatycznie stosuje podatek VAT jeśli jest wymagany
- Używa reguły podatkowe dla dostawy

### 10. **Zachowanie zakresu (Range Behavior)**
- Obsługuje różne typy zakresu dostawy (price/weight)
- Bierze pod uwagę ustawienia zachowania zakresu

## Instalacja

1. Pobierz moduł
2. Umieść folder `lowestpricedelivery` w katalogu `modules/` Prestashopa
3. Zaloguj się do panelu administracyjnego
4. Przejdź do **Moduły** → **Zarządzaj modułami**
5. Wyszukaj "Najniższy koszt dostawy"
6. Kliknij **Zainstaluj**

## Jak to działa

1. Moduł rejestruje się w hakie `displayProductPriceBlock`
2. Na karcie produktu wyświetla najniższy koszt dostawy
3. Obliczenia odbywają się w oparciu o:
   - Aktywnie ustawiony kraj dostawy
   - Wagę produktu
   - Cenę produktu
   - Wszystkie dostępne nośniki dostawy

## Wyświetlanie

Informacja o najniższej cenie dostawy pojawia się na karcie produktu (strona produktu) w sekcji ceny, zawierając:
- Najmniejszą możliwą cenę dostawy
- Nazwę nośnika oferującego tę cenę

## Przykład wyjścia

```
Najniższa możliwa dostawa: 5,00 zł (DPD)
```

## Struktura bazy danych

Moduł odczytuje dane z następujących tabel:

- `ps_carrier` - informacje o nośnikach
- `ps_range_price` - zakresy cenowe
- `ps_range_weight` - zakresy wagowe
- `ps_carrier_range` - przepisania nośników
- `ps_delivery` - reguły dostawy
- `ps_zone_country` - mapowanie krajów do stref
- `ps_product_attribute` - dane kombinacji

## Zmienne konfiguracyjne

- `PS_COUNTRY_DEFAULT` - domyślny kraj
- `PS_ZONE_DEFAULT_DELIVERY` - domyślna strefa dostawy
- `PS_SHIPPING_HANDLING` - opłata za obsługę
- `PS_SHIPPING_TAX` - czy stosować podatek
- `PS_SHIPPING_TAX_RULES_GROUP` - grupa reguł podatkowych

## Zależności

- Klasa `Context` z PrestaShop
- Klasa `Db` z PrestaShop
- Klasa `Configuration` z PrestaShop
- Klasa `Product` z PrestaShop
- Klasa `Address` z PrestaShop
- Klasa `TaxManagerFactory` z PrestaShop

## Notatki

- Moduł oblicza najniższą możliwą cenę dostawy
- Jeśli żaden nośnik nie jest dostępny, informacja nie jest wyświetlana
- Cena jest wyświetlana w walucie aktualnie ustawionej w Prestashopie
- Moduł jest responsywny i dostosowuje się do urządzeń mobilnych

## Troubleshooting

Jeśli moduł nie pokazuje informacji:
1. Sprawdź czy moduł jest zainstalowany i aktywny
2. Sprawdź czy masz zdefiniowane nośniki dostawy
3. Sprawdź czy nośniki mają ustawione zakresy dostawy
4. Przeanalizuj logi PrestaShop (jeśli dostępne)

## Licencja

Open Software License (OSL 3.0)

## Wsparcie

Aby zgłosić błędy lub zasugerować ulepszenia, skontaktuj się z zespołem wsparcia.
