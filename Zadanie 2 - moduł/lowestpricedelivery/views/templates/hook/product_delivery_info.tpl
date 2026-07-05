{if isset($lowest_delivery_options) && $lowest_delivery_options|@count}
  {assign var=option value=$lowest_delivery_options[0]}
  <div class="product-delivery-info">
    <strong>{l s='Najniższy koszt dostawy:' mod='lowestpricedelivery'}</strong>
    <span>
      {if $option.is_free}
        {l s='Darmowa dostawa' mod='lowestpricedelivery'}
      {else}
        {$option.price_label|escape:'htmlall':'UTF-8'}
      {/if}
      {if $option.carrier_name}
        <span> ({$option.carrier_name|escape:'htmlall':'UTF-8'})</span>
      {/if}
    </span>
  </div>
{/if}

{if isset($pickup_delivery_info) && $pickup_delivery_info}
  <div class="product-delivery-pickup-info">
    <small>{$pickup_delivery_info|escape:'htmlall':'UTF-8'}</small>
  </div>
{/if}
  