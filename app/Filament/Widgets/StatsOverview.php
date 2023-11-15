<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{   
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '15s'; //interval between polling intervals to update the data

    protected static bool $isLazy = true;
    protected function getStats(): array
    {
        return [
            //
            Stat::make('total customers',Customer::count())->description('Increase in customers')->descriptionIcon('heroicon-m-arrow-trending-up')->color('success')->chart([1,9,7,3,7,1]),
            Stat::make('Total Products',Product::count())->description('Total products in app')->descriptionIcon('heroicon-m-arrow-trending-down')->color('danger')->chart([3,6,3,1,3,4,4,5,5,2]),
            Stat::make('Pending Orders',Order::where('status','=','pending')->count())->description('Total products in app')->descriptionIcon('heroicon-m-arrow-trending-down')->color('danger')->chart([3,6,3,1,3,4,4,5,5,2]),
        ];
    }
}
