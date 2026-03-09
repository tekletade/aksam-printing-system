<?php

namespace App\Filament\Admin\Pages;

use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Printer;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class InventoryReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static string $view = 'filament.admin.pages.inventory-report';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Inventory Report';

    protected static ?int $navigationSort = 4;

    public ?array $filters = [
        'from_date' => null,
        'to_date' => null,
        'category' => null,
        'status' => 'all',
    ];

    public $reportData = null;
    public $summaryStats = null;

    public function mount(): void
    {
        $this->filters['from_date'] = now()->startOfMonth()->format('Y-m-d');
        $this->filters['to_date'] = now()->endOfMonth()->format('Y-m-d');
    }

    public function generateReport()
    {
        $this->validate([
            'filters.from_date' => 'required|date',
            'filters.to_date' => 'required|date|after_or_equal:filters.from_date',
        ]);

        // Get inventory items with current stock
        $query = InventoryItem::query();

        if ($this->filters['category']) {
            $query->where('category', $this->filters['category']);
        }

        if ($this->filters['status'] === 'low_stock') {
            $query->whereColumn('current_stock', '<=', 'minimum_stock');
        } elseif ($this->filters['status'] === 'out_of_stock') {
            $query->where('current_stock', '<=', 0);
        }

        $this->reportData = $query->get();

        // Get transaction summary
        $transactions = InventoryTransaction::whereBetween('created_at', [
                Carbon::parse($this->filters['from_date']),
                Carbon::parse($this->filters['to_date'])
            ])
            ->select(
                'transaction_type',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(quantity) as total_quantity'),
                DB::raw('SUM(total_amount) as total_value')
            )
            ->groupBy('transaction_type')
            ->get();

        // Calculate summary statistics
        $this->summaryStats = [
            'total_items' => $this->reportData->count(),
            'total_value' => $this->reportData->sum(function ($item) {
                return $item->current_stock * $item->unit_cost;
            }),
            'low_stock_items' => $this->reportData->filter(function ($item) {
                return $item->current_stock <= $item->minimum_stock;
            })->count(),
            'out_of_stock' => $this->reportData->filter(function ($item) {
                return $item->current_stock <= 0;
            })->count(),
            'categories' => $this->reportData->groupBy('category')->map->count(),
            'transactions' => $transactions,
            'total_transactions' => $transactions->sum('count'),
            'total_transaction_value' => $transactions->sum('total_value'),
        ];

        // Get usage by printer
        $this->summaryStats['usage_by_printer'] = InventoryTransaction::whereBetween('created_at', [
                Carbon::parse($this->filters['from_date']),
                Carbon::parse($this->filters['to_date'])
            ])
            ->where('transaction_type', 'usage')
            ->whereNotNull('printer_id')
            ->select('printer_id', DB::raw('SUM(quantity) as total_used'), DB::raw('SUM(total_amount) as total_cost'))
            ->with('printer')
            ->groupBy('printer_id')
            ->get();

        Notification::make()
            ->title('Inventory report generated successfully')
            ->success()
            ->send();
    }

    public function getCategoryOptions()
    {
        return InventoryItem::distinct('category')
            ->pluck('category', 'category')
            ->toArray();
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Filter Inventory Report')
                ->schema([
                    Select::make('filters.category')
                        ->label('Category')
                        ->options(fn () => $this->getCategoryOptions())
                        ->placeholder('All Categories'),

                    Select::make('filters.status')
                        ->label('Stock Status')
                        ->options([
                            'all' => 'All Items',
                            'low_stock' => 'Low Stock',
                            'out_of_stock' => 'Out of Stock',
                        ])
                        ->default('all'),

                    DatePicker::make('filters.from_date')
                        ->label('From Date')
                        ->required()
                        ->default(now()->startOfMonth()),

                    DatePicker::make('filters.to_date')
                        ->label('To Date')
                        ->required()
                        ->default(now()->endOfMonth()),
                ])->columns(2)
        ];
    }
}
