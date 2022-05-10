<?php

namespace App\Providers\Observers\Sheet;

use App\Models\Info\EntryCategory;
use App\Models\Sheet\Expense;

class ExpenseObserver
{
    /**
     * Handle the expense "saving" event.
     *
     * @param \App\Models\Sheet\Expense $expense
     *
     * @return void
     * @throws \Exception
     */
    public function saving(Expense $expense)
    {
        if( !$expense->entry_category_id || !EntryCategory::isHasContractor($expense->entry_category_id) ) {
            $expense->contractor_id = null;
        }
    }
}
