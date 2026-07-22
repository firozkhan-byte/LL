<div class="p-6 max-w-7xl mx-auto space-y-6">
    @if($product->parent_id)
        <div class="p-3 bg-indigo-50 dark:bg-indigo-950/20 border-l-4 border-indigo-500 rounded-lg flex justify-between items-center text-xs text-indigo-800 dark:text-indigo-300 font-semibold">
            <span>This is a variant size of the main product: <a href="{{ route('admin.products.detail', $product->parent_id) }}" class="underline hover:text-indigo-900">{{ $product->parent->name }}</a></span>
            <a href="{{ route('admin.products.detail', $product->parent_id) }}" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-bold">View Parent Template</a>
        </div>
    @endif
    <!-- Breadcrumbs & Navigation -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="space-y-1">
            <nav class="flex text-sm text-gray-500 dark:text-gray-400 space-x-2">
                <a href="{{ route('admin.products') }}" class="hover:text-indigo-600 transition">{{ __('Product Catalog') }}</a>
                <span>&bull;</span>
                <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $product->name }}</span>
            </nav>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
                {{ $product->name }}
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                    @if($product->status === 'active') bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-300
                    @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 @endif">
                    {{ $product->status }}
                </span>
                @if($product->trashed())
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800 uppercase tracking-wider">
                        {{ __('Deleted') }}
                    </span>
                @endif
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                SKU: <span class="font-mono font-bold">{{ $product->sku }}</span> &bull; 
                HSN: <span class="font-mono font-bold">{{ $product->hsn_code ?? __('N/A') }}</span>
            </p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.products') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer">
                &larr; {{ __('Back') }}
            </a>
            
            <button wire:click="toggleStatus" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer">
                {{ $product->status === 'active' ? __('Mark Inactive') : __('Mark Active') }}
            </button>
            
            @if($product->trashed())
                <button wire:click="restoreProduct" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer">
                    {{ __('Restore Product') }}
                </button>
            @else
                <button wire:click="openEditModal" class="inline-flex items-center px-4 py-2 bg-indigo-650 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer">
                    {{ __('Edit Profile') }}
                </button>
                <button wire:click="deleteProduct" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer">
                    {{ __('Delete') }}
                </button>
            @endif
        </div>
    </div>

    <!-- System message notifications -->
    @if (session()->has('message'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border-l-4 border-green-500 rounded-lg flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Main Content Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- LEFT PANEL: Image, Barcode & Specifications -->
        <div class="space-y-6 lg:col-span-1">
            <!-- Product Images Gallery -->
            <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 space-y-4">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">{{ __('Product Media') }}</h3>
                <div class="h-64 w-full rounded-lg bg-gray-50 dark:bg-gray-900 border dark:border-gray-800 flex items-center justify-center overflow-hidden relative">
                    @if($product->primaryImage())
                        <img src="{{ Storage::url($product->primaryImage()->image_path) }}" class="h-full w-full object-contain" alt="{{ $product->name }}" />
                    @else
                        <div class="flex flex-col items-center justify-center text-gray-400 space-y-2">
                            <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-xs">{{ __('No Images Added') }}</span>
                        </div>
                    @endif
                </div>
                
                @if($product->images->count() > 1)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach($product->images as $img)
                            <div class="h-16 rounded border bg-gray-50 dark:bg-gray-900 overflow-hidden cursor-pointer hover:border-indigo-500 transition">
                                <img src="{{ Storage::url($img->image_path) }}" class="h-full w-full object-cover" alt="Thumb" />
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Barcode & QR code mock -->
            <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">{{ __('Identifier & Labels') }}</h3>
                    <button onclick="navigator.clipboard.writeText('{{ $product->sku }}')" class="text-xs text-indigo-600 hover:text-indigo-800 cursor-pointer">{{ __('Copy SKU') }}</button>
                </div>
                <div class="flex flex-col items-center justify-center p-4 border rounded-lg bg-gray-50 dark:bg-gray-900 dark:border-gray-850 space-y-2">
                    <!-- Barcode display -->
                    <div class="flex flex-col items-center">
                        <div class="flex space-x-0.5 h-12 items-end">
                            @for ($i = 0; $i < 30; $i++)
                                <div class="bg-gray-900 dark:bg-gray-100" style="width: {{ rand(1, 4) }}px; height: {{ rand(30, 48) }}px;"></div>
                            @endfor
                        </div>
                        <span class="text-[10px] font-mono mt-1 text-gray-600 dark:text-gray-400">{{ $product->barcode ?? $product->sku }}</span>
                    </div>
                </div>
            </div>

            <!-- Spec Sheets details -->
            <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 space-y-4">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">{{ __('Specifications') }}</h3>
                <div class="divide-y divide-gray-150 dark:divide-gray-700 text-sm">
                    <div class="py-2.5 flex justify-between">
                        <span class="text-gray-450">{{ __('Liquor Type') }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $product->liquor_type }}</span>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <span class="text-gray-450">{{ __('Volume (Size)') }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $product->volume_ml }} ml</span>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <span class="text-gray-450">{{ __('Alcohol By Volume (ABV)') }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $product->alcohol_percentage }}%</span>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <span class="text-gray-450">{{ __('Origin') }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white">
                            {{ $product->origin_country ?: __('N/A') }} 
                            @if($product->origin_region) ({{ $product->origin_region }}) @endif
                        </span>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <span class="text-gray-450">{{ __('Manufacturer') }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $product->manufacturer->name ?? __('N/A') }}</span>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <span class="text-gray-450">{{ __('GST Rate') }}</span>
                        <span class="font-semibold text-gray-900 dark:text-white">{{ $product->gst_rate }}%</span>
                    </div>
                    <div class="py-2.5 flex justify-between">
                        <span class="text-gray-450">{{ __('Tracking Modes') }}</span>
                        <div class="flex space-x-1">
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $product->batch_tracking ? 'bg-blue-50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-300' : 'bg-gray-100 text-gray-400' }}">{{ __('Batch') }}</span>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $product->expiry_tracking ? 'bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-300' : 'bg-gray-100 text-gray-400' }}">{{ __('Expiry') }}</span>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase {{ $product->serial_tracking ? 'bg-purple-50 dark:bg-purple-950/20 text-purple-700 dark:text-purple-300' : 'bg-gray-100 text-gray-400' }}">{{ __('Serial') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Excise Brand Registration Card -->
            <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 space-y-4">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">{{ __('Excise Brand Registrations') }}</h3>
                <div class="divide-y divide-gray-150 dark:divide-gray-700 text-sm">
                    @if($product->brand && $product->brand->registrations->count() > 0)
                        @foreach($product->brand->registrations as $reg)
                            <div class="py-2.5 flex flex-col space-y-1">
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $reg->state }}</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                        @if($reg->status === 'active' && $reg->expiry_date->isFuture()) bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-300
                                        @else bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-300 @endif">
                                        {{ $reg->status }}
                                    </span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 font-mono">
                                    <span>Code: {{ $reg->excise_code }}</span>
                                    <span>Exp: {{ $reg->expiry_date->format('Y-m-d') }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="py-4 text-center text-xs text-gray-500 italic">
                            {{ __('No state-wise registrations logged for this brand.') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Tabs Content -->
        <div class="space-y-6 lg:col-span-2">
            <!-- Tabs Menu -->
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="flex space-x-4 flex-wrap" aria-label="Tabs">
                    <button wire:click="changeTab('stock')" class="px-3 py-2.5 font-bold text-sm border-b-2 transition cursor-pointer {{ $activeTab === 'stock' ? 'border-indigo-600 text-indigo-650 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 hover:border-gray-300' }}">
                        {{ __('Stock Inventory') }}
                    </button>
                    @if(!$product->parent_id)
                        <button wire:click="changeTab('variants')" class="px-3 py-2.5 font-bold text-sm border-b-2 transition cursor-pointer {{ $activeTab === 'variants' ? 'border-indigo-600 text-indigo-650 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 hover:border-gray-300' }}">
                            {{ __('Bottle Sizes (Variants)') }}
                        </button>
                    @endif
                    <button wire:click="changeTab('pricing')" class="px-3 py-2.5 font-bold text-sm border-b-2 transition cursor-pointer {{ $activeTab === 'pricing' ? 'border-indigo-600 text-indigo-650 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 hover:border-gray-300' }}">
                        {{ __('Pricing & Finance') }}
                    </button>
                    <button wire:click="changeTab('ledger')" class="px-3 py-2.5 font-bold text-sm border-b-2 transition cursor-pointer {{ $activeTab === 'ledger' ? 'border-indigo-600 text-indigo-650 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 hover:border-gray-300' }}">
                        {{ __('Ledger History') }}
                    </button>
                    <button wire:click="changeTab('analytics')" class="px-3 py-2.5 font-bold text-sm border-b-2 transition cursor-pointer {{ $activeTab === 'analytics' ? 'border-indigo-600 text-indigo-650 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 hover:border-gray-300' }}">
                        {{ __('Sales Analytics') }}
                    </button>
                    <button wire:click="changeTab('logs')" class="px-3 py-2.5 font-bold text-sm border-b-2 transition cursor-pointer {{ $activeTab === 'logs' ? 'border-indigo-600 text-indigo-650 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 hover:border-gray-300' }}">
                        {{ __('Audit Trail') }}
                    </button>
                </nav>
            </div>

            <!-- Tab Panels -->
            <div>
                <!-- TAB: Product Variants -->
                @if($activeTab === 'variants' && !$product->parent_id)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 space-y-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Configured Sizes & Packaging') }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Define variant packaging sizes for this product template.') }}</p>
                            </div>
                            <button wire:click="openVariantModal" class="inline-flex items-center px-3 py-1.5 bg-indigo-655 hover:bg-indigo-700 text-white rounded-lg text-xs font-semibold shadow cursor-pointer">
                                {{ __('Add Bottle Size') }}
                            </button>
                        </div>
                        
                        <div class="overflow-x-auto border rounded-lg dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                                <thead class="bg-gray-50 dark:bg-gray-900/50">
                                    <tr>
                                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Variant Name') }}</th>
                                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('SKU') }}</th>
                                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase text-right">{{ __('Volume') }}</th>
                                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase text-right">{{ __('Selling Price') }}</th>
                                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase text-right">{{ __('MRP') }}</th>
                                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase text-right">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-150 dark:divide-gray-700 text-xs">
                                    @forelse($product->variants as $variant)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-750/30 transition">
                                            <td class="px-4 py-3.5 font-bold text-gray-950 dark:text-white">{{ $variant->name }}</td>
                                            <td class="px-4 py-3.5 text-xs text-gray-500 font-mono">{{ $variant->sku }}</td>
                                            <td class="px-4 py-3.5 text-right font-semibold">{{ $variant->volume_ml }} ml</td>
                                            <td class="px-4 py-3.5 text-right font-black text-indigo-600">₹{{ number_format($variant->selling_price, 2) }}</td>
                                            <td class="px-4 py-3.5 text-right text-gray-500">₹{{ number_format($variant->mrp, 2) }}</td>
                                            <td class="px-4 py-3.5 text-right font-bold">
                                                <a href="{{ route('admin.products.detail', $variant->id) }}" class="text-indigo-650 hover:text-indigo-900">
                                                    {{ __('View Dashboard') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 italic">
                                                {{ __('No variant sizes configured for this product template yet.') }}
                                              </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- TAB 1: Stock levels across warehouses -->
                @if($activeTab === 'stock')
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden space-y-4 p-5">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Warehouse & Store Inventory Levels') }}</h3>
                        <div class="overflow-x-auto border rounded-lg dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                                <thead class="bg-gray-50 dark:bg-gray-900/50">
                                    <tr>
                                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Warehouse / Location') }}</th>
                                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Code') }}</th>
                                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Active Batch') }}</th>
                                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase text-right">{{ __('Stock Level') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-150 dark:divide-gray-700 text-sm">
                                    @php $totalStock = 0; @endphp
                                    @forelse($this->warehouseStocks as $stock)
                                        @php $totalStock += $stock['available_qty']; @endphp
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-750/30 transition">
                                            <td class="px-4 py-3.5 font-semibold text-gray-900 dark:text-white">{{ $stock['warehouse']->name }}</td>
                                            <td class="px-4 py-3.5 text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $stock['warehouse']->code }}</td>
                                            <td class="px-4 py-3.5 text-xs text-gray-500 dark:text-gray-400 font-mono">
                                                @if($stock['batch_number'])
                                                    <span>{{ $stock['batch_number'] }}</span>
                                                    @if($stock['expiry_date'])
                                                        <span class="block text-[10px] text-amber-500">Exp: {{ $stock['expiry_date'] }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400 italic">{{ __('None') }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3.5 text-right font-black text-sm">
                                                @if($stock['available_qty'] > 10)
                                                    <span class="text-green-600 dark:text-green-400">{{ number_format($stock['available_qty']) }} Units</span>
                                                @elseif($stock['available_qty'] > 0)
                                                    <span class="text-amber-500 font-bold">{{ number_format($stock['available_qty']) }} Units (Low)</span>
                                                @else
                                                    <span class="text-red-500 italic font-medium">{{ __('Out of Stock') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 italic">
                                                {{ __('No inventory logged for this product.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-gray-900/50 border-t font-black">
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-sm text-gray-900 dark:text-white uppercase">{{ __('Total Aggregate Stock') }}</td>
                                        <td class="px-4 py-3 text-right text-base text-indigo-650 dark:text-indigo-400">{{ number_format($totalStock) }} Units</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- TAB 2: Financial Details & Margin Calculators -->
                @if($activeTab === 'pricing')
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Financial Blueprint') }}</h3>
                        
                        <!-- Mini visual breakdown bar -->
                        <div class="space-y-2">
                            <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">{{ __('Cost & Profit Breakdown') }}</span>
                            <div class="h-6 w-full rounded-lg overflow-hidden flex text-xs font-extrabold text-white text-center">
                                @php
                                    $price = $product->selling_price;
                                    $cost = $product->purchase_price;
                                    $gstVal = $this->financials['gst_amount'];
                                    $profitVal = $this->financials['net_profit'];
                                    
                                    $costPct = $price > 0 ? ($cost / $price) * 100 : 100;
                                    $gstPct = $price > 0 ? ($gstVal / $price) * 100 : 0;
                                    $profitPct = $price > 0 ? ($profitVal / $price) * 100 : 0;
                                @endphp
                                @if($price > 0 && $profitVal > 0)
                                    <div class="bg-gray-400 dark:bg-gray-600 flex items-center justify-center" style="width: {{ $costPct }}%">{{ __('Cost') }} ({{ round($costPct) }}%)</div>
                                    <div class="bg-amber-500 flex items-center justify-center" style="width: {{ $gstPct }}%">{{ __('Tax') }} ({{ round($gstPct) }}%)</div>
                                    <div class="bg-indigo-650 flex items-center justify-center" style="width: {{ $profitPct }}%">{{ __('Profit') }} ({{ round($profitPct) }}%)</div>
                                @else
                                    <div class="bg-gray-300 dark:bg-gray-600 w-full flex items-center justify-center text-gray-600">{{ __('No Profit Margin Configured') }}</div>
                                @endif
                            </div>
                        </div>

                        <!-- Table specs -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900 border dark:border-gray-750 rounded-xl">
                                <span class="block text-xs font-semibold text-gray-400 uppercase">{{ __('Purchase Cost') }}</span>
                                <span class="text-xl font-black text-gray-950 dark:text-white">₹{{ number_format($product->purchase_price, 2) }}</span>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900 border dark:border-gray-750 rounded-xl">
                                <span class="block text-xs font-semibold text-gray-400 uppercase">{{ __('Selling Price') }}</span>
                                <span class="text-xl font-black text-gray-950 dark:text-white">₹{{ number_format($product->selling_price, 2) }}</span>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900 border dark:border-gray-750 rounded-xl">
                                <span class="block text-xs font-semibold text-gray-400 uppercase">{{ __('MRP') }}</span>
                                <span class="text-xl font-black text-gray-950 dark:text-white">₹{{ number_format($product->mrp, 2) }}</span>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900 border dark:border-gray-750 rounded-xl">
                                <span class="block text-xs font-semibold text-gray-400 uppercase">{{ __('Tax In Price') }}</span>
                                <span class="text-xl font-black text-amber-500">₹{{ number_format($this->financials['gst_amount'], 2) }}</span>
                            </div>
                        </div>

                        <!-- Profit metrics -->
                        <div class="border-t pt-4 dark:border-gray-700 divide-y divide-gray-150 dark:divide-gray-700 text-sm">
                            <div class="py-2.5 flex justify-between items-center">
                                <span class="text-gray-500 font-medium">{{ __('Gross Profit Margin (Direct)') }}</span>
                                <span class="font-extrabold text-green-600">₹{{ number_format($this->financials['profit'], 2) }}</span>
                            </div>
                            <div class="py-2.5 flex justify-between items-center">
                                <span class="text-gray-500 font-medium">{{ __('Net Profit Margin (Tax Deducted)') }}</span>
                                <span class="font-extrabold text-green-600">₹{{ number_format($this->financials['net_profit'], 2) }}</span>
                            </div>
                            <div class="py-2.5 flex justify-between items-center">
                                <span class="text-gray-500 font-medium">{{ __('Margin Percentage (Margin / Sale Price)') }}</span>
                                <span class="font-extrabold text-indigo-600">{{ number_format($this->financials['margin_percent'], 1) }}%</span>
                            </div>
                            <div class="py-2.5 flex justify-between items-center">
                                <span class="text-gray-500 font-medium">{{ __('Markup Percentage (Margin / Purchase Cost)') }}</span>
                                <span class="font-extrabold text-indigo-600">{{ number_format($this->financials['markup_percent'], 1) }}%</span>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- TAB 3: Ledger History -->
                @if($activeTab === 'ledger')
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 space-y-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Stock Ledger Transactions') }}</h3>
                        
                        <div class="overflow-x-auto border rounded-lg dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                                <thead class="bg-gray-50 dark:bg-gray-900/50">
                                    <tr>
                                        <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Reference') }}</th>
                                        <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Warehouse') }}</th>
                                        <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Action') }}</th>
                                        <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase text-right">{{ __('Quantity') }}</th>
                                        <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase text-right">{{ __('Balance') }}</th>
                                        <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Date') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-150 dark:divide-gray-700 text-xs">
                                    @forelse($ledgerHistory as $log)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-750/30 transition">
                                            <td class="px-4 py-3 font-mono font-bold text-indigo-650 dark:text-indigo-400 text-[10px]">
                                                {{ $log->reference_type }} 
                                                @if($log->reference_id)
                                                    <span class="block text-[8px] text-gray-400 font-normal">#{{ substr($log->reference_id, 0, 8) }}</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">{{ $log->warehouse->name ?? __('N/A') }}</td>
                                            <td class="px-4 py-3">
                                                <span class="px-1.5 py-0.5 rounded font-bold uppercase tracking-wider text-[9px]
                                                    @if(str_contains($log->transaction_type, 'add') || str_contains($log->transaction_type, 'receipt') || str_contains($log->transaction_type, 'purchase')) bg-green-50 dark:bg-green-950/20 text-green-700 dark:text-green-300
                                                    @elseif(str_contains($log->transaction_type, 'remove') || str_contains($log->transaction_type, 'sale')) bg-red-50 dark:bg-red-950/20 text-red-700 dark:text-red-300
                                                    @else bg-gray-100 text-gray-700 @endif">
                                                    {{ str_replace('_', ' ', $log->transaction_type) }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-right font-black @if($log->quantity > 0) text-green-600 @else text-red-500 @endif">
                                                {{ $log->quantity > 0 ? '+' : '' }}{{ number_format($log->quantity) }}
                                            </td>
                                            <td class="px-4 py-3 text-right font-bold text-gray-900 dark:text-white">
                                                {{ number_format($log->balance_after) }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-500 dark:text-gray-450 whitespace-nowrap">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 italic">
                                                {{ __('No transaction logs registered for this product.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="pt-2">
                            {{ $ledgerHistory->links() }}
                        </div>
                    </div>
                @endif

                <!-- TAB 4: Sales Analytics -->
                @if($activeTab === 'analytics')
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Activity & Volume Analytics') }}</h3>
                        
                        <!-- Grid metrics -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-900 border dark:border-gray-750 rounded-xl">
                                <span class="block text-xs font-semibold text-gray-400 uppercase">{{ __('Total Units Sold') }}</span>
                                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($this->analytics['total_sales_qty']) }}</span>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900 border dark:border-gray-750 rounded-xl">
                                <span class="block text-xs font-semibold text-gray-400 uppercase">{{ __('Sales Revenue') }}</span>
                                <span class="text-2xl font-black text-green-600">₹{{ number_format($this->analytics['total_sales_revenue'], 2) }}</span>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900 border dark:border-gray-750 rounded-xl">
                                <span class="block text-xs font-semibold text-gray-400 uppercase">{{ __('Total Purchased') }}</span>
                                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($this->analytics['total_purchased_qty']) }}</span>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900 border dark:border-gray-750 rounded-xl">
                                <span class="block text-xs font-semibold text-gray-400 uppercase">{{ __('Purchase Outflow') }}</span>
                                <span class="text-2xl font-black text-indigo-600">₹{{ number_format($this->analytics['total_purchased_cost'], 2) }}</span>
                            </div>
                        </div>

                        <!-- 6 Months Trend visualizer -->
                        <div class="border-t pt-5 dark:border-gray-700">
                            <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">{{ __('Monthly Revenue Trend (Last 6 Months)') }}</h4>
                            <div class="flex items-end justify-between h-40 pt-6 px-4 border rounded-xl bg-gray-50 dark:bg-gray-900 dark:border-gray-750">
                                @php
                                    $maxRevenue = collect($this->analytics['sales_trend'])->max('amount') ?: 1000;
                                @endphp
                                @foreach($this->analytics['sales_trend'] as $trend)
                                    @php
                                        $heightPct = ($trend['amount'] / $maxRevenue) * 100;
                                    @endphp
                                    <div class="flex flex-col items-center flex-1 space-y-2 group relative">
                                        <!-- Tooltip -->
                                        <div class="absolute -top-8 bg-gray-900 text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition duration-150 pointer-events-none font-bold">
                                            ₹{{ number_format($trend['amount'], 2) }}
                                        </div>
                                        <div class="w-12 bg-indigo-600 dark:bg-indigo-500 rounded-t hover:bg-indigo-750 transition" style="height: {{ max($heightPct, 5) }}px;"></div>
                                        <span class="text-[10px] text-gray-500 dark:text-gray-400 font-bold whitespace-nowrap">{{ $trend['month'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- TAB 5: Audit Log/Trail -->
                @if($activeTab === 'logs')
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 space-y-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Security & Changes Audit Trail') }}</h3>
                        
                        <div class="overflow-x-auto border rounded-lg dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                                <thead class="bg-gray-50 dark:bg-gray-900/50">
                                    <tr>
                                        <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Event') }}</th>
                                        <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Description') }}</th>
                                        <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Performed By') }}</th>
                                        <th class="px-4 py-2.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">{{ __('Date & Time') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-150 dark:divide-gray-700 text-xs" x-data="{ openLog: null }">
                                    @forelse($this->auditLogs as $index => $log)
                                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-750/30 transition cursor-pointer" @click="openLog = (openLog === {{ $index }} ? null : {{ $index }})">
                                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">
                                                <span class="px-2 py-0.5 rounded font-extrabold uppercase text-[9px] tracking-wider
                                                    @if($log['event'] === 'created') bg-green-50 text-green-700 dark:bg-green-950/20 dark:text-green-300
                                                    @elseif($log['event'] === 'deleted') bg-red-50 text-red-700 dark:bg-red-950/20 dark:text-red-300
                                                    @else bg-blue-50 text-blue-700 dark:bg-blue-950/20 dark:text-blue-300 @endif">
                                                    {{ $log['event'] }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300 font-semibold">{{ $log['description'] }}</td>
                                            <td class="px-4 py-3 font-medium text-gray-700 dark:text-gray-300">{{ $log['user_name'] }}</td>
                                            <td class="px-4 py-3 text-gray-500 dark:text-gray-450 whitespace-nowrap">{{ $log['created_at'] }}</td>
                                        </tr>
                                        <!-- Expanded JSON properties -->
                                        @if($log['properties'])
                                            <tr x-show="openLog === {{ $index }}" x-cloak class="bg-gray-50 dark:bg-gray-900 border-l-4 border-indigo-500">
                                                <td colspan="4" class="px-6 py-4">
                                                    <div class="text-[10px] font-mono text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-950 p-3 rounded-lg border dark:border-gray-800 overflow-x-auto max-h-40">
                                                        <pre>{{ $log['properties'] }}</pre>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 italic">
                                                {{ __('No audit trail matches.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Product Modification Form Modal -->
    @if($showingProductModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="$set('showingProductModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border dark:border-gray-700">
                    <form wire:submit="saveProduct">
                        <div class="px-6 py-4 border-b dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ __('Edit Product Profile') }}
                            </h3>
                        </div>
                        
                        <div class="p-6 space-y-4 max-h-[550px] overflow-y-auto">
                            <!-- Basic details -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="form-name" :value="__('Product Name')" />
                                    <x-text-input wire:model="name" id="form-name" class="mt-1 block w-full text-sm" required />
                                    <x-input-error :messages="$errors->get('name')" />
                                </div>
                                <div>
                                    <x-input-label for="form-sku" :value="__('SKU (Locked)')" />
                                    <x-text-input wire:model="sku" id="form-sku" class="mt-1 block w-full text-sm bg-gray-100 dark:bg-gray-700" readonly disabled />
                                    <x-input-error :messages="$errors->get('sku')" />
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-4">
                                <div>
                                    <x-input-label for="form-category" :value="__('Category')" />
                                    <select wire:model="categoryId" id="form-category" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-xs py-2">
                                        <option value="">-- {{ __('Select') }} --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="form-brand" :value="__('Brand')" />
                                    <select wire:model="brandId" id="form-brand" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-xs py-2">
                                        <option value="">-- {{ __('Select') }} --</option>
                                        @foreach($brands as $b)
                                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="form-manufacturer" :value="__('Manufacturer')" />
                                    <select wire:model="manufacturerId" id="form-manufacturer" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-xs py-2">
                                        <option value="">-- {{ __('Select') }} --</option>
                                        @foreach($manufacturers as $m)
                                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Excise & measurements -->
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 border-t pt-4 dark:border-gray-700">
                                <div>
                                    <x-input-label for="form-liquor-type" :value="__('Liquor Type')" />
                                    <select wire:model="liquorType" id="form-liquor-type" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-xs py-2">
                                        <option value="Spirit">{{ __('Spirit') }}</option>
                                        <option value="Beer">{{ __('Beer') }}</option>
                                        <option value="Wine">{{ __('Wine') }}</option>
                                        <option value="Liqueur">{{ __('Liqueur') }}</option>
                                        <option value="Cider">{{ __('Cider') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="form-volume" :value="__('Volume (ml)')" />
                                    <x-text-input wire:model="volumeMl" id="form-volume" type="number" class="mt-1 block w-full text-sm" required />
                                    <x-input-error :messages="$errors->get('volumeMl')" />
                                </div>
                                <div>
                                    <x-input-label for="form-alcohol" :value="__('Alcohol %')" />
                                    <x-text-input wire:model="alcoholPercentage" id="form-alcohol" type="number" step="0.01" class="mt-1 block w-full text-sm" required />
                                    <x-input-error :messages="$errors->get('alcoholPercentage')" />
                                </div>
                                <div>
                                    <x-input-label for="form-gst" :value="__('GST Rate %')" />
                                    <x-text-input wire:model="gstRate" id="form-gst" type="number" step="0.01" class="mt-1 block w-full text-sm" required />
                                    <x-input-error :messages="$errors->get('gstRate')" />
                                </div>
                            </div>

                            <!-- Pricing -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t pt-4 dark:border-gray-700">
                                <div>
                                    <x-input-label for="form-mrp" :value="__('MRP (₹)')" />
                                    <x-text-input wire:model="mrp" id="form-mrp" type="number" step="0.01" class="mt-1 block w-full text-sm" required />
                                    <x-input-error :messages="$errors->get('mrp')" />
                                </div>
                                <div>
                                    <x-input-label for="form-purchase-price" :value="__('Purchase Price (₹)')" />
                                    <x-text-input wire:model="purchasePrice" id="form-purchase-price" type="number" step="0.01" class="mt-1 block w-full text-sm" required />
                                    <x-input-error :messages="$errors->get('purchasePrice')" />
                                </div>
                                <div>
                                    <x-input-label for="form-selling-price" :value="__('Selling Price (₹)')" />
                                    <x-text-input wire:model="sellingPrice" id="form-selling-price" type="number" step="0.01" class="mt-1 block w-full text-sm" required />
                                    <x-input-error :messages="$errors->get('sellingPrice')" />
                                </div>
                            </div>

                            <!-- Coding Compliance codes -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 border-t pt-4 dark:border-gray-700">
                                <div>
                                    <x-input-label for="form-barcode" :value="__('Barcode / EAN')" />
                                    <x-text-input wire:model="barcode" id="form-barcode" class="mt-1 block w-full text-sm" />
                                    <x-input-error :messages="$errors->get('barcode')" />
                                </div>
                                <div>
                                    <x-input-label for="form-hsn" :value="__('HSN Code')" />
                                    <x-text-input wire:model="hsnCode" id="form-hsn" class="mt-1 block w-full text-sm" />
                                    <x-input-error :messages="$errors->get('hsnCode')" />
                                </div>
                            </div>

                            <!-- Country specifications -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="form-origin-country" :value="__('Country of Origin')" />
                                    <x-text-input wire:model="originCountry" id="form-origin-country" class="mt-1 block w-full text-sm" />
                                </div>
                                <div>
                                    <x-input-label for="form-origin-region" :value="__('Region (State/Province)')" />
                                    <x-text-input wire:model="originRegion" id="form-origin-region" class="mt-1 block w-full text-sm" />
                                </div>
                            </div>

                            <!-- Inventory triggers -->
                            <div class="flex items-center space-x-6 border-t pt-4 dark:border-gray-700">
                                <label class="flex items-center space-x-2 text-xs font-bold text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" wire:model="batchTracking" class="rounded border-gray-350 text-indigo-600" />
                                    <span>{{ __('Enable Batch Tracking') }}</span>
                                </label>
                                <label class="flex items-center space-x-2 text-xs font-bold text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" wire:model="expiryTracking" class="rounded border-gray-350 text-indigo-600" />
                                    <span>{{ __('Enable Expiry Tracking') }}</span>
                                </label>
                                <label class="flex items-center space-x-2 text-xs font-bold text-gray-700 dark:text-gray-300">
                                    <input type="checkbox" wire:model="serialTracking" class="rounded border-gray-350 text-indigo-600" />
                                    <span>{{ __('Enable Serial Tracking') }}</span>
                                </label>
                            </div>

                            <!-- Description -->
                            <div class="border-t pt-4 dark:border-gray-700">
                                <x-input-label for="form-desc" :value="__('Product Notes / Description')" />
                                <textarea wire:model="description" id="form-desc" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-xs py-2"></textarea>
                            </div>

                            <!-- Photo Uploads -->
                            <div>
                                <x-input-label :value="__('Product Photos')" />
                                <input type="file" wire:model="productImages" multiple class="mt-1 block w-full text-xs text-gray-500" />
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700 flex justify-end space-x-2">
                            <button type="button" wire:click="$set('showingProductModal', false)" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 cursor-pointer">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold cursor-pointer">
                                {{ __('Save Changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Variant Creation Modal -->
    @if($showingVariantModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="$set('showingVariantModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border dark:border-gray-700">
                    <form wire:submit="saveVariant">
                        <div class="px-6 py-4 border-b dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ __('Add Bottle Size Variant') }}
                            </h3>
                        </div>
                        
                        <div class="p-6 space-y-4 max-h-[550px] overflow-y-auto">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="var-volume" :value="__('Volume (ml)')" />
                                    <x-text-input wire:model="varVolumeMl" id="var-volume" type="number" class="mt-1 block w-full text-sm" required />
                                </div>
                                <div>
                                    <x-input-label for="var-alcohol" :value="__('Alcohol % (ABV)')" />
                                    <x-text-input wire:model="varAlcoholPercentage" id="var-alcohol" type="number" step="0.01" class="mt-1 block w-full text-sm" required />
                                </div>
                            </div>

                           <div class="grid grid-cols-2 gap-4 border-t pt-4 dark:border-gray-700">
                               <div>
                                   <x-input-label for="var-sku" :value="__('Variant SKU (Auto-generates if blank)')" />
                                   <x-text-input wire:model="varSku" id="var-sku" class="mt-1 block w-full text-sm" placeholder="SKU-XXXXXX" />
                               </div>
                               <div>
                                   <x-input-label for="var-barcode" :value="__('Barcode / EAN')" />
                                   <x-text-input wire:model="varBarcode" id="var-barcode" class="mt-1 block w-full text-sm" />
                               </div>
                           </div>

                           <div class="grid grid-cols-3 gap-4 border-t pt-4 dark:border-gray-700">
                               <div>
                                   <x-input-label for="var-mrp" :value="__('MRP (₹)')" />
                                   <x-text-input wire:model="varMrp" id="var-mrp" type="number" step="0.01" class="mt-1 block w-full text-sm" required />
                               </div>
                               <div>
                                   <x-input-label for="var-purchase-price" :value="__('Purchase Price (₹)')" />
                                   <x-text-input wire:model="varPurchasePrice" id="var-purchase-price" type="number" step="0.01" class="mt-1 block w-full text-sm" required />
                               </div>
                               <div>
                                   <x-input-label for="var-selling-price" :value="__('Selling Price (₹)')" />
                                   <x-text-input wire:model="varSellingPrice" id="var-selling-price" type="number" step="0.01" class="mt-1 block w-full text-sm" required />
                               </div>
                           </div>

                           <!-- Inventory triggers -->
                           <div class="flex items-center space-x-6 border-t pt-4 dark:border-gray-700 font-semibold text-xs text-gray-700 dark:text-gray-300">
                               <label class="flex items-center space-x-2">
                                   <input type="checkbox" wire:model="varBatchTracking" class="rounded border-gray-350 text-indigo-600" />
                                   <span>{{ __('Enable Batch Tracking') }}</span>
                               </label>
                               <label class="flex items-center space-x-2">
                                   <input type="checkbox" wire:model="varExpiryTracking" class="rounded border-gray-350 text-indigo-600" />
                                   <span>{{ __('Enable Expiry Tracking') }}</span>
                               </label>
                               <label class="flex items-center space-x-2">
                                   <input type="checkbox" wire:model="varSerialTracking" class="rounded border-gray-350 text-indigo-600" />
                                   <span>{{ __('Enable Serial Tracking') }}</span>
                               </label>
                           </div>
                       </div>

                       <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700 flex justify-end space-x-2">
                           <button type="button" wire:click="$set('showingVariantModal', false)" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 cursor-pointer">
                               {{ __('Cancel') }}
                           </button>
                           <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold cursor-pointer">
                               {{ __('Save Variant') }}
                           </button>
                       </div>
                   </form>
               </div>
           </div>
       </div>
   @endif
</div>
