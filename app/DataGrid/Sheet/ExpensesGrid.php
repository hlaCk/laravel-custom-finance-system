<?php

namespace App\DataGrid\Sheet;

use App\Models\Sheet\Expense;
use Yazan\DataTable\DataGrid;

class ExpensesGrid extends DataGrid
{

    public $model = Expense::class;


}
