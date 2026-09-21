<?php

namespace App\Http\Controllers;

use App\Actions\CloseCashierShift;
use App\Actions\OpenCashierShift;
use App\Actions\RecordCashMovement;
use App\Enums\CashMovementType;
use App\Http\Requests\CashMovementRequest;
use App\Http\Requests\CloseShiftRequest;
use App\Http\Requests\OpenShiftRequest;
use App\Models\CashierShift;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShiftController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        $shifts = CashierShift::query()
            ->where('company_id', $user->company_id)
            ->with(['user:id,name', 'branch:id,name'])
            ->when($user->cannot('reports.view'), fn ($q) => $q->where('user_id', $user->id))
            ->latest('opened_at')
            ->paginate(20);

        return Inertia::render('shifts/Index', [
            'shifts' => $shifts,
            'currentShift' => $user->currentShift()?->load('cashMovements'),
        ]);
    }

    public function open(OpenShiftRequest $request, OpenCashierShift $action): RedirectResponse
    {
        $action->handle(
            $request->user(),
            $request->user()->branch_id,
            (float) $request->opening_cash,
            $request->note,
        );

        return redirect()->route('pos.index')->with('success', 'Shift opened.');
    }

    public function close(CloseShiftRequest $request, CashierShift $shift, CloseCashierShift $action): RedirectResponse
    {
        abort_if(
            $shift->user_id !== $request->user()->id && $request->user()->cannot('reports.view'),
            403
        );

        $action->handle($shift, $request->user(), (float) $request->actual_cash, $request->note);

        return redirect()->route('shifts.index')->with('success', 'Shift closed.');
    }

    public function cashMovement(CashMovementRequest $request, CashierShift $shift, RecordCashMovement $action): RedirectResponse
    {
        abort_if(
            $shift->user_id !== $request->user()->id && $request->user()->cannot('reports.view'),
            403
        );

        $action->handle(
            $shift,
            CashMovementType::from($request->type),
            (float) $request->amount,
            $request->user(),
            $request->reason,
        );

        return back()->with('success', 'Cash movement recorded.');
    }
}
