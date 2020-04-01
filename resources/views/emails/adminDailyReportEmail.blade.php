@extends('emails.layouts.app')

@section('content')

    <h2>Hospitals with low amount of assets:</h2>
    @forelse($dangerStock as $hospital)
        @if(count($hospital->stocks) > 0)
            <h3>{{ $hospital->name }}</h3>
            <ul>
                @foreach ($hospital->stocks as $stock)
                    <li>{{ $stock->asset->name . ': ' . $stock->current_stock }} left</li>
                @endforeach
            </ul>
        @endif
    @empty
        <h3>There are no danger in stock yet.</h3>
    @endforelse

    <h2>Changes in the amounts for last 24 hours:</h2>
    @if(count($transactions) > 0)
        <table style="border: 1px solid #ddd;border-collapse: collapse;">
            <thead>
                <th style="border: 1px solid #ddd;width: 200px">Hospital</th>
                <th style="border: 1px solid #ddd;width: 200px">Asset</th>
                <th style="border: 1px solid #ddd;width: 200px">Items of transactions</th>
                <th style="border: 1px solid #ddd;width: 100px">Items left</th>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    @if($loop->index == 10)
                        @break;
                    @endif
                    <tr>
                        <td style="border: 1px solid #ddd;">{{ $transaction->team->name }}</td>
                        <td style="border: 1px solid #ddd;">{{ $transaction->asset->name }}</td>
                        <td style="border: 1px solid #ddd;">{{ $transaction->sum }}</td>
                        <td style="border: 1px solid #ddd;">{{ $transaction->current_stock }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if(count($transactions) > 10)
            <p><a href="{{ route('admin.transactions.index') }}">Log in to view more transactions</a></p>
        @endif
    @else
        <h3>No transactions were made during last 24 hours.</h3>
    @endif
@endsection
