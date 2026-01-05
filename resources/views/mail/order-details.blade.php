<x-mail::message>
# {{ __('Hello') }}, {{ $order->address->name }}!

## {{ __('Thank you for your order!') }}

{{ __('Your order has been received and now being processed.') }}
{{ __('Your orders are shown below for your reference: #:order', ['order' => $order->getKey()]) }}

# {{ __('Order details') }}

<x-mail::table>
| {{ __('Product') }} | {{ __('Quantity') }} | {{ __('Price') }} |
|:--------|:--------:|:-----:|
@foreach ($order->items as $item)
| {{ $item->getName() }} | {{ $item->getQuantity() }} | {{ $item->getFormattedPrice() }} |
@endforeach
| **{{ __('Subtotal') }}** || {{ $order->getFormattedSubtotal() }} |
</x-mail::table>

**{{ __('Discount') }}**: {{ $order->getFormattedTotalDiscount() }}

@if($order->needsShipping())
**{{ __('Shipping') }}** ({{ $order->shipping->driverName }}): {{ $order->shipping->formattedTotal }}
@endif

**{{ __('Tax') }}**: {{ $order->getFormattedTax() }}

**{{ __('Subtotal') }}**: {{ $order->getFormattedSubtotal() }}

**{{ __('Gross Total') }}**: {{ $order->getFormattedTotal() }}

# {{ __('Billing details') }}

{{ $order->address->name }}<br>
{{ $order->address->phone }}<br>
{{ $order->address->email }}

{{ $order->address->address }}<br>
{{ $order->address->postcode }}
{{ $order->address->city }},
{{ $order->address->countryName }}
</x-mail::message>
