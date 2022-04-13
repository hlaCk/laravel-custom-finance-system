<?php

namespace App\Http\Controllers\Api\Sheet;

use App\DataGrid\Sheet\ExpensesGrid;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function all(Request $request)
    {
        $collection = (new ExpensesGrid())->render();

        return ['success' => true, 'collection' => $collection];
    }
}
