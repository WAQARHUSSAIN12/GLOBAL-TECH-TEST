<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Purchase Records') }}
            </h2>
            <!-- Only show the creation link if the authenticated user passes the admin gate -->
            @can('access-admin')
                <a href="{{ url('/purchases/create') }}" class="inline-block bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 text-sm rounded shadow-sm no-underline">
                    + Add Line Item
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if($purchases->isEmpty())
                    <p class="text-gray-500 text-center py-4">No purchase entries recorded yet.</p>
                @else
                    <div class="space-y-6">
                        @foreach($purchases as $purchase)
                            <div class="border rounded-lg p-4 bg-gray-50 shadow-sm">
                                <div class="flex justify-between border-b pb-2 mb-3">
                                    <span class="text-sm font-bold text-gray-600">Purchase ID: #{{ $purchase->id }}</span>
                                    <span class="text-sm text-gray-500">{{ $purchase->created_at->format('M d, Y h:i A') }}</span>
                                </div>
                                <table class="w-full text-left text-sm">
                                    <thead>
                                        <tr class="text-gray-500 border-b">
                                            <th class="pb-1">Item</th>
                                            <th class="pb-1">Brand</th>
                                            <th class="pb-1">Qty</th>
                                            <th class="pb-1">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($purchase->items as $item)
                                            <tr>
                                                <td class="py-1 font-medium">{{ $item->item->name }}</td>
                                                <td class="py-1 text-gray-600">{{ $item->brand->name }}</td>
                                                <td class="py-1">{{ $item->qty }}</td>
                                                <td class="py-1">Rs. {{ number_format($item->price, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="text-right border-t pt-2 mt-2 font-bold text-gray-800">
                                    Total: Rs. {{ number_format($purchase->total, 2) }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>