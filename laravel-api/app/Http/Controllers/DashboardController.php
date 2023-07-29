<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $expenseSummary = ExpenseCategory::select('id', 'name')
            ->addSelect([
                'expenses_sum' => Expense::select(DB::raw('SUM(amount)'))
                    ->whereColumn('expense_category_id', 'expense_categories.id')
                    ->where('user_id', $request->user()->id)
            ])
            ->get();

        return $expenseSummary;
    }
}