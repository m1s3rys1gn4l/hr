@extends('layouts.app')

@section('content')
<div class="card">
    <h2>Salary Payout</h2>
    <p class="muted">Enter employee IDs separated by commas, spaces, or + (e.g., "2+3" or "1, 4, 7")</p>

    <form action="{{ route('payouts.create') }}" method="GET">
        <div class="row">
            <div>
                <label>Employee IDs</label>
                <textarea name="employee_codes" rows="3" placeholder="e.g., 1+2+3 or 1, 2, 3" required>{{ request('employee_codes') }}</textarea>
            </div>
            <div style="display:flex;align-items:end;">
                <button class="btn" type="submit">Search</button>
            </div>
        </div>
    </form>

    @if(!empty($searchError))
        <p class="error" style="margin-top:10px;">{{ $searchError }}</p>
    @endif
</div>

@if(session('status'))
    <div class="card" style="background:#d4edda;border-color:#c3e6cb;color:#155724;">
        <p style="margin:0;">✓ {{ session('status') }}</p>
    </div>
@endif

@if($errors->has('bulk'))
    <div class="card" style="background:#f8d7da;border-color:#f5c6cb;color:#721c24;">
        <h4>Errors:</h4>
        <ul style="margin:8px 0 0 20px;">
            @foreach($errors->get('bulk') as $errorList)
                @foreach((array)$errorList as $error)
                    <li>{{ $error }}</li>
                @endforeach
            @endforeach
        </ul>
    </div>
@endif

@if(!empty($employees))
<form action="{{ route('payouts.store') }}" method="POST">
    @csrf
    
    @foreach($employees as $index => $employee)
    <div class="card">
        <h3>Employee {{ $employee->employee_code }} - {{ $employee->name }}</h3>
        <div class="row" style="margin-bottom:12px;">
            <div><strong>ID:</strong> {{ $employee->employee_code }}</div>
            <div><strong>Name:</strong> {{ $employee->name }}</div>
            <div><strong>Current Balance:</strong> {{ number_format($employee->current_balance, 2) }} SAR</div>
            <div><strong>Last Payout:</strong> {{ optional($employee->payouts()->latest('paid_at')->first()?->paid_at)->format('Y-m-d H:i') ?: '-' }}</div>
        </div>

        <input type="hidden" name="payouts[{{ $index }}][employee_id]" value="{{ $employee->id }}">

        <div class="row">
            <div>
                <label>Pay Amount (SAR)</label>
                <input type="number" name="payouts[{{ $index }}][amount]" min="0.01" step="0.01" 
                       value="{{ old('payouts.'.$index.'.amount') }}" 
                       max="{{ $employee->current_balance }}" required>
            </div>
            <div>
                <label>Note (optional)</label>
                <input type="text" name="payouts[{{ $index }}][note]" 
                       value="{{ old('payouts.'.$index.'.note') }}">
            </div>
        </div>
    </div>
    @endforeach

    <div class="card">
        <button class="btn" type="submit">Process All Payouts</button>
    </div>
</form>
@endif
@endsection
