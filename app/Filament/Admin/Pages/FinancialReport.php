<?php

namespace App\Filament\Admin\Pages;

use App\Models\Order;
use App\Models\PrintJob;
use App\Models\JournalEntry;
use App\Models\ChartOfAccount;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static string $view = 'filament.admin.pages.financial-report';

    protected static ?string $navigationGroup = 'Reports';

    protected static ?string $navigationLabel = 'Financial Report';

    protected static ?int $navigationSort = 3;

    public ?array $filters = [
        'from_date' => null,
        'to_date' => null,
        'report_type' => 'income_statement',
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
            'filters.report_type' => 'required|in:income_statement,balance_sheet,cash_flow,revenue_summary',
        ]);

        switch ($this->filters['report_type']) {
            case 'income_statement':
                $this->generateIncomeStatement();
                break;
            case 'balance_sheet':
                $this->generateBalanceSheet();
                break;
            case 'revenue_summary':
                $this->generateRevenueSummary();
                break;
        }

        Notification::make()
            ->title('Financial report generated successfully')
            ->success()
            ->send();
    }

    protected function generateIncomeStatement()
    {
        // Calculate revenue
        $revenue = Order::whereBetween('order_date', [
                Carbon::parse($this->filters['from_date']),
                Carbon::parse($this->filters['to_date'])
            ])
            ->where('payment_status', 'paid')
            ->sum('total');

        // Calculate cost of goods sold (toner, paper)
        $cogs = PrintJob::whereBetween('completed_at', [
                Carbon::parse($this->filters['from_date']),
                Carbon::parse($this->filters['to_date'])
            ])
            ->sum('total_price') * 0.3; // Assuming 30% cost

        // Calculate expenses (salaries, rent, utilities)
        $salaryExpenses = JournalEntry::whereBetween('entry_date', [
                Carbon::parse($this->filters['from_date']),
                Carbon::parse($this->filters['to_date'])
            ])
            ->whereHas('account', function ($query) {
                $query->where('type', 'expense');
            })
            ->sum('amount');

        $grossProfit = $revenue - $cogs;
        $netIncome = $grossProfit - $salaryExpenses;

        $this->reportData = [
            'revenue' => $revenue,
            'cogs' => $cogs,
            'gross_profit' => $grossProfit,
            'operating_expenses' => $salaryExpenses,
            'net_income' => $netIncome,
            'profit_margin' => $revenue > 0 ? ($netIncome / $revenue) * 100 : 0,
        ];

        // Get revenue breakdown by source
        $this->summaryStats = [
            'revenue_by_type' => Order::whereBetween('order_date', [
                    Carbon::parse($this->filters['from_date']),
                    Carbon::parse($this->filters['to_date'])
                ])
                ->select('source_channel', DB::raw('SUM(total) as total'))
                ->groupBy('source_channel')
                ->get(),

            'revenue_by_month' => Order::whereBetween('order_date', [
                    Carbon::parse($this->filters['from_date']),
                    Carbon::parse($this->filters['to_date'])
                ])
                ->select(DB::raw('DATE_FORMAT(order_date, "%Y-%m") as month'), DB::raw('SUM(total) as total'))
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
        ];
    }

    protected function generateBalanceSheet()
    {
        // Assets
        $cash = ChartOfAccount::where('account_code', 'like', '1010%')->sum('current_balance');
        $accountsReceivable = ChartOfAccount::where('account_code', 'like', '1200%')->sum('current_balance');
        $inventory = ChartOfAccount::where('account_code', 'like', '1300%')->sum('current_balance');
        $fixedAssets = ChartOfAccount::where('account_code', 'like', '1400%')->sum('current_balance');

        $totalAssets = $cash + $accountsReceivable + $inventory + $fixedAssets;

        // Liabilities
        $accountsPayable = ChartOfAccount::where('account_code', 'like', '2100%')->sum('current_balance');
        $taxPayable = ChartOfAccount::where('account_code', 'like', '2200%')->sum('current_balance');
        $accruedExpenses = ChartOfAccount::where('account_code', 'like', '2300%')->sum('current_balance');

        $totalLiabilities = $accountsPayable + $taxPayable + $accruedExpenses;

        // Equity
        $shareCapital = ChartOfAccount::where('account_code', 'like', '3100%')->sum('current_balance');
        $retainedEarnings = ChartOfAccount::where('account_code', 'like', '3200%')->sum('current_balance');

        $totalEquity = $shareCapital + $retainedEarnings;

        $this->reportData = [
            'assets' => [
                'cash' => $cash,
                'accounts_receivable' => $accountsReceivable,
                'inventory' => $inventory,
                'fixed_assets' => $fixedAssets,
                'total' => $totalAssets,
            ],
            'liabilities' => [
                'accounts_payable' => $accountsPayable,
                'tax_payable' => $taxPayable,
                'accrued_expenses' => $accruedExpenses,
                'total' => $totalLiabilities,
            ],
            'equity' => [
                'share_capital' => $shareCapital,
                'retained_earnings' => $retainedEarnings,
                'total' => $totalEquity,
            ],
        ];
    }

    protected function generateRevenueSummary()
    {
        $this->reportData = Order::whereBetween('order_date', [
                Carbon::parse($this->filters['from_date']),
                Carbon::parse($this->filters['to_date'])
            ])
            ->select(
                'customer_id',
                DB::raw('COUNT(*) as order_count'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('AVG(total) as average_order_value')
            )
            ->groupBy('customer_id')
            ->with('customer')
            ->orderBy('total_revenue', 'desc')
            ->get();

        $this->summaryStats = [
            'total_revenue' => $this->reportData->sum('total_revenue'),
            'total_orders' => $this->reportData->sum('order_count'),
            'average_order' => $this->reportData->avg('average_order_value'),
            'top_customer' => $this->reportData->first(),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Filter Financial Report')
                ->schema([
                    Select::make('filters.report_type')
                        ->label('Report Type')
                        ->options([
                            'income_statement' => 'Income Statement (Profit & Loss)',
                            'balance_sheet' => 'Balance Sheet',
                            'revenue_summary' => 'Revenue Summary',
                        ])
                        ->required()
                        ->reactive(),

                    DatePicker::make('filters.from_date')
                        ->label('From Date')
                        ->required()
                        ->default(now()->startOfMonth()),

                    DatePicker::make('filters.to_date')
                        ->label('To Date')
                        ->required()
                        ->default(now()->endOfMonth()),
                ])->columns(3)
        ];
    }
}
