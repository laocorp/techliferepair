<?php

use Livewire\Volt\Component;
use App\Models\Part;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Client; // <--- Nuevo
use App\Models\Setting;
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule as ValidationRule;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    // --- Variables del POS ---
    public string $search = '';
    public array $cart = []; 
    public float $subtotal = 0.0;
    public float $tax_amount = 0.0;
    public float $total = 0.0;

    // --- Configuración ---
    public string $currency = '$';
    public string $tax_name = 'IVA';
    public float $tax_rate = 0;

    // --- Cliente Seleccionado ---
    public ?int $selected_client_id = null;

    // --- Variables para Crear Cliente Rápido ---
    public bool $client_drawer = false;
    #[Rule('required|min:3')] public string $new_client_name = '';
    #[Rule('nullable')] public string $new_client_id = ''; // DNI/RUC
    #[Rule('nullable')] public string $new_client_phone = '';
    #[Rule('nullable|email')] public string $new_client_email = '';

    // --- Variables para Crear Producto Rápido ---
    public bool $product_drawer = false;
    #[Rule('required|min:3')] public string $new_part_name = '';
    public string $new_part_sku = '';
    #[Rule('required|numeric|min:0')] public float $new_part_price = 0;
    #[Rule('required|integer|min:0')] public int $new_part_stock = 0;

    public function mount(): void
    {
        $settings = Setting::where('company_id', auth()->user()->company_id)->first();
        if ($settings) {
            $this->currency = $settings->currency_symbol;
            $this->tax_name = $settings->tax_name;
            $this->tax_rate = $settings->tax_rate;
        }
    }

    // --- LÓGICA DE CARRITO ---

    public function addToCart($partId): void
    {
        $part = Part::find($partId);
        
        if ($part->stock <= 0) {
            $this->error('Sin stock disponible');
            return;
        }

        if (isset($this->cart[$partId])) {
            if ($this->cart[$partId]['quantity'] < $part->stock) {
                $this->cart[$partId]['quantity']++;
            } else {
                $this->error('Stock máximo alcanzado');
            }
        } else {
            $this->cart[$partId] = [
                'id' => $part->id,
                'name' => $part->name,
                'sku' => $part->sku,
                'price' => $part->price,
                'quantity' => 1
            ];
        }
        
        $this->calculateTotals();
        // $this->success('Agregado'); // Opcional: quitar para hacerlo más rápido visualmente
    }

    public function removeFromCart($partId): void
    {
        unset($this->cart[$partId]);
        $this->calculateTotals();
    }

    public function calculateTotals(): void
    {
        $this->subtotal = 0;
        foreach ($this->cart as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }
        $this->tax_amount = $this->subtotal * ($this->tax_rate / 100);
        $this->total = $this->subtotal + $this->tax_amount;
    }

    // --- CREACIÓN RÁPIDA ---

    public function createClient(): void
    {
        $this->validate([
            'new_client_name' => 'required|min:3',
            'new_client_email' => ['nullable', 'email', ValidationRule::unique('clients', 'email')->where('company_id', auth()->user()->company_id)]
        ]);

        $client = Client::create([
            'name' => $this->new_client_name,
            'tax_id' => $this->new_client_id,
            'email' => $this->new_client_email,
            'phone' => $this->new_client_phone,
            'company_id' => auth()->user()->company_id // El trait lo pone, pero por seguridad explícita
        ]);

        $this->selected_client_id = $client->id; // Auto-seleccionar
        $this->client_drawer = false;
        $this->reset(['new_client_name', 'new_client_id', 'new_client_email', 'new_client_phone']);
        $this->success('Cliente creado y seleccionado');
    }

    public function createProduct(): void
    {
        $this->validate([
            'new_part_name' => 'required',
            'new_part_price' => 'required',
            'new_part_stock' => 'required',
            'new_part_sku' => ['required', ValidationRule::unique('parts', 'sku')->where('company_id', auth()->user()->company_id)]
        ]);

        $part = Part::create([
            'name' => $this->new_part_name,
            'sku' => $this->new_part_sku,
            'price' => $this->new_part_price,
            'stock' => $this->new_part_stock,
            'company_id' => auth()->user()->company_id
        ]);

        $this->product_drawer = false;
        $this->reset(['new_part_name', 'new_part_sku', 'new_part_price', 'new_part_stock']);
        $this->success('Producto creado');
        $this->addToCart($part->id); // Agregar al carrito de una vez
    }

    // --- COBRO ---

    public function checkout(): void
    {
        if (empty($this->cart)) return;

        DB::transaction(function () {
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'client_id' => $this->selected_client_id, // <--- Guardamos al cliente
                'total' => $this->total,
                'payment_method' => 'cash',
                'company_id' => auth()->user()->company_id
            ]);

            foreach ($this->cart as $item) {
                $part = Part::find($item['id']);
                if ($part) {
                    $part->decrement('stock', $item['quantity']);
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'part_id' => $part->id,
                        'quantity' => $item['quantity'],
                        'price' => $item['price']
                    ]);
                }
            }
            
            $this->js("window.open('/pos/ticket/{$sale->id}', '_blank')");
        });
        
        $this->success('Venta registrada');
        $this->cart = [];
        $this->selected_client_id = null;
        $this->calculateTotals();
    }

    public function with(): array
    {
        return [
            'parts' => Part::query()
                ->where('name', 'like', "%$this->search%")
                ->orWhere('sku', 'like', "%$this->search%")
                ->where('stock', '>', 0)
                ->orderBy('name')
                ->limit(20)
                ->get(),
            // Lista de clientes para el select
            'clients' => Client::orderBy('name')->get() 
        ];
    }
}; ?>

