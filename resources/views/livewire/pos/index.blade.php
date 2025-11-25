<?php

use Livewire\Volt\Component;
use App\Models\Part;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Setting; // Importar Setting
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public string $search = '';
    public array $cart = [];
    public float $subtotal = 0.0;
    public float $tax_amount = 0.0;
    public float $total = 0.0;

    // Configuración cargada
    public string $currency = '$';
    public string $tax_name = 'IVA';
    public float $tax_rate = 0;

    public function mount(): void
    {
        // Cargar configuración de la empresa actual
        $settings = Setting::where('company_id', auth()->user()->company_id)->first();
        if ($settings) {
            $this->currency = $settings->currency_symbol;
            $this->tax_name = $settings->tax_name;
            $this->tax_rate = $settings->tax_rate;
        }
    }

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

        // Cálculo de Impuestos
        $this->tax_amount = $this->subtotal * ($this->tax_rate / 100);
        $this->total = $this->subtotal + $this->tax_amount;
    }

    public function checkout(): void
    {
        if (empty($this->cart)) return;

        DB::transaction(function () {
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'total' => $this->total,
                // Podrías guardar subtotal e impuestos aquí si agregas las columnas a la tabla sales
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
                ->get()
        ];
    }
}; ?>

<div class="grid lg:grid-cols-3 gap-6 h-[calc(100vh-100px)]">
    
    <!-- COLUMNA IZQUIERDA: PRODUCTOS -->
    <div class="lg:col-span-2 flex flex-col h-full">
        <div class="mb-4 bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-icon name="o-magnifying-glass" class="w-5 h-5 text-slate-400" />
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-slate-900 focus:border-transparent outline-none transition-all"
                    placeholder="Escanear o buscar producto..." 
                    autofocus
                />
            </div>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 overflow-y-auto p-1 content-start">
            @foreach($parts as $part)
                <div 
                    class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:border-blue-400 cursor-pointer transition-all flex flex-col justify-between h-32 group"
                    wire:click="addToCart({{ $part->id }})"
                >
                    <div>
                        <div class="font-bold text-slate-800 leading-tight line-clamp-2 text-sm">{{ $part->name }}</div>
                        <div class="text-xs text-slate-400 font-mono mt-1">{{ $part->sku }}</div>
                    </div>
                    <div class="flex justify-between items-end mt-2">
                        <div class="text-xs font-medium text-slate-500 bg-slate-100 px-2 py-1 rounded">Stock: {{ $part->stock }}</div>
                        <div class="text-lg font-black text-blue-600">{{ $currency }}{{ number_format($part->price, 2) }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- COLUMNA DERECHA: TICKET -->
    <div class="lg:col-span-1 flex flex-col h-full">
        <div class="bg-white border border-slate-200 rounded-xl shadow-xl flex flex-col h-full overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-900 text-white flex justify-between items-center">
                <h2 class="font-bold text-lg flex items-center gap-2"><x-icon name="o-shopping-cart" class="w-5 h-5" /> Ticket</h2>
                <div class="text-xs opacity-70"># Nueva Venta</div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-2 bg-slate-50">
                @forelse($cart as $item)
                    <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm flex justify-between items-center">
                        <div class="flex-1 min-w-0 pr-2">
                            <div class="font-bold text-sm text-slate-800 truncate">{{ $item['name'] }}</div>
                            <div class="text-xs text-slate-500 font-mono">
                                {{ $item['quantity'] }} x {{ $currency }}{{ number_format($item['price'], 2) }}
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-slate-900 text-sm">{{ $currency }}{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                            <button wire:click="removeFromCart({{ $item['id'] }})" class="text-red-400 hover:text-red-600"><x-icon name="o-trash" class="w-4 h-4" /></button>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-slate-400 opacity-50">
                        <x-icon name="o-shopping-bag" class="w-16 h-16 mb-2" />
                        <p>Carrito vacío</p>
                    </div>
                @endforelse
            </div>

            <div class="p-5 bg-white border-t border-slate-200 shadow-lg z-10">
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm text-slate-500">
                        <span>Subtotal</span>
                        <span>{{ $currency }}{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-slate-500">
                        <span>{{ $tax_name }} ({{ $tax_rate }}%)</span>
                        <span>{{ $currency }}{{ number_format($tax_amount, 2) }}</span>
                    </div>
                    <div class="border-t border-dashed border-slate-200 my-2 pt-2 flex justify-between items-end">
                        <span class="font-bold text-slate-700 text-lg">TOTAL</span>
                        <span class="text-3xl font-black text-slate-900 tracking-tight">{{ $currency }}{{ number_format($total, 2) }}</span>
                    </div>
                </div>
                
                <button 
                    wire:click="checkout" 
                    class="w-full py-3.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg shadow-lg shadow-blue-600/20 transition-all flex justify-center items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    {{ empty($cart) ? 'disabled' : '' }}
                >
                    <span wire:loading.remove class="flex items-center gap-2"><x-icon name="o-check-circle" class="w-6 h-6" /> COBRAR</span>
                    <span wire:loading class="loading loading-spinner loading-md"></span>
                </button>
            </div>
        </div>
    </div>
</div>
