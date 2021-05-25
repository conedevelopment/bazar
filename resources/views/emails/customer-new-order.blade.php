@component ('mail::message')

# Hello, {{ $order->address->name }}!

## Thank you for your order!

Your order has been received and now being processed. Your orders are shown below for your reference:
**Order #{{ $order->id }}**

# Order details

@component ('mail::table')
| Product | Quantity | Price |
|:--------|:--------:|------:|
@foreach ($order->products as $product)
| {{ $product->name }} | {{ $product->item->quantity }} | {{ $product->item->getFormattedPrice() }} |
@endforeach
| **Subtotal** || {{ Str::currency($order->items->sum('total'), $order->currency) }} |
@endcomponent

**Discount**: {{ $order->getFormattedDiscount() }}

**Shipping** (via {{ $order->shipping->driverName }}): {{ $order->shipping->getFormattedTotal() }}

**Tax**: {{ $order->getFormattedTax() }}

**Net. Total**: {{ $order->getFormattedNetTotal() }}

**Gross Total**: {{ $order->getFormattedTotal() }}

# Billing details

{{ $order->address->name }}<br>
{{ $order->address->phone }}<br>
{{ $order->address->email }}

{{ $order->address->address }}<br>
{{ $order->address->postcode }}
{{ $order->address->city }},
{{ $order->address->countryName }}

@endcomponent
