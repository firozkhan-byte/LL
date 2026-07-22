<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ __('Enterprise POS Terminal') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Quick billing workspace with barcode support, loyalty accrual, split payment registers, and receipt layouts.') }}
            </p>
        </div>
        <div>
            <select wire:model.live="selectedWarehouseId" class="block rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm py-2">
                @foreach($warehouses as $wh)
                    <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->code }})</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left touch grid (Products list) -->
        <div class="lg:col-span-7 space-y-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <x-text-input wire:model.live="searchProduct" type="text" class="w-full text-sm py-2" placeholder="Search liquor items by name or SKU..." />
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @forelse($productsList as $p)
                    <button wire:click="addToCart('{{ $p->id }}')" class="bg-white dark:bg-gray-800 p-4 rounded-xl border dark:border-gray-700 text-left hover:shadow transition hover:border-indigo-500 cursor-pointer space-y-2 flex flex-col justify-between h-36">
                        <div>
                            <span class="px-1.5 py-0.5 bg-gray-50 dark:bg-gray-900 text-gray-500 text-[9px] rounded font-bold uppercase tracking-wider">{{ $p->liquor_type }}</span>
                            <div class="text-sm font-extrabold text-gray-900 dark:text-white mt-1 line-clamp-2">{{ $p->name }}</div>
                        </div>
                        <div class="flex justify-between items-center w-full">
                            <span class="text-xs text-gray-400 font-medium">{{ $p->volume_ml }}ml</span>
                            <span class="text-sm font-black text-indigo-600 dark:text-indigo-400">₹{{ number_format($p->selling_price, 2) }}</span>
                        </div>
                    </button>
                @empty
                    <div class="col-span-full bg-white dark:bg-gray-800 p-8 rounded-xl border dark:border-gray-700 text-center text-sm text-gray-500 italic">
                        {{ __('No active products found.') }}
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Cart Panel workspace -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Checkout Cart -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                <h3 class="text-md font-bold text-gray-900 dark:text-white border-b pb-2 dark:border-gray-700 flex justify-between">
                    <span>{{ __('Cart Workspace') }}</span>
                    <span class="text-xs text-gray-400">{{ count($cart) }} {{ __('Lines') }}</span>
                </h3>

                <div class="space-y-3 max-h-[220px] overflow-y-auto pr-1">
                    @forelse($cart as $index => $item)
                        <div class="flex justify-between items-center text-sm bg-gray-50 dark:bg-gray-900/40 p-2.5 rounded-lg border dark:border-gray-800">
                            <div>
                                <div class="font-extrabold text-gray-900 dark:text-white">{{ $item['name'] }}</div>
                                <div class="text-xs text-gray-400">₹{{ number_format($item['price'], 2) }}</div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="number" step="1" id="qty-{{ $index }}" value="{{ $item['quantity'] }}" class="w-16 rounded border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-xs py-1" />
                                <button onclick="var input=document.getElementById('qty-{{ $index }}'); @this.updateQuantity({{ $index }}, input.value)" class="text-xs text-indigo-600 font-bold cursor-pointer">Update</button>
                                <button wire:click="updateQuantity({{ $index }}, 0)" class="text-red-500 hover:text-red-700">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-sm text-gray-500 italic">{{ __('Cart is currently empty.') }}</div>
                    @endforelse
                </div>

                <!-- Customer Loyalty Lookup -->
                <div class="pt-4 border-t dark:border-gray-700 space-y-2">
                    <x-input-label for="customerPhone" :value="__('Loyalty Customer Phone Lookup')" />
                    <div class="flex space-x-2">
                        <x-text-input wire:model="customerPhone" id="customerPhone" class="text-xs py-1.5 flex-1" placeholder="e.g. 9876543210" />
                        <button type="button" wire:click="lookupCustomer" class="px-3 bg-gray-900 hover:bg-gray-800 text-white rounded text-xs font-semibold cursor-pointer">Lookup</button>
                    </div>
                    @if (session()->has('cust_message'))
                        <div class="text-[10px] text-green-600 font-bold">{{ session('cust_message') }}</div>
                    @elseif (session()->has('cust_error'))
                        <div class="text-[10px] text-red-500">{{ session('cust_error') }}</div>
                    @endif
                </div>

                <!-- Coupon Discount code box -->
                <div class="pt-4 border-t dark:border-gray-700 space-y-2">
                    <x-input-label for="couponCode" :value="__('Apply Discount Coupon')" />
                    <div class="flex space-x-2">
                        <x-text-input wire:model="couponCode" id="couponCode" class="text-xs py-1.5 flex-1" placeholder="e.g. LUCKY100" />
                        <button type="button" wire:click="applyCoupon" class="px-3 bg-gray-900 hover:bg-gray-800 text-white rounded text-xs font-semibold cursor-pointer">Apply</button>
                    </div>
                    @if (session()->has('discount_message'))
                        <div class="text-[10px] text-green-600 font-bold">{{ session('discount_message') }}</div>
                    @elseif (session()->has('discount_error'))
                        <div class="text-[10px] text-red-500">{{ session('discount_error') }}</div>
                    @endif
                </div>

                <!-- Gift Card field -->
                <div class="pt-4 border-t dark:border-gray-700 space-y-2">
                    <x-input-label for="giftCardNumber" :value="__('Redeem Gift Card Credit')" />
                    <div class="flex space-x-2">
                        <x-text-input wire:model="giftCardNumber" id="giftCardNumber" class="text-xs py-1.5 flex-1" placeholder="e.g. GC-CARD-999" />
                        <button type="button" wire:click="applyGiftCard" class="px-3 bg-gray-900 hover:bg-gray-800 text-white rounded text-xs font-semibold cursor-pointer">Redeem</button>
                    </div>
                    @if (session()->has('gc_message'))
                        <div class="text-[10px] text-green-600 font-bold">{{ session('gc_message') }}</div>
                    @elseif (session()->has('gc_error'))
                        <div class="text-[10px] text-red-500">{{ session('gc_error') }}</div>
                    @endif
                </div>

                <!-- Billing Summary Card -->
                <div class="pt-4 border-t dark:border-gray-700 space-y-2 text-xs">
                    <div class="flex justify-between text-gray-500">
                        <span>Cart Subtotal</span>
                        <span>₹{{ number_format($cartSubtotal, 2) }}</span>
                    </div>
                    @if($discountAmount > 0)
                        <div class="flex justify-between text-rose-500 font-bold">
                            <span>Coupon Discount</span>
                            <span>-₹{{ number_format($discountAmount, 2) }}</span>
                        </div>
                    @endif
                    @if($giftCardAppliedBalance > 0)
                        <div class="flex justify-between text-rose-500 font-bold">
                            <span>Gift Card Credit Applied</span>
                            <span>-₹{{ number_format($giftCardAppliedBalance, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-gray-500">
                        <span>GST Tax (18% inclusive simulation)</span>
                        <span>₹{{ number_format($taxAmount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm font-black border-t dark:border-gray-700 pt-2 text-gray-900 dark:text-white">
                        <span>Final Bill Amount</span>
                        <span>₹{{ number_format($cartFinal, 2) }}</span>
                    </div>
                </div>

                <button wire:click="openCheckout" class="w-full py-3 bg-indigo-600 text-white hover:bg-indigo-700 font-black rounded-lg text-sm transition cursor-pointer shadow">
                    {{ __('Complete Checkout Pay') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Split Payments checkout modal -->
    @if($showingCheckoutModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="$set('showingCheckoutModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border dark:border-gray-700">
                    <form wire:submit="saveCheckout">
                        <div class="px-6 py-4 border-b dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Log Split Payment Checkout') }}</h3>
                        </div>

                        <div class="p-6 space-y-4">
                            <div class="p-4 bg-indigo-50 dark:bg-indigo-950/20 text-indigo-700 dark:text-indigo-400 font-black text-center text-lg rounded-xl">
                                Total Bill Due: ₹{{ number_format($cartFinal, 2) }}
                            </div>

                            @if(session()->has('checkout_error'))
                                <div class="p-3 bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/50 text-red-700 text-xs rounded">
                                    {{ session('checkout_error') }}
                                </div>
                            @endif

                            <div>
                                <x-input-label for="cashPaid" :value="__('Cash Tendered')" />
                                <x-text-input wire:model="cashPaid" id="cashPaid" type="number" class="mt-1 block w-full text-sm" />
                            </div>
                            <div>
                                <x-input-label for="upiPaid" :value="__('UPI Payment amount')" />
                                <x-text-input wire:model="upiPaid" id="upiPaid" type="number" class="mt-1 block w-full text-sm" />
                            </div>
                            <div>
                                <x-input-label for="cardPaid" :value="__('Card Terminal amount')" />
                                <x-text-input wire:model="cardPaid" id="cardPaid" type="number" class="mt-1 block w-full text-sm" />
                            </div>
                            @if($giftCardAppliedBalance > 0)
                                <div>
                                    <x-input-label for="giftCardPaid" :value="__('Gift Card balance applied')" />
                                    <x-text-input wire:model="giftCardPaid" id="giftCardPaid" type="number" class="mt-1 block w-full text-sm bg-gray-50 cursor-not-allowed" readonly />
                                </div>
                            @endif
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 border-t dark:border-gray-700">
                            <x-primary-button type="submit">{{ __('Finalize Payment') }}</x-primary-button>
                            <x-secondary-button wire:click="$set('showingCheckoutModal', false)">{{ __('Cancel') }}</x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Receipt Print preview modal -->
    @if($showingReceiptModal && $receiptDetails)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="$set('showingReceiptModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-850 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-sm sm:w-full border dark:border-gray-700 p-6 space-y-4">
                    
                    <!-- Paper invoice style -->
                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-700 p-4 space-y-4 font-mono text-xs text-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-900">
                        <div class="text-center space-y-1">
                            <h2 class="text-sm font-extrabold uppercase tracking-widest text-gray-950 dark:text-white">{{ __('Living Liquidz POS') }}</h2>
                            <p>{{ __('Corporate Nariman Point, Mumbai') }}</p>
                            <p>GSTIN: 27AAACL3045F1Z9</p>
                        </div>

                        <div class="border-t border-dashed dark:border-gray-700 pt-2 space-y-1">
                            <div>Invoice: <span class="font-bold">{{ $receiptDetails['invoice_number'] }}</span></div>
                            <div>Date: <span>{{ $receiptDetails['date'] }}</span></div>
                            <div>Client: <span>{{ $receiptDetails['customer_name'] }}</span></div>
                        </div>

                        <div class="border-t border-dashed dark:border-gray-700 pt-2 space-y-1.5">
                            <div class="flex justify-between font-bold border-b border-dashed dark:border-gray-700 pb-1">
                                <span>Item description</span>
                                <span>Total price</span>
                            </div>
                            @foreach($receiptDetails['items'] as $item)
                                <div class="flex justify-between">
                                    <span>{{ $item['name'] }} (x{{ $item['quantity'] }})</span>
                                    <span>₹{{ number_format($item['quantity'] * $item['price'], 2) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-dashed dark:border-gray-700 pt-2 space-y-1 text-right">
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span>₹{{ number_format($receiptDetails['subtotal'], 2) }}</span>
                            </div>
                            @if($receiptDetails['discount'] > 0)
                                <div class="flex justify-between text-rose-600">
                                    <span>Discounts:</span>
                                    <span>-₹{{ number_format($receiptDetails['discount'], 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between font-extrabold text-sm border-t border-dashed dark:border-gray-700 pt-1 text-gray-900 dark:text-white">
                                <span>GRAND TOTAL:</span>
                                <span>₹{{ number_format($receiptDetails['total'], 2) }}</span>
                            </div>
                        </div>

                        <div class="text-center pt-4 border-t border-dashed dark:border-gray-700 text-[10px]">
                            <p class="font-bold">{{ __('Thank you for shopping!') }}</p>
                            <p>{{ __('Please keep physical receipt for returns.') }}</p>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button onclick="window.print()" class="flex-1 py-2 bg-gray-950 text-white rounded-lg text-xs font-bold hover:bg-gray-800 transition cursor-pointer text-center">
                            {{ __('Print Receipt') }}
                        </button>
                        <button wire:click="$set('showingReceiptModal', false)" class="flex-1 py-2 bg-gray-200 text-gray-800 dark:bg-gray-750 dark:text-white rounded-lg text-xs font-bold hover:bg-gray-300 dark:hover:bg-gray-700 transition cursor-pointer text-center">
                            {{ __('Close') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
