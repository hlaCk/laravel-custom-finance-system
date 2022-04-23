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

    public function project_credits_ytd_by_month_show(Request $request, Project $project)
    {
        $expenses = $project->expenses_ytd_by_month($request->entry_category_id, $request->from_date);
        $credits = $project->credits_ytd_by_month($request->credit_category_id, $request->from_date);
        $credits_counts = $credits->sum('count');
        $no_of_payments = $credits->map->count;
        $credits = $credits->map->amount;

        $exists = $expenses->intersectByKeys($credits)->keys();

        $balance = [];
        foreach( $exists as $month ) {
            $balance[ $month ] = (double) $credits[ $month ] - (double) $expenses[ $month ];
        }

        $expenses->except($exists)
                 ->each(fn($value, $month) => ($balance[ $month ] = 0 - (double) $value));

        $credits->except($exists)
                ->each(fn($value, $month) => ($balance[ $month ] = (double) $value));

        $balance = toCollect($balance)
            ->filter(fn($value) => (double) $value);

        $credits = toCollect($credits)
            ->filter(fn($value) => (double) $value);

        $no_of_payments = toCollect($no_of_payments)
            ->filter(fn($value) => (double) $value);

        $dates =
            iterator_to_array(getDefaultFromDate()->monthsUntil(getDefaultToDate())->map(fn($v) => $v->format('M/Y')));

        $new_balance = [ '#' => __('models/sheet/credit.balance') ];
        $new_credits = [ '#' => __('models/sheet/credit.credits') ];
        $new_no_of_payments = [ '#' => __('models/sheet/credit.no_of_payments') ];
        $headers = [ [ 'label' => '#', 'class' => 'font-bold' ] ];
        toCollect(array_fill(0, count($dates), '0'))
            ->map(
                function ($v, $d) use (
                    $dates,
                    &$balance,
                    &$new_balance,
                    &$credits,
                    &$new_credits,
                    &$no_of_payments,
                    &
                    $new_no_of_payments,
                    &$headers
                ) {
                    $label = $dates[ $d ];
                    $headers[] = [
                        'label' => $label,
                        'class' => 'text-center',
                    ];
                    $has_label = $balance->has($label);
                    $new_balance_value = $has_label ? $balance->get($label) : 0;
                    $new_balance[ $label ] = $new_balance_value ? formatValueAsCurrency($new_balance_value) : '-';

                    $has_label = $credits->has($label);
                    $new_credits_value = $has_label ? $credits->get($label) : 0;
                    $new_credits[ $label ] = $new_credits_value ? formatValueAsCurrency($new_credits_value) : '-';

                    $has_label = $no_of_payments->has($label);
                    $no_of_payments_count = $has_label ? $no_of_payments->get($label) : 0;
                    $new_no_of_payments[ $label ] = $no_of_payments_count ?: '-';

                    return $v;
                }
            );
        $headers[] = [ 'label' => __('models/sheet/credit.grand_total'), 'class' => 'text-center font-bold' ];
        $sum_balance = $balance->sum();
        $sum_credits = $credits->sum();
        $new_balance[ __('models/sheet/credit.grand_total') ] = $sum_balance ? formatValueAsCurrency($sum_balance) : '-';
        $new_credits[ __('models/sheet/credit.grand_total') ] = $sum_credits ? formatValueAsCurrency($sum_credits) : '-';
        $new_no_of_payments[ __('models/sheet/credit.grand_total') ] = $credits_counts;

        return ProjectCreditsYtdByMonthResource::make([
                                                          'headers'        => $headers,
                                                          'balance'        => $new_balance,
                                                          'credits'        => $new_credits,
                                                          'no_of_payments' => $new_no_of_payments,
                                                          'grand_total'    => [
                                                              'balance' => $balance->sum('value'),
                                                              'credits' => $credits->sum('value'),
                                                              'count'   => $credits_counts,
                                                          ],
                                                      ]);
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

    public function project_expenses_ytd_by_category_month_show(Request $request, Project $project)
    {
        $data = $project->expenses_ytd_by_month($request->entry_category_id ?? '*', $request->from_date);
        $dates =
            iterator_to_array(getDefaultFromDate()->monthsUntil(getDefaultToDate())->map(fn($v) => $v->format('M/Y')));
        $headers = [ [ 'label' => '#', 'class' => 'font-bold' ] ];
        $headers = array_merge($headers, $dates);
        $headers[] = [ 'label' => __('models/sheet/credit.grand_total'), 'class' => 'text-center font-bold' ];

        $entry_categories = EntryCategory::get('name')->map->name;
        foreach( $entry_categories as $category ) {
            if( !isset($data[ $category ]) ) {
                $data[ $category ] = collect();
            }
        }

        $new_data = [];
        $new_data_for_sum = [];
        foreach( $data as $category => $_dates ) {
            $new_data[ $category ] = [ '#' => $category ];
            $new_data_for_sum[ $category ] = [ '#' => $category ];

            foreach( $dates as $date ) {
                $value = $_dates[ $date ] ?? 0;
                $new_data[ $category ][ $date ] = $value ? formatValueAsCurrency($value) : '-';
                $new_data_for_sum[ $category ][ $date ] = $value;
            }

            $dates_sum = $_dates->sum();
            $new_data[ $category ][ __('models/sheet/credit.grand_total') ] = $dates_sum ? formatValueAsCurrency($dates_sum) : '-';
            $new_data_for_sum[ $category ][ __('models/sheet/credit.grand_total') ] = $_dates->sum();
        }
        $new_data = toCollect(array_values($new_data));
        $new_data_for_sum = toCollect(array_values($new_data_for_sum));

        $new_data_totals = [ '#' => null ];
        foreach( $dates as $date ) {
            $_sum_dates = $new_data_for_sum->sum($date);
            $new_data_totals[ $date ] = $_sum_dates ? formatValueAsCurrency($_sum_dates) : '-';
        }
        $grand_total = $new_data_for_sum->sum(__('models/sheet/credit.grand_total'));
        $new_data_totals[ __('models/sheet/credit.grand_total') ] = $grand_total ? formatValueAsCurrency($grand_total) : '-';
        $new_data[] = $new_data_totals;

        return ProjectExpensesYtdByCateoryResource::collection([
                                                                   'data' => [
                                                                       'headers' => $headers,
                                                                       'data'    => $new_data,
                                                                   ],
                                                               ])
                                                  ->additional([
                                                                   'grand_total' => $data->values()->map->sum()->sum(),
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
            $data_key  => $data,
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

}
