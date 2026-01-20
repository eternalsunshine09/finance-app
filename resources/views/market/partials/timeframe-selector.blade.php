@php
if (!isset($timeframes)) {
$timeframes = [
'1d' => '1D',
'5d' => '1W',
'1mo' => '1M',
'3mo' => '3M',
'6mo' => '6M',
'1y' => '1Y',
'2y' => '2Y',
'5y' => '5Y',
'max' => 'All'
];
}

$timeframe = $timeframe ?? '1mo';
@endphp

<div class="flex items-center justify-between mb-8">
    <div class="flex items-center space-x-2">
        @foreach($timeframes as $tfValue => $tfLabel)
        <a href="{{ request()->fullUrlWithQuery(['timeframe' => $tfValue]) }}" class="px-3 py-1.5 text-sm font-medium rounded-lg transition-all duration-200
                  {{ $timeframe == $tfValue 
                     ? 'bg-gray-900 text-white shadow-sm' 
                     : 'bg-gray-100 text-gray-700 hover:bg-gray-200 hover:text-gray-900' }}">
            {{ $tfLabel }}
        </a>
        @endforeach
    </div>

    <div class="text-right">
        <div class="text-3xl font-bold text-gray-900">
            @if(Route::currentRouteName() == 'market.crypto')
            ${{ number_format($btc['price'] ?? 0, 2) }}
            @elseif(Route::currentRouteName() == 'market.us')
            ${{ number_format($sp500['price'] ?? 0, 2) }}
            @else
            {{ number_format($ihsg['price'] ?? 0, 2) }}
            @endif
        </div>
        <div class="flex items-center justify-end gap-2 mt-2">
            @php
            $data = $btc ?? $sp500 ?? $ihsg ?? [];
            $changePercent = $data['change_percent'] ?? 0;
            @endphp
            <span class="text-sm font-medium {{ $changePercent >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $changePercent > 0 ? '+' : '' }}{{ number_format($changePercent, 2) }}%
            </span>
            <span class="text-xs text-gray-500">
                {{ $changePercent >= 0 ? '▲' : '▼' }}
            </span>
        </div>
    </div>
</div>