<table class="table table-sm is-headless mb-0 vertical-align-top">
    <tr>
        <th>{{ __('ID') }}:</th>
        <td>#{{ $order->id }}</td>
    </tr>
    <tr>
        <th>{{ __('Date') }}:</th>
        <td>{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
    </tr>
    <tr>
        <th>{{ __('Customer') }}:</th>
        <td>{{ $order->address->name }}</td>
    </tr>
    <tr>
        <th>{{ __('Tax') }}:</th>
        <td>{{ $order->formattedTax }}</td>
    </tr>
    <tr>
        <th>{{ __('Shipping') }}:</th>
        <td>
            {{ $order->shipping->formattedTotal }}
            ({{ $order->shipping->driverName }})
        </td>
    </tr>
    <tr>
        <th>{{ __('Discount') }}:</th>
        <td>{{ $order->formattedDiscount }}</td>
    </tr>
    <tr>
        <th>{{ __('Total') }}:</th>
        <td>{{ $order->formattedTotal }}</td>
    </tr>
    <tr>
        <th>{{ __('Billing Address') }}:</th>
        <td>
            <address>
                <div>{{ $order->address->name }}</div>
                @if ($order->address->company)
                    <div>{{ $order->address->company }}</div>
                @endif
                <div>{{ $order->address->address }}</div>
                @if ($order->address->address_second)
                    <div>{{ $order->address->address_second }}</div>
                @endif
                <div>
                    {{ $order->address->postcode }},
                    {{ $order->address->city }},
                    {{ $order->address->state }}
                </div>
                <div>{{ $order->address->countryName }}</div>
                <div>{{ $order->address->phone }}</div>
                <div>{{ $order->address->email }}</div>
            </address>
        </td>
    </tr>
    <tr>
        <th>{{ __('Shipping Address') }}:</th>
        <td>
            <address>
                <div>{{ $order->shipping->address->name }}</div>
                @if ($order->shipping->address->company)
                    <div>{{ $order->shipping->address->company }}</div>
                @endif
                <div>{{ $order->shipping->address->address }}</div>
                @if ($order->shipping->address->address_second)
                    <div>{{ $order->shipping->address->address_second }}</div>
                @endif
                <div>
                    {{ $order->shipping->address->postcode }},
                    {{ $order->shipping->address->city }},
                    {{ $order->shipping->address->state }}
                </div>
                <div>{{ $order->shipping->address->countryName }}</div>
                <div>{{ $order->shipping->address->phone }}</div>
                <div>{{ $order->shipping->address->email }}</div>
            </address>
        </td>
    </tr>
</table>
