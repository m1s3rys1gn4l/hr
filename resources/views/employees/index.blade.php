@extends('layouts.app')

@section('content')
<div class="card" style="display:flex;justify-content:space-between;align-items:center;">
    <div>
        <h2>Active Employees</h2>
        <p class="muted">Search and manage active employees</p>
    </div>
    <div style="display:flex;gap:8px;">
        <a class="btn btn-secondary" href="{{ route('employees.left') }}">Left Employees</a>
        <a class="btn" href="{{ route('employees.create') }}">Add Employee</a>
    </div>
</div>

<div class="card">
    <form method="GET" action="{{ route('employees.index') }}" class="row">
        <div>
            <label>Search (ID, Name, Phone)</label>
            <input type="text" name="search" value="{{ $search }}" placeholder="e.g. 1 or Ahmed">
        </div>
        <div>
            <label>Show</label>
            <select name="per_page">
                <option value="50" {{ $perPage === '50' ? 'selected' : '' }}>50</option>
                <option value="100" {{ $perPage === '100' ? 'selected' : '' }}>100</option>
                <option value="all" {{ $perPage === 'all' ? 'selected' : '' }}>All</option>
            </select>
        </div>
        <div style="display:flex;align-items:end;gap:8px;">
            <button class="btn" type="submit">Apply</button>
            <a class="btn btn-secondary" href="{{ route('employees.index') }}">Reset</a>
        </div>
    </form>
</div>

<div class="card">
    <h3>Employee List</h3>
    <table>
        <thead>
        <tr>
            <th>Unique ID</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Daily Salary</th>
            <th>Current Balance</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @forelse($activeEmployees as $employee)
            <tr>
                <td>{{ $employee->employee_code }}</td>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->phone ?: '-' }}</td>
                <td>{{ number_format($employee->daily_salary, 2) }} SAR</td>
                <td>{{ number_format($employee->current_balance, 2) }} SAR</td>
                <td>
                    <a class="btn btn-secondary" href="{{ route('employees.show', $employee) }}">Profile</a>
                    <button
                        type="button"
                        class="btn"
                        data-open-payout
                        data-employee-id="{{ $employee->id }}"
                        data-employee-name="{{ $employee->name }}"
                        data-employee-code="{{ $employee->employee_code }}"
                        data-employee-balance="{{ number_format((float) $employee->current_balance, 2, '.', '') }}"
                    >
                        Payout
                    </button>
                    <form action="{{ route('employees.mark-left', $employee) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-danger" type="submit">Set Left</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">No active employees.</td></tr>
        @endforelse
        </tbody>
    </table>

    @if(method_exists($activeEmployees, 'links'))
        <div style="margin-top:10px;">{{ $activeEmployees->links() }}</div>
    @endif
</div>

<div id="quick-payout-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.35); z-index:1000; align-items:center; justify-content:center; padding:16px;">
    <div style="width:100%; max-width:420px; background:#fff; border:1px solid #e5e7eb; border-radius:8px; padding:16px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h3 style="margin:0;">Quick Payout</h3>
            <button type="button" id="quick-payout-close" class="btn btn-secondary">Close</button>
        </div>

        <p class="muted" id="quick-payout-employee-label" style="margin-top:0;"></p>

        <form method="POST" action="{{ route('payouts.store') }}">
            @csrf
            <input type="hidden" name="return_to" value="{{ request()->fullUrl() }}">
            <input type="hidden" name="quick_payout" value="1">
            <input type="hidden" name="payouts[0][employee_id]" id="quick-payout-employee-id">

            @if(old('quick_payout') && ($errors->has('bulk') || $errors->has('payouts.0.employee_id') || $errors->has('payouts.0.amount') || $errors->has('payouts.0.note')))
                <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:6px; padding:10px; margin-bottom:10px; font-size:13px;">
                    <strong style="display:block; margin-bottom:4px;">Please fix these issues:</strong>
                    <ul style="margin:0; padding-left:18px;">
                        @foreach($errors->get('payouts.0.employee_id') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                        @foreach($errors->get('payouts.0.amount') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                        @foreach($errors->get('payouts.0.note') as $message)
                            <li>{{ $message }}</li>
                        @endforeach
                        @foreach($errors->get('bulk') as $message)
                            @if(is_array($message))
                                @foreach($message as $subMessage)
                                    <li>{{ $subMessage }}</li>
                                @endforeach
                            @else
                                <li>{{ $message }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="margin-bottom:10px;">
                <label for="quick-payout-amount">Amount (SAR)</label>
                <input type="number" min="0.01" step="0.01" name="payouts[0][amount]" id="quick-payout-amount" required>
            </div>

            <div style="margin-bottom:12px;">
                <label for="quick-payout-note">Note (optional)</label>
                <input type="text" name="payouts[0][note]" id="quick-payout-note" maxlength="255" placeholder="e.g. Weekly payout">
            </div>

            <div style="display:flex; gap:8px; justify-content:flex-end;">
                <button type="button" id="quick-payout-cancel" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn">Save Payout</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        const modal = document.getElementById('quick-payout-modal');
        if (!modal) {
            return;
        }

        const openButtons = document.querySelectorAll('[data-open-payout]');
        const closeButton = document.getElementById('quick-payout-close');
        const cancelButton = document.getElementById('quick-payout-cancel');
        const employeeIdInput = document.getElementById('quick-payout-employee-id');
        const amountInput = document.getElementById('quick-payout-amount');
        const noteInput = document.getElementById('quick-payout-note');
        const employeeLabel = document.getElementById('quick-payout-employee-label');
        const shouldReopenFromOldInput = {{ old('quick_payout') ? 'true' : 'false' }};
        const oldEmployeeId = @json(old('payouts.0.employee_id'));
        const oldAmount = @json(old('payouts.0.amount'));
        const oldNote = @json(old('payouts.0.note'));

        function closeModal() {
            modal.style.display = 'none';
            employeeIdInput.value = '';
            amountInput.value = '';
            noteInput.value = '';
            employeeLabel.textContent = '';
        }

        openButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const employeeId = button.getAttribute('data-employee-id');
                const employeeName = button.getAttribute('data-employee-name') || '';
                const employeeCode = button.getAttribute('data-employee-code') || '';
                const employeeBalance = button.getAttribute('data-employee-balance') || '0.00';

                employeeIdInput.value = employeeId || '';
                amountInput.value = employeeBalance;
                employeeLabel.textContent = `Employee #${employeeCode} - ${employeeName} (Balance: ${employeeBalance} SAR)`;
                modal.style.display = 'flex';
                amountInput.focus();
            });
        });

        closeButton.addEventListener('click', closeModal);
        cancelButton.addEventListener('click', closeModal);

        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal();
            }
        });

        if (shouldReopenFromOldInput) {
            const matchingButton = oldEmployeeId
                ? document.querySelector(`[data-open-payout][data-employee-id="${oldEmployeeId}"]`)
                : null;

            if (matchingButton) {
                matchingButton.click();
            } else {
                employeeIdInput.value = oldEmployeeId || '';
                employeeLabel.textContent = oldEmployeeId
                    ? `Employee ID: ${oldEmployeeId}`
                    : '';
                modal.style.display = 'flex';
            }

            if (oldAmount !== null && oldAmount !== '') {
                amountInput.value = oldAmount;
            }

            if (oldNote !== null && oldNote !== '') {
                noteInput.value = oldNote;
            }
        }
    })();
</script>
@endsection
