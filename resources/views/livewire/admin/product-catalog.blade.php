<div class="p-6 max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                {{ __('Product Catalog') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Manage product profiles, liquor excise parameters, pricing boundaries, and variants.') }}
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <button wire:click="exportExcel" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer">
                <svg class="h-5 w-5 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                {{ __('Export Excel') }}
            </button>
            <button wire:click="openCreateModal" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow cursor-pointer">
                {{ __('Add Product') }}
            </button>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
            <div class="p-3 bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 rounded-lg">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <span class="block text-xs text-gray-400 font-semibold uppercase">{{ __('Total Products') }}</span>
                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $metrics['total_products'] }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-lg">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <span class="block text-xs text-gray-400 font-semibold uppercase">{{ __('Active Brands') }}</span>
                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $metrics['brands_count'] }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
            <div class="p-3 bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 rounded-lg">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <span class="block text-xs text-gray-400 font-semibold uppercase">{{ __('Active Categories') }}</span>
                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $metrics['categories_count'] }}</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex items-center space-x-4">
            <div class="p-3 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 rounded-lg">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <span class="block text-xs text-gray-400 font-semibold uppercase">{{ __('Inactive Products') }}</span>
                <span class="text-2xl font-black text-gray-900 dark:text-white">{{ $metrics['inactive_count'] }}</span>
            </div>
        </div>
    </div>

    <!-- Alert Message -->
    @if (session()->has('message'))
        <div class="p-4 bg-green-50 dark:bg-green-950/30 border-l-4 border-green-500 rounded-lg flex items-center space-x-3">
            <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Advanced Filter Section & Import Excel Layout -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 space-y-4">
        <!-- Main Search and Status Bar -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <x-input-label for="search" :value="__('Search Catalogue')" />
                <x-text-input wire:model.live="search" id="search" type="text" class="mt-1 block w-full" placeholder="Search by product name, SKU, barcode, HSN code..." />
            </div>
            <div>
                <x-input-label for="filterStatus" :value="__('Filter Status')" />
                <select wire:model.live="filterStatus" id="filterStatus" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2">
                    <option value="active">{{ __('Active Products') }}</option>
                    <option value="inactive">{{ __('Inactive Products') }}</option>
                    <option value="deleted">{{ __('Soft Deleted') }}</option>
                </select>
            </div>
            <!-- Excel Import -->
            <div class="flex items-end">
                <form wire:submit="importExcel" class="w-full flex items-center space-x-2">
                    <input wire:model="importFile" type="file" id="importFile" class="hidden" />
                    <label for="importFile" class="flex-1 inline-flex justify-center items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-xs font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer">
                        {{ $importFile ? $importFile->getClientOriginalName() : __('Choose Excel file') }}
                    </label>
                    <button type="submit" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold cursor-pointer">
                        {{ __('Import') }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Secondary Filters Grid -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 pt-2 border-t dark:border-gray-700">
            <div>
                <x-input-label for="selectedCategory" :value="__('Category')" />
                <select wire:model.live="selectedCategory" id="selectedCategory" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-xs py-2">
                    <option value="">-- {{ __('All Categories') }} --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="selectedBrand" :value="__('Brand')" />
                <select wire:model.live="selectedBrand" id="selectedBrand" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-xs py-2">
                    <option value="">-- {{ __('All Brands') }} --</option>
                    @foreach($brands as $b)
                        <option value="{{ $b->id }}">{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <x-input-label for="selectedLiquorType" :value="__('Liquor Type')" />
                <select wire:model.live="selectedLiquorType" id="selectedLiquorType" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-xs py-2">
                    <option value="">-- {{ __('All Types') }} --</option>
                    <option value="Spirit">{{ __('Spirit') }}</option>
                    <option value="Beer">{{ __('Beer') }}</option>
                    <option value="Wine">{{ __('Wine') }}</option>
                    <option value="Liqueur">{{ __('Liqueur') }}</option>
                    <option value="Cider">{{ __('Cider') }}</option>
                </select>
            </div>

            <div>
                <x-input-label for="selectedVolume" :value="__('Volume (ml)')" />
                <select wire:model.live="selectedVolume" id="selectedVolume" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-xs py-2">
                    <option value="">-- {{ __('All Volumes') }} --</option>
                    <option value="180">{{ __('180 ml (Quarter)') }}</option>
                    <option value="330">{{ __('330 ml (Pint)') }}</option>
                    <option value="375">{{ __('375 ml (Half)') }}</option>
                    <option value="650">{{ __('650 ml (Large Beer)') }}</option>
                    <option value="750">{{ __('750 ml (Full)') }}</option>
                    <option value="1000">{{ __('1000 ml (Litre)') }}</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <div class="w-1/2">
                    <x-input-label for="minPrice" :value="__('Min Price')" />
                    <x-text-input wire:model.live="minPrice" id="minPrice" type="number" class="mt-1 block w-full text-xs" placeholder="0" />
                </div>
                <div class="w-1/2">
                    <x-input-label for="maxPrice" :value="__('Max Price')" />
                    <x-text-input wire:model.live="maxPrice" id="maxPrice" type="number" class="mt-1 block w-full text-xs" placeholder="10k" />
                </div>
            </div>
        </div>
    </div>

    <!-- Product Grid Listing -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-left">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Image') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Product details') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Category / Brand') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Pricing & Tax') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Tracking') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3.5 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="h-12 w-12 rounded-lg bg-gray-100 dark:bg-gray-900 border dark:border-gray-800 flex items-center justify-center overflow-hidden">
                                    @if($product->primaryImage())
                                        <img src="{{ Storage::url($product->primaryImage()->image_path) }}" class="h-full w-full object-cover" alt="Image" />
                                    @else
                                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('admin.products.detail', $product->id) }}" class="text-sm font-extrabold text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:underline">{{ $product->name }}</a>
                                <div class="text-xs text-gray-400 font-mono">SKU: {{ $product->sku }} &bull; HSN: {{ $product->hsn_code ?? __('N/A') }}</div>
                                <div class="text-[10px] text-indigo-500">{{ $product->liquor_type }} &bull; {{ $product->volume_ml }}ml &bull; alc. {{ $product->alcohol_percentage }}%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                <div class="font-semibold">{{ $product->category->name ?? __('Uncategorized') }}</div>
                                <div class="text-xs text-gray-400">{{ $product->brand->name ?? __('N/A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="font-extrabold text-gray-900 dark:text-white">₹{{ number_format($product->selling_price, 2) }}</div>
                                <div class="text-xs text-gray-400">MRP: ₹{{ number_format($product->mrp, 2) }} &bull; GST: {{ $product->gst_rate }}%</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col space-y-0.5">
                                    <span class="px-1.5 py-0.25 rounded text-[9px] font-bold tracking-wider uppercase inline-block w-fit
                                        {{ $product->batch_tracking ? 'bg-blue-50 dark:bg-blue-950/20 text-blue-700 dark:text-blue-300' : 'bg-gray-100 text-gray-400' }}">
                                        {{ __('Batch') }}
                                    </span>
                                    <span class="px-1.5 py-0.25 rounded text-[9px] font-bold tracking-wider uppercase inline-block w-fit
                                        {{ $product->expiry_tracking ? 'bg-amber-50 dark:bg-amber-950/20 text-amber-700 dark:text-amber-300' : 'bg-gray-100 text-gray-400' }}">
                                        {{ __('Expiry') }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                    @if($product->status === 'active') bg-green-50 dark:bg-green-950/30 text-green-700 dark:text-green-300
                                    @else bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 @endif">
                                    {{ $product->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold">
                                @if($filterStatus === 'deleted')
                                    <button wire:click="restoreProduct('{{ $product->id }}')" class="text-indigo-650 hover:text-indigo-900 cursor-pointer">
                                        {{ __('Restore') }}
                                    </button>
                                @else
                                    <a href="{{ route('admin.products.detail', $product->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3 cursor-pointer">
                                        {{ __('View') }}
                                    </a>
                                    <button wire:click="openEditModal('{{ $product->id }}')" class="text-indigo-650 hover:text-indigo-900 mr-3 cursor-pointer">
                                        {{ __('Edit') }}
                                    </button>
                                    <button wire:click="deleteProduct('{{ $product->id }}')" class="text-red-650 hover:text-red-900 cursor-pointer">
                                        {{ __('Delete') }}
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500 italic">
                                {{ __('No products matching search parameters.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t dark:border-gray-700">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Product Creation/Modification Form Modal -->
    @if($showingProductModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80" wire:click="$set('showingProductModal', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border dark:border-gray-700">
                    <form wire:submit="saveProduct">
                        <div class="px-6 py-4 border-b dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                {{ $productId ? __('Edit Product Profile') : __('Create New Product') }}
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
                                    <x-input-label for="form-sku" :value="__('SKU (Auto-generates if blank)')" />
                                    <x-text-input wire:model="sku" id="form-sku" class="mt-1 block w-full text-sm" placeholder="SKU-XXXXXX" />
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
                                </div>
                            </div>

                            <!-- Financial details -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-t pt-4 dark:border-gray-700">
                                <div>
                                    <x-input-label for="form-mrp" :value="__('MRP (₹)')" />
                                    <x-text-input wire:model="mrp" id="form-mrp" type="number" step="0.01" class="mt-1 block w-full text-sm" required />
                                    <x-input-error :messages="$errors->get('mrp')" />
                                </div>
                                <div>
                                    <x-input-label for="form-purchase" :value="__('Purchase Price (₹)')" />
                                    <x-text-input wire:model="purchasePrice" id="form-purchase" type="number" step="0.01" class="mt-1 block w-full text-sm" required />
                                    <x-input-error :messages="$errors->get('purchasePrice')" />
                                </div>
                                <div>
                                    <x-input-label for="form-selling" :value="__('Selling Price (₹)')" />
                                    <x-text-input wire:model="sellingPrice" id="form-selling" type="number" step="0.01" class="mt-1 block w-full text-sm" required />
                                    <x-input-error :messages="$errors->get('sellingPrice')" />
                                </div>
                            </div>

                            <!-- Image Attachment -->
                            <div class="border-t pt-4 dark:border-gray-700">
                                <x-input-label :value="__('Product Images (Multiple)')" />
                                <div class="mt-2 flex items-center space-x-2">
                                    <input wire:model="productImages" type="file" multiple id="productImages" class="hidden" />
                                    <label for="productImages" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-xs font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer">
                                        {{ __('Choose Images') }}
                                    </label>
                                    <span class="text-xs text-gray-400">
                                        {{ count($productImages) }} {{ __('files selected') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Tracking Configuration Toggles -->
                            <div class="grid grid-cols-3 gap-4 border-t pt-4 dark:border-gray-700">
                                <label class="inline-flex items-center space-x-2">
                                    <input wire:model="batchTracking" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('Enable Batch Tracking') }}</span>
                                </label>
                                <label class="inline-flex items-center space-x-2">
                                    <input wire:model="expiryTracking" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('Enable Expiry Tracking') }}</span>
                                </label>
                                <label class="inline-flex items-center space-x-2">
                                    <input wire:model="serialTracking" type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __('Enable Serial Tracking') }}</span>
                                </label>
                            </div>

                            <!-- Origin information -->
                            <div class="grid grid-cols-2 gap-4 border-t pt-4 dark:border-gray-700">
                                <div>
                                    <x-input-label for="form-country" :value="__('Country of Origin')" />
                                    <x-text-input wire:model="originCountry" id="form-country" class="mt-1 block w-full text-sm" placeholder="e.g. Scotland" />
                                </div>
                                <div>
                                    <x-input-label for="form-region" :value="__('Region of Origin')" />
                                    <x-text-input wire:model="originRegion" id="form-region" class="mt-1 block w-full text-sm" placeholder="e.g. Speyside" />
                                </div>
                            </div>

                            <!-- Additional text -->
                            <div>
                                <x-input-label for="form-desc" :value="__('Description')" />
                                <textarea wire:model="description" id="form-desc" rows="2" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2"></textarea>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 border-t dark:border-gray-700">
                            <x-primary-button type="submit">{{ __('Save Product') }}</x-primary-button>
                            <x-secondary-button wire:click="$set('showingProductModal', false)">{{ __('Cancel') }}</x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
