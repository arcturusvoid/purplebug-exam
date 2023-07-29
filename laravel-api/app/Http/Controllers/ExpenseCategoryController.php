<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function __construct()
    {
        // Apply 'admin' middleware to the specified methods
        $this->middleware('admin')->only(['store', 'update', 'destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ExpenseCategory::orderByDesc('created_at')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        ExpenseCategory::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json(['status' => 'Added Expense Category!'], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $expenseCategory->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json(['status' => 'Expense Category Updated!'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->delete();

        return response()->json(['status' => 'Expense Category Deleted!'], 200);
    }
}