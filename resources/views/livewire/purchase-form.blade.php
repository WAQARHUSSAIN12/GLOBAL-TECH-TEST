<div>
     
    <?php

    use Livewire\Component;
    use App\Models\Item;
    use App\Models\Brand;
    use App\Models\Purchase;
    use Illuminate\Support\Facades\DB;

    new class extends Component
    {
        public array $rows = [];
        public float $total = 0.00;

        public function mount()
        {
            $this->addRow();
        }

        public function addRow()
        {
            $this->rows[] = ['item_id' => '', 'brand_id' => '', 'qty' => 1, 'price' => 0];
            $this->checkForDuplicatesAndCalculate();
        }

        public function removeRow($index)
        {
            unset($this->rows[$index]);
            $this->rows = array_values($this->rows);
            $this->checkForDuplicatesAndCalculate();
        }

        public function with(): array
        {
            return [
                'availableItems' => Item::all(),
                'availableBrands' => Brand::all(),
            ];
        }

        public function rules()
        {
            return [
                'rows' => 'required|array|min:1',
                'rows.*.item_id' => 'required|exists:items,id',
                'rows.*.brand_id' => 'required|exists:brands,id',
                'rows.*.qty' => 'required|integer|min:1',
                'rows.*.price' => 'required|numeric|min:0.01',
            ];
        }

        protected $messages = [
            'rows.*.item_id.required' => 'Select an item.',
            'rows.*.brand_id.required' => 'Select a brand.',
            'rows.*.qty.min' => 'Qty must be ≥ 1.',
            'rows.*.price.min' => 'Price must be > 0.',
        ];

        public function updatedRows()
        {
            $this->checkForDuplicatesAndCalculate();
        }

        public function checkForDuplicatesAndCalculate()
        {
            $combinations = [];
            $calculatedTotal = 0;
            $this->resetErrorBag();

            foreach ($this->rows as $index => $row) {
                $qty = (int)($row['qty'] ?? 0);
                $price = (float)($row['price'] ?? 0);
                $calculatedTotal += ($qty * $price);

                if (!empty($row['item_id']) && !empty($row['brand_id'])) {
                    $comboKey = $row['item_id'] . '-' . $row['brand_id'];
                    if (in_array($comboKey, $combinations)) {
                        $this->addError("rows.{$index}.duplicate", "This Item & Brand combo is already added below.");
                    } else {
                        $combinations[] = $comboKey;
                    }
                }
            }

            $this->total = $calculatedTotal;
            
            if (count($this->rows) > 0 && !empty($this->rows[0]['item_id'])) {
                $this->validate();
            }
        }

        public function savePurchase()
        {
            if (!auth()->user() || !auth()->user()->is_admin) {
                abort(403, 'Unauthorized action.');
            }

            $this->checkForDuplicatesAndCalculate();
            if ($this->getErrorBag()->any()) return;

            DB::transaction(function () {
                $purchase = Purchase::create(['total' => $this->total,
                'user_id' => auth()->id(),]);

                foreach ($this->rows as $row) {
                    $purchase->items()->create([
                        'item_id'  => $row['item_id'],
                        'brand_id' => $row['brand_id'],
                        'qty'      => $row['qty'],
                        'price'    => $row['price'],
                    ]);
                }
            });

            session()->flash('message', 'Purchase Entry saved securely using DB Transactions!');
            $this->rows = [];
            $this->addRow();
            $this->total = 0;
        }
    };
    ?>
   

    <div class="p-6 bg-white rounded-lg shadow-md max-w-5xl mx-auto">
        <h2 class="text-2xl font-bold mb-4 text-gray-800 border-b pb-2">Purchase Entry</h2>

        @if (session()->has('message'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-md shadow-sm">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="savePurchase" class="space-y-4">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-700 uppercase text-xs tracking-wider border-b">
                            <th class="p-3">Item</th>
                            <th class="p-3">Brand</th>
                            <th class="p-3 w-24">Qty</th>
                            <th class="p-3 w-32">Unit Price</th>
                            <th class="p-3 w-32">Line Total</th>
                            <th class="p-3 w-16 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $index => $row)
                            <tr class="border-b hover:bg-gray-50 transition-colors" wire:key="row-{{ $index }}">
                                
                                <!-- Item Picker -->
                                <td class="p-2">
                                    <select wire:model.live="rows.{{ $index }}.item_id" class="w-full border rounded p-1.5 bg-white text-sm">
                                        <option value="">-- Choose Item --</option>
                                        @foreach($availableItems as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    @error("rows.{$index}.item_id") <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                                </td>

                                <!-- Brand Picker -->
                                <td class="p-2">
                                    <select wire:model.live="rows.{{ $index }}.brand_id" class="w-full border rounded p-1.5 bg-white text-sm">
                                        <option value="">-- Choose Brand --</option>
                                        @foreach($availableBrands as $brand)
                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                        @endforeach
                                    </select>
                                    @error("rows.{$index}.brand_id") <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                                    @error("rows.{$index}.duplicate") <div class="text-red-600 font-semibold text-xs mt-1">{{ $message }}</div> @enderror
                                </td>

                                <!-- Qty Input -->
                                <td class="p-2">
                                    <input type="number" wire:model.live="rows.{{ $index }}.qty" class="w-full border rounded p-1.5 text-sm" min="1">
                                    @error("rows.{$index}.qty") <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                                </td>

                                <!-- Price Input -->
                                <td class="p-2">
                                    <input type="number" step="0.01" wire:model.live="rows.{{ $index }}.price" class="w-full border rounded p-1.5 text-sm">
                                    @error("rows.{$index}.price") <div class="text-red-500 text-xs mt-1">{{ $message }}</div> @enderror
                                </td>

                                <!-- Line Valuation Display -->
                                <td class="p-2 font-mono text-sm align-middle text-gray-600">
                                    Rs. {{ number_format(((int)($row['qty'] ?? 0) * (float)($row['price'] ?? 0)), 2) }}
                                </td>

                                <!-- Action Remove Trigger -->
                                <td class="p-2 text-center">
                                    @if(count($rows) > 1)
                                        <button type="button" wire:click="removeRow({{ $index }})" class="text-red-500 hover:text-red-700 font-bold text-lg">
                                            &times;
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-between items-center pt-4 border-t">
                <button type="button" wire:click="addRow" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 text-sm rounded shadow-sm">
                    + Add Line Item
                </button>
                <div class="text-right">
                    <span class="text-gray-500 uppercase tracking-wide text-xs block">Grand Total Valuation</span>
                    <span class="text-2xl font-mono font-bold text-gray-900">Rs. {{ number_format($total, 2) }}</span>
                </div>
            </div>

            <div class="pt-2 text-right">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded font-semibold shadow">
                    Finalize Purchase
                </button>
            </div>
        </form>
    </div>
</div>