<div class="grid lg:grid-cols-3 gap-6 h-[calc(100vh-100px)]">
    
    <!-- COLUMNA IZQUIERDA: PRODUCTOS -->
    <div class="lg:col-span-2 flex flex-col h-full">
        <div class="mb-4 flex gap-2">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-icon name="o-magnifying-glass" class="w-5 h-5 text-slate-400" />
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-slate-900 focus:border-transparent outline-none transition-all shadow-sm"
                    placeholder="Escanear o buscar producto..." 
                    autofocus
                />
            </div>
            <!-- Botón Nuevo Producto -->
            <button wire:click="$toggle('product_drawer')" class="btn btn-primary h-full aspect-square rounded-xl shadow-md" title="Crear Producto Rápido">
                <x-icon name="o-plus" class="w-6 h-6" />
            </button>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 overflow-y-auto p-1 content-start custom-scrollbar">
            @foreach($parts as $part)
                <div 
                    class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:border-blue-500 hover:-translate-y-1 cursor-pointer transition-all flex flex-col justify-between h-36 group"
                    wire:click="addToCart({{ $part->id }})"
                >
                    <div>
                        <div class="flex justify-between items-start mb-1">
                            <span class="text-[10px] font-mono text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">{{ $part->sku }}</span>
                        </div>
                        <div class="font-bold text-slate-800 leading-tight line-clamp-2 text-sm">{{ $part->name }}</div>
                    </div>
                    <div class="flex justify-between items-end mt-2">
                        <div class="text-xs font-medium text-slate-500 bg-slate-50 px-2 py-1 rounded">Stock: {{ $part->stock }}</div>
                        <div class="text-lg font-black text-blue-600 group-hover:scale-110 transition-transform">{{ $currency }}{{ number_format($part->price, 2) }}</div>
                    </div>
                </div>
            @endforeach
            
            @if($parts->isEmpty())
                <div class="col-span-full flex flex-col items-center justify-center py-20 text-slate-400">
                    <x-icon name="o-archive-box-x-mark" class="w-16 h-16 mb-4 opacity-30" />
                    <p class="font-medium">No se encontraron productos</p>
                </div>
            @endif
        </div>
    </div>

    <!-- COLUMNA DERECHA: TICKET -->
    <div class="lg:col-span-1 flex flex-col h-full">
        <div class="bg-white border border-slate-200 rounded-xl shadow-xl flex flex-col h-full overflow-hidden">
            
            <!-- Selector de Cliente -->
            <div class="p-4 bg-slate-50 border-b border-slate-200">
                <div class="flex gap-2">
                    <div class="flex-1">
                        <x-select 
                            placeholder="Cliente (Opcional)" 
                            :options="$clients" 
                            wire:model="selected_client_id" 
                            icon="o-user" 
                            class="bg-white border-slate-300 w-full"
                            searchable
                        />
                    </div>
                    <button wire:click="$toggle('client_drawer')" class="btn btn-sm btn-outline bg-white" title="Nuevo Cliente">
                        <x-icon name="o-user-plus" class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <!-- Lista de Items -->
            <div class="flex-1 overflow-y-auto p-4 space-y-2 bg-white">
                @forelse($cart as $item)
                    <div class="flex justify-between items-center p-2 hover:bg-slate-50 rounded-lg transition-colors group">
                        <div class="flex-1 min-w-0 pr-2">
                            <div class="font-bold text-sm text-slate-800 truncate">{{ $item['name'] }}</div>
                            <div class="text-xs text-slate-500 font-mono">
                                {{ $item['quantity'] }} x {{ $currency }}{{ number_format($item['price'], 2) }}
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-slate-900 text-sm">{{ $currency }}{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                            <button wire:click="removeFromCart({{ $item['id'] }})" class="text-slate-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100"><x-icon name="o-x-mark" class="w-4 h-4" /></button>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-slate-300 opacity-60">
                        <x-icon name="o-shopping-cart" class="w-16 h-16 mb-2" />
                        <p class="text-sm font-medium">Carrito vacío</p>
                    </div>
                @endforelse
            </div>

            <!-- Resumen y Totales -->
            <div class="p-5 bg-slate-50 border-t border-slate-200">
                <div class="space-y-2 mb-4 text-sm">
                    <div class="flex justify-between text-slate-500">
                        <span>Subtotal</span>
                        <span>{{ $currency }}{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>{{ $tax_name }} ({{ $tax_rate }}%)</span>
                        <span>{{ $currency }}{{ number_format($tax_amount, 2) }}</span>
                    </div>
                    <div class="border-t border-dashed border-slate-300 my-2 pt-2 flex justify-between items-end">
                        <span class="font-bold text-slate-800 text-lg">TOTAL</span>
                        <span class="text-3xl font-black text-slate-900 tracking-tight">{{ $currency }}{{ number_format($total, 2) }}</span>
                    </div>
                </div>
                
                <button 
                    wire:click="checkout" 
                    class="w-full py-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-lg shadow-lg shadow-slate-900/20 transition-all active:scale-95 flex justify-center items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    {{ empty($cart) ? 'disabled' : '' }}
                >
                    <span wire:loading.remove class="flex items-center gap-2"><x-icon name="o-banknotes" class="w-6 h-6" /> COBRAR</span>
                    <span wire:loading class="loading loading-spinner loading-md"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- DRAWER: NUEVO CLIENTE -->
    <x-drawer wire:model="client_drawer" title="Nuevo Cliente Rápido" right class="w-full lg:w-1/3">
        <x-form wire:submit="createClient" class="space-y-4">
            <x-input label="Nombre" wire:model="new_client_name" icon="o-user" autofocus />
            <x-input label="ID Fiscal / RUC" wire:model="new_client_id" icon="o-identification" />
            <x-input label="Teléfono" wire:model="new_client_phone" icon="o-phone" />
            <x-input label="Email" wire:model="new_client_email" icon="o-envelope" />
            
            <x-slot:actions>
                <x-button label="Guardar y Seleccionar" class="btn-primary w-full" type="submit" spinner="createClient" />
            </x-slot:actions>
        </x-form>
    </x-drawer>

    <!-- DRAWER: NUEVO PRODUCTO -->
    <x-drawer wire:model="product_drawer" title="Alta Rápida de Producto" right class="w-full lg:w-1/3">
        <x-form wire:submit="createProduct" class="space-y-4">
            <x-input label="SKU / Código" wire:model="new_part_sku" icon="o-qr-code" autofocus />
            <x-input label="Nombre del Producto" wire:model="new_part_name" icon="o-tag" />
            <div class="grid grid-cols-2 gap-4">
                <x-input label="Precio ($)" wire:model="new_part_price" type="number" step="0.01" />
                <x-input label="Stock Inicial" wire:model="new_part_stock" type="number" />
            </div>
            
            <x-slot:actions>
                <x-button label="Guardar y Agregar" class="btn-primary w-full" type="submit" spinner="createProduct" />
            </x-slot:actions>
        </x-form>
    </x-drawer>
</div>
