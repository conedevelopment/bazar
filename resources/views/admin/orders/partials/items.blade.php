<div class="table-responsive">
    <table class="table table-hover has-filled-header mb-0">
        <thead>
            <tr>
                <th scope="col">{{ __('Name') }}</th>
                <th scope="col">
                    {{ __('Price') }}
                    <span class="badge badge-light text-uppercase">{{ $order->currency }}</span>
                </th>
                <th scope="col">
                    {{ __('Tax') }}
                    <span class="badge badge-light text-uppercase">{{ $order->currency }}</span>
                </th>
                <th scope="col">{{ __('Qty') }}</th>
                <th scope="col">
                    {{ __('Total') }}
                    <span class="badge badge-light text-uppercase">{{ $order->currency }}</span>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->item->price }}</td>
                    <td>{{ $product->item->tax }}</td>
                    <td>{{ $product->item->quantity }}</td>
                    <td>{{ $product->item->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
