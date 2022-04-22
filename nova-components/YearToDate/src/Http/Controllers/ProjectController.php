<?php

namespace Sheets\YearToDate\Http\Controllers;

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

    public function show(Request $request, Project $project)
    {
        return ProjectResource::make($project)->except([ 'id' ]);
    }

    public function expenses_index(Request $request, Project $project)
    {
        return ProjectExpenseResource::collection($project->expenses);
    }

    public function entry_categories_index(Request $request)
    {
        return SelectOptionsResource::collection(EntryCategory::all());
    }

    public function project_expenses_ytd_by_month_show(Request $request, Project $project)
    {
        $total = 0;
        $data = $project->expenses_ytd_by_month($request->entry_category_id, $request->from_date)
                        ->map(function ($value, $label) use (&$total) {
                            $total += doubleval($value);
                            return compact('value', 'label');
                        })
                        ->values();

        return ProjectExpensesYtdByMonthResource::make($data);
    }

    public function project_expenses_ytd_by_category_show(Request $request, Project $project)
    {
        $data = $project->expenses_ytd_by_month($request->entry_category_id ?? '*', $request->from_date, null)
                        ->mapWithKeys(fn($value, $label) => [ $label => compact('value', 'label') ])
                        ->values();

        return ProjectExpensesYtdByCateoryResource::collection($data)
                                                  ->additional([
                                                                   'grand_total' => $data->sum('value'),
                                                               ]);
    }

    public function project_expenses_ytd_by_category_month_show(Request $request, Project $project)
    {
        $data = $project->expenses_ytd_by_month($request->entry_category_id ?? '*', $request->from_date)
                        ->mapWithKeys(function ($data, $label) {
                            $data = array_merge(
                                $this->mapValueMonth($data),
                                [ 'label' => $label ]
                            );
                            return [
                                $label => $data,
                            ];
                        })
                        ->values();

        return ProjectExpensesYtdByCateoryResource::collection($data)
                                                  ->additional([
                                                                   'grand_total' => $data->sum('value'),
                                                               ]);
    }

    public function mapValueMonth($data, $data_key = 'data', $total_key = 'value')
    {
        $total = 0;
        $data = toCollect($data)
            ->map(function ($value, $month) use (&$total) {
                $total += doubleval($value);
                return compact('value', 'month');
            })
            ->values();

        return [
            $data_key => $data,
            $total_key => $total,
        ];
    }

    public function credit_categories_index(Request $request)
    {
        return SelectOptionsResource::collection(CreditCategory::all());
    }

    public function credits_index(Request $request, Project $project)
    {
        return ProjectCreditResource::collection($project->credits);
    }

    public function project_credits_ytd_by_month_show(Request $request, Project $project)
    {
        $total = 0;
        $data = $project->credits_ytd_by_month($request->credit_category_id, $request->from_date)
                        ->map(function ($value, $label) use (&$total) {
                            $total += doubleval($value);
                            return compact('value', 'label');
                        })
                        ->values();

        return ProjectCreditsYtdByMonthResource::make($data);
    }

    public function project_credits_ytd_by_category_show(Request $request, Project $project)
    {
        $data = $project->credits_ytd_by_month($request->credit_category_id ?? '*', $request->from_date, null)
                        ->mapWithKeys(fn($value, $label) => [ $label => compact('value', 'label') ])
                        ->values();

        return ProjectCreditsYtdByCateoryResource::collection($data)
                                                  ->additional([
                                                                   'grand_total' => $data->sum('value'),
                                                               ]);
    }

    public function project_credits_ytd_by_category_month_show(Request $request, Project $project)
    {
        $data = $project->credits_ytd_by_month($request->credit_category_id ?? '*', $request->from_date)
                        ->mapWithKeys(function ($data, $label) {
                            $data = array_merge(
                                $this->mapValueMonth($data),
                                [ 'label' => $label ]
                            );
                            return [
                                $label => $data,
                            ];
                        })
                        ->values();

        return ProjectCreditsYtdByCateoryResource::collection($data)
                                                  ->additional([
                                                                   'grand_total' => $data->sum('value'),
                                                               ]);
    }

}
