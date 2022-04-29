<?php

namespace Sheets\ReportComponents\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Abstracts\SelectOptionsResource;
use App\Models\Info\CreditCategory;
use App\Models\Info\EntryCategory;
use App\Models\Info\Project\Project;
use Illuminate\Http\Request;
use Sheets\YearToDate\Http\Resources\ProjectCreditResource;
use Sheets\YearToDate\Http\Resources\ProjectCreditsYtdByCateoryResource;
use Sheets\YearToDate\Http\Resources\ProjectCreditsYtdByMonthResource;
use Sheets\YearToDate\Http\Resources\ProjectExpenseResource;
use Sheets\YearToDate\Http\Resources\ProjectExpensesYtdByCateoryResource;
use Sheets\YearToDate\Http\Resources\ProjectExpensesYtdByMonthResource;
use Sheets\YearToDate\Http\Resources\ProjectResource;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        return SelectOptionsResource::collection(Project::all(), 'name', 'id');
    }
}
