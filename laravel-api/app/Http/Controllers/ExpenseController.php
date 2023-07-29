<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{

    public function index(Request $request)
    {
        $expenses = $request->user()->expenses()->with('expenseCategory')->orderByDesc('created_at')->get();
        return $expenses;
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Expense::create([
            'user_id' => $request->user()->id,
            'expense_category_id' => $request->expenseCategoryId,
            'amount' => $request->amount,
            'entry_date' => $request->entryDate,
        ]);

        return response()->json(['status' => 'Expense Added!'], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $expense->update([
            'expense_category_id' => $request->expenseCategoryId,
            'amount' => $request->amount,
            'entry_date' => $request->entryDate,
        ]);

        return response()->json(['status' => 'Expense Updated!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return response()->json(['status' => 'Expense Deleted!'], 200);
    }
}