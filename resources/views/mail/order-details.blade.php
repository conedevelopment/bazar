<x-mail::message>
# Hello, {{ $order->address->name }}!

## Thank you for your order!

Your order has been received and now being processed. Your orders are shown below for your reference:
**Order #{{ $order->getKey() }}**

# Order details

<x-mail::table>
| Product | Quantity | Tax | Price |
|:--------|:--------:|:---:|:-----:|
@foreach ($order->items as $item)
| {{ $item->name }} | {{ $item->quantity }} | {{ $item->formattedTax }} | {{ $item->formattedPrice }} |
@endforeach
| **Subtotal** ||| {{ $order->formattedSubtotal }} |
</x-mail::table>

**Shipping** ({{ $order->shipping->driverName }}): {{ $order->shipping->formattedTotal }}

**Tax**: {{ $order->formattedTax }}

**Subtotal**: {{ $order->formattedSubtotal }}

**Gross Total**: {{ $order->formattedTotal }}

# Billing details

{{ $order->address->name }}<br>
{{ $order->address->phone }}<br>
{{ $order->address->email }}

{{ $order->address->address }}<br>
{{ $order->address->postcode }}
{{ $order->address->city }},
{{ $order->address->countryName }}
</x-mail::message>
