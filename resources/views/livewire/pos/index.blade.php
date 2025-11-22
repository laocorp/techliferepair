<?php

use Livewire\Volt\Component;
use App\Models\Part;
use App\Models\Sale;     // <--- Nuevo
use App\Models\SaleItem; // <--- Nuevo
use Mary\Traits\Toast;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

new 
#[Layout('layouts.app')]
class extends Component {
    use Toast;

    public string $search = '';
    public array $cart = []; // Carrito de compras: [part_id => cantidad]
    public float $total = 0.0;

    // Agregar item al carrito
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
                $this->error('No hay más unidades disponibles');
            }
        } else {
            $this->cart[$partId] = [
                'id' => $part->id,
                'name' => $part->name,
                'sku' => $part->sku,
                'price' => $part->price,
                'quantity' => 1,
                'max_stock' => $part->stock
            ];
        }
        
        $this->calculateTotal();
        $this->success('Agregado al carrito');
    }

    public function removeFromCart($partId): void
    {
        unset($this->cart[$partId]);
        $this->calculateTotal();
    }

    public function calculateTotal(): void
    {
        $this->total = 0;
        foreach ($this->cart as $item) {
            $this->total += $item['price'] * $item['quantity'];
        }
    }

    // --- NUEVA LÓGICA DE COBRO ---
    public function checkout(): void
    {
        if (empty($this->cart)) {
            $this->error('El carrito está vacío');
            return;
        }

        // Usamos una transacción para que todo se guarde o nada (seguridad)
        DB::transaction(function () {
            
            // 1. Crear la Venta
            $sale = Sale::create([
                'user_id' => auth()->id(),
                'total' => $this->total,
                'payment_method' => 'cash', // Podrías agregar un select para esto luego
                'company_id' => auth()->user()->company_id // El trait lo hace, pero por si acaso
            ]);

            // 2. Procesar cada item
            foreach ($this->cart as $item) {
                $part = Part::find($item['id']);
                
                if ($part && $part->stock >= $item['quantity']) {
                    // Descontar stock
                    $part->decrement('stock', $item['quantity']);

                    // Guardar detalle
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'part_id' => $part->id,
                        'quantity' => $item['quantity'],
                        'price' => $item['price']
                    ]);
                }
            }

            // 3. Disparar la impresión del ticket (Redirigir a nueva pestaña)
            // Usamos 'flash' session o evento de navegador para abrir el PDF
            $this->js("window.open('/pos/ticket/{$sale->id}', '_blank')");
        });
        
        $this->success('Venta registrada correctamente.');
        $this->cart = [];
        $this->total = 0;
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
    {{-- ... (El HTML se mantiene igual que antes) ... --}}
    <!-- COLUMNA IZQUIERDA: PRODUCTOS -->
    <div class="lg:col-span-2 flex flex-col h-full">
        <!-- Barra de Búsqueda -->
        <div class="mb-4 bg-white p-4 rounded-xl shadow-sm border border-slate-200">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-icon name="o-magnifying-glass" class="w-5 h-5 text-slate-400" />
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search" 
                    class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all"
                    placeholder="Buscar producto por nombre o código SKU..." 
                    autofocus
                />
            </div>
        </div>
        
        <!-- Grid de Productos -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 overflow-y-auto p-1 flex-1 content-start">
            @foreach($parts as $part)
                <div 
                    class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:border-blue-400 cursor-pointer transition-all flex flex-col justify-between h-40 group relative overflow-hidden"
                    wire:click="addToCart({{ $part->id }})"
                >
                    <!-- Efecto visual al hacer hover -->
                    <div class="absolute inset-0 bg-blue-50 opacity-0 group-hover:opacity-10 transition-opacity"></div>

                    <div>
                        <div class="flex justify-between items-start mb-1">
                            <div class="text-xs font-bold text-slate-400 font-mono bg-slate-100 px-1.5 py-0.5 rounded">{{ $part->sku }}</div>
                        </div>
                        <div class="font-bold text-slate-800 leading-tight line-clamp-2 text-sm h-10">{{ $part->name }}</div>
                    </div>
                    
                    <div class="flex justify-between items-end mt-2 relative z-10">
                        <div class="text-xs font-medium text-slate-500">
                            Stock: <span class="{{ $part->stock <= 5 ? 'text-red-500 font-bold' : '' }}">{{ $part->stock }}</span>
                        </div>
                        <div class="text-lg font-black text-blue-600 group-hover:scale-110 transition-transform">
                            ${{ number_format($part->price, 2) }}
                        </div>
                    </div>
                </div>
            @endforeach
            
            @if($parts->isEmpty())
                <div class="col-span-full flex flex-col items-center justify-center py-10 text-slate-400 h-64 bg-slate-50 rounded-xl border-2 border-dashed border-slate-200">
                    <x-icon name="o-face-frown" class="w-12 h-12 mb-3 opacity-50" />
                    <p class="font-medium">No se encontraron productos</p>
                    <p class="text-xs mt-1">Intenta con otro término de búsqueda</p>
                </div>
            @endif
        </div>
    </div>

    <!-- COLUMNA DERECHA: TICKET DE VENTA -->
    <div class="lg:col-span-1 flex flex-col h-full">
        <div class="bg-white border border-slate-200 rounded-xl shadow-xl flex flex-col h-full overflow-hidden">
            <!-- Cabecera del Ticket -->
            <div class="p-5 border-b border-slate-100 bg-slate-900 text-white">
                <div class="flex justify-between items-center">
                    <h2 class="font-bold text-lg flex items-center gap-2">
                        <x-icon name="o-shopping-cart" class="w-5 h-5" /> Venta Actual
                    </h2>
                    <div class="text-xs font-mono opacity-70">{{ date('d/m/Y H:i') }}</div>
                </div>
            </div>

            <!-- Lista de Items (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50">
                @forelse($cart as $item)
                    <div class="bg-white p-3 rounded-lg border border-slate-200 shadow-sm flex justify-between items-center group hover:border-blue-200 transition-colors">
                        <div class="flex-1 min-w-0 pr-2">
                            <div class="font-bold text-sm text-slate-800 truncate">{{ $item['name'] }}</div>
                            <div class="text-xs text-slate-500 font-mono mt-0.5">
                                {{ $item['quantity'] }} x ${{ number_format($item['price'], 2) }}
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="font-bold text-slate-900 text-sm">${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                            <button 
                                wire:click="removeFromCart({{ $item['id'] }})" 
                                class="text-slate-300 hover:text-red-500 transition-colors p-1 rounded-full hover:bg-red-50"
                                title="Eliminar"
                            >
                                <x-icon name="o-trash" class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-slate-400 opacity-60">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                            <x-icon name="o-shopping-bag" class="w-10 h-10 text-slate-300" />
                        </div>
                        <p class="font-medium">El carrito está vacío</p>
                        <p class="text-xs mt-1">Selecciona productos para comenzar</p>
                    </div>
                @endforelse
            </div>

            <!-- Resumen y Totales -->
            <div class="p-5 bg-white border-t border-slate-200 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm text-slate-500">
                        <span>Subtotal</span>
                        <span>${{ number_format($total / 1.12, 2) }}</span> <!-- Ejemplo IVA 12% -->
                    </div>
                    <div class="flex justify-between text-sm text-slate-500">
                        <span>Impuestos (aprox)</span>
                        <span>${{ number_format($total - ($total / 1.12), 2) }}</span>
                    </div>
                    <div class="border-t border-dashed border-slate-200 my-2 pt-2 flex justify-between items-end">
                        <span class="font-bold text-slate-700 text-lg">Total</span>
                        <span class="text-3xl font-black text-slate-900 tracking-tight">${{ number_format($total, 2) }}</span>
                    </div>
                </div>
                
                <button 
                    wire:click="checkout" 
                    wire:loading.attr="disabled"
                    class="w-full py-3.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg shadow-lg shadow-blue-600/20 transition-all active:scale-95 flex justify-center items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    {{ empty($cart) ? 'disabled' : '' }}
                >
                    <span wire:loading.remove class="flex items-center gap-2">
                        <x-icon name="o-check-circle" class="w-6 h-6" /> COBRAR
                    </span>
                    <span wire:loading class="loading loading-spinner loading-md"></span>
                </button>
            </div>
        </div>
    </div>
</div>
