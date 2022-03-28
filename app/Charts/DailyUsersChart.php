<?php

namespace App\Charts;

use Carbon\Carbon;
use Chartisan\PHP\Chartisan;
use Fidum\ChartTile\Charts\Chart;
use Illuminate\Http\Request;

class DailyUsersChart extends Chart
{
    public function handler(Request $request): Chartisan
    {
        $date = Carbon::now()->subMonth()->startOfDay();

        $data = collect(range(0, 12))->map(function ($days) use ($date) {
            return [
                'x' => $date->clone()->addDays($days)->toDateString(),
                'y' => rand(100, 500),
            ];
        });

        return Chartisan::build()
            ->labels($data->pluck('x')->toArray())
            ->dataset('Example Data', $data->toArray());
    }

    public function type(): string
    {
        return 'bar';
    }

    public function options(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            // etc ...
        ];
    }
}
