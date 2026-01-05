<x-mail::message>
# {{ __('Hello') }}, {{ $order->address->name }}!

## {{ __('Thank you for your order!') }}

{{ __('Your order has been received and now being processed.') }}
{{ __('Your orders are shown below for your reference: #:order', ['order' => $order->getKey()]) }}

# {{ __('Order details') }}

<x-mail::table>
| {{ __('Product') }} | {{ __('Quantity') }} | {{ __('Amount') }} |
|:--------|:--------:|:-----:|
@foreach ($order->getItems() as $item)
| {{ $item->getName() }} | {{ $item->getQuantity() }} | {{ $item->getFormattedPrice() }} |
@endforeach
@if($order->needsShipping())
| {{ $order->shipping->driverName }} || {{ $order->shipping->getFormattedSubtotal() }} |
@endif
| **{{ __('Subtotal') }}** || {{ $order->getFormattedSubtotal() }} |
| **{{ __('Tax') }}** || {{ $order->getFormattedTax() }} |
| **{{ __('Discount') }}** || {{ $order->getFormattedDiscountTotal() }} |
| **{{ __('Total') }}** || {{ $order->getFormattedTotal() }} |
</x-mail::table>

# {{ __('Billing details') }}

{{ $order->address->name }}<br>
{{ $order->address->phone }}<br>
{{ $order->address->email }}

{{ $order->address->address }}<br>
{{ $order->address->postcode }}
{{ $order->address->city }},
{{ $order->address->countryName }}
</x-mail::message>
