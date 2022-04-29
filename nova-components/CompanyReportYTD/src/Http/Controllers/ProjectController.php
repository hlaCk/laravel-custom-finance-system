<?php

namespace Sheets\CompanyReportYTD\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Abstracts\SelectOptionsResource;
use App\Models\Info\CreditCategory;
use App\Models\Info\EntryCategory;
use App\Models\Info\Project\Project;
use App\Models\Sheet\Credit;
use Illuminate\Http\Request;
use Sheets\CompanyReportYTD\Http\Resources\ProjectCreditsYtdByMonthResource;
use Sheets\CompanyReportYTD\Http\Resources\ProjectCreditsYtdByProjectResource;
use Sheets\CompanyReportYTD\Http\Resources\ProjectResource;

class ProjectController extends Controller
{
    public static function getProjectExpensesColumnLabel()
    {
        return __('models/sheet/expense.company_report_ytd_header_label');
    }

    public function index(Request $request)
    {
        return SelectOptionsResource::collection(Project::all(), 'name', 'id');
    }

    public function show(Request $request, Project $project)
    {
        return ProjectResource::make($project)->except([ 'id' ]);
    }

    public function entry_categories_index(Request $request)
    {
        return SelectOptionsResource::collection(EntryCategory::all());
    }

    public function project_expenses_credits_ytd_by_month_show(Request $request, Project $project)
    {
        $expenses = $project->expenses_ytd_by_month($request->entry_category_id, $request->from_date);
        $credits = $project->credits_ytd_by_month($request->credit_category_id, $request->from_date);
        $credits_counts = $credits->sum('count');
        $no_of_payments = $credits->map->count;
        $credits = $credits->map->amount;

        $exists = $expenses->intersectByKeys($credits)->keys();
        $balance = [];

        foreach( $exists as $month ) {
            $balance[ $month ] = (double) ($credits[ $month ] ?? 0) - (double) ($expenses[ $month ] ?? 0);
        }

        $expenses->except($exists)
                 ->each(function ($value, $month) use (&$balance) {
                     $balance[ $month ] = 0 - (double) $value;
                 });

        $credits->except($exists)
                ->each(function ($value, $month) use (&$balance) {
                    $balance[ $month ] = (double) $value;
                });

        $balance = toCollect($balance)
            ->filter(fn($value) => (double) $value);

        $credits = toCollect($credits)
            ->filter(fn($value) => (double) $value);

        $no_of_payments = toCollect($no_of_payments)
            ->filter(fn($value) => (double) $value);

        $dates =
            iterator_to_array(
                getDefaultFromDate()->monthsUntil(getDefaultToDate())->map(fn($v) => $v->translatedFormat('M/Y'))
            );

        $new_balance = [ self::getProjectCreditsColumnLabel() => __('models/sheet/credit.balance') ];
        $new_credits = [ self::getProjectCreditsColumnLabel() => __('models/sheet/credit.credits') ];
        $new_expenses = [ self::getProjectCreditsColumnLabel() => __('models/sheet/credit.expenses') ];
        $new_no_of_payments = [ self::getProjectCreditsColumnLabel() => __('models/sheet/credit.no_of_payments') ];
        $headers = [ [ 'label' => self::getProjectCreditsColumnLabel(), 'class' => 'font-bold' ] ];

        $getValueFromCollectionByLabel =
            fn($collection, $label) => $collection->has($label) ? $collection->get($label) : 0;

        toCollect(array_fill(0, count($dates), '0'))
            ->map(
                function ($v, $d) use (
                    $dates,
                    &$balance,
                    &$new_balance,
                    &$credits,
                    &$expenses,
                    &$new_credits,
                    &$new_expenses,
                    &$no_of_payments,
                    &$new_no_of_payments,
                    &$headers,

                    $getValueFromCollectionByLabel
                ) {
                    $label = $dates[ $d ];
                    $headers[] = [
                        'label' => $label,
                        'class' => 'text-center',
                    ];

                    $new_balance[ $label ] = ($_value = $getValueFromCollectionByLabel($balance, $label))
                        ?
                        formatValueAsCurrency($_value)
                        :
                        '-';

                    $new_credits[ $label ] = ($_value = $getValueFromCollectionByLabel($credits, $label))
                        ?
                        formatValueAsCurrency($_value)
                        :
                        '-';

                    $new_expenses[ $label ] = ($_value = $getValueFromCollectionByLabel($expenses, $label))
                        ?
                        formatValueAsCurrency($_value)
                        :
                        '-';

                    $new_no_of_payments[ $label ] =
                        ($_value = $getValueFromCollectionByLabel($no_of_payments, $label)) ?: '-';

                    return $v;
                }
            );
        $headers[] = [ 'label' => __('models/sheet/credit.grand_total'), 'class' => 'text-center font-bold' ];
        $sum_balance = $balance->sum();
        $sum_credits = $credits->sum();
        $sum_expenses = $expenses->sum();

        $new_balance[ __('models/sheet/credit.grand_total') ] =
            $sum_balance ? formatValueAsCurrency($sum_balance) : '-';
        $new_credits[ __('models/sheet/credit.grand_total') ] =
            $sum_credits ? formatValueAsCurrency($sum_credits) : '-';
        $new_expenses[ __('models/sheet/credit.grand_total') ] =
            $sum_expenses ? formatValueAsCurrency($sum_expenses) : '-';
        $new_no_of_payments[ __('models/sheet/credit.grand_total') ] = $credits_counts;

        return ProjectCreditsYtdByMonthResource::make([
                                                          'headers'        => $headers,
                                                          'balance'        => $new_balance,
                                                          'credits'        => $new_credits,
                                                          'expenses'       => $new_expenses,
                                                          'no_of_payments' => $new_no_of_payments,
                                                          'grand_total'    => [
                                                              'balance'  => $balance->sum('value'),
                                                              'credits'  => $credits->sum('value'),
                                                              'expenses' => $expenses->sum('value'),
                                                              'count'    => $credits_counts,
                                                          ],
                                                      ]);
    }

    public static function getProjectCreditsColumnLabel()
    {
        return __('models/sheet/credit.company_report_ytd_header_label');
    }

    public function project_credits_ytd_by_project_show(Request $request, $groupBy = 'M/Y')
    {
        $getValueFromCollectionByLabel =
            fn($collection, $label) => $collection->has($label) ? $collection->get($label) : 0;

        $getKeyPrefix = fn($index = "") => "col-{$index}";
        $getZeroValueLabel = fn() => '-';

        $projectNameColumnKey = 'id';
        $projectGrandTotalColumnKey = 'actions';

        $_grand_total = __('models/sheet/credit.grand_total');
        $_total_of = __('models/sheet/credit.total_of');

        $_new_credits = collect();
        $amountByProject = [];
        $amountByDate = [];

        $dates =
            iterator_to_array(
                getDefaultFromDate()->monthsUntil(getDefaultToDate())->map(fn($v) => $v->translatedFormat('M/Y'))
            );

        $header_grand_total =
//            [ 'label' => $_grand_total, 'class' => 'text-center font-bold', 'key' => $projectGrandTotalColumnKey ];
            self::parseTableHeadColumn([
                                           'value' => $_grand_total,
                                           'index' => $projectGrandTotalColumnKey,
                                           'class' => 'text-center font-bold',
                                       ]);
        $headers = [
            self::parseTableHeadColumn([
                                           'value' => self::getProjectCreditsByProjectColumnLabel(),
                                           'index' => $projectNameColumnKey,
                                           'class' => 'font-bold',
                                       ]),
        ];

        $credits = Credit::ByDate(
            $request->from_date,
            $request->credit_category_id,
            $request->project_id
        )
                         ->get()
                         ->groupBy(fn($model) => $model->project_name)
            ->map // map projects name -> dates
            ->groupBy(fn($model) => $model->formatDate(value($groupBy)))
            // map project name -> dates -> credits
            ->map(function ($c, $project) use (&$amountByProject) {
                /*
                 * $c = [
                 *  date => [
                 *      credit, ...
                 *  ]
                 * ]
                 *
                 * after:
                 * $c = [
                 *  date => amount
                 * ]
                 */
                $c = $c->map->sum('amount')->filter();

                $amount = (double) $c->sum();
                $amountByProject[ $project ] = ($amountByProject[ $project ] ?? 0) + $amount;

                return $c;
            });

        toCollect(array_fill(0, count($dates), '0'))
            ->map(function ($v, $d) use (
                $dates,
                $credits,
                &$_new_credits,
                &$headers,
                &$amountByDate,
                $getValueFromCollectionByLabel,
                $getKeyPrefix,
                $getZeroValueLabel
            ) {
                $label = $dates[ $d ];
                $headers[] = self::parseTableHeadColumn($label, $d);

                foreach( $credits as $_project => $_dates ) {
                    $_value =
                        when($_original_value = $getValueFromCollectionByLabel($_dates, $label),
                            fn($v) => formatValueAsCurrency($v),
                             $getZeroValueLabel
                        );

                    $_new_credits[ $_project ] = ($_new_credits[ $_project ] ?? collect());
                    $_new_credits[ $_project ][ $getKeyPrefix($d) ] = $_value;

                    $amountByDate[ $getKeyPrefix($d) ] ??= 0;
                    $amountByDate[ $getKeyPrefix($d) ] += (double) ($_original_value ?: 0);
                }

                return true;
            });

        $headers[] = $header_grand_total;

        $data = $_new_credits
            ->map(fn($dates, $p) => toCollect($dates)
                ->prepend($p, $projectNameColumnKey)
                ->put(
                    $projectGrandTotalColumnKey,
                    when((double) ($amountByProject[ $p ] ?? 0), fn($v) => formatValueAsCurrency($v), $getZeroValueLabel)
                )
            )
            ->values()
            ->add(
                toCollect($amountByDate)
                    ->put($projectGrandTotalColumnKey, array_sum($amountByDate))
                    ->map(fn($v, $k) => when($v, fn($_v) => formatValueAsCurrency($_v), $getZeroValueLabel))
                    ->prepend($_total_of, $projectNameColumnKey)
            );

        return ProjectCreditsYtdByProjectResource::make(
            compact(
                'headers',
                'data'
            )
        );
    }

    /**
     * @param string|\Closure|array|null      $value
     * @param string|int|double|\Closure|null $index
     * @param string|\Closure|null            $class
     * @param string|\Closure|null            $key_prefix
     *
     * @return array
     */
    public static function parseTableHeadColumn($value, $index = 0, $class = 'text-center', $key_prefix = 'col-'): array
    {
        /** @var string|\Closure|array $value */
        $key_prefix = (string) value($key_prefix);
        $class = (string) value($class);
        $index = (string) value($index);
        $value = value($value);

        if( func_num_args() === 1 && is_array($value) ) {
            /** @var array $value */
            $key_prefix = (string) value(data_get($value, 'key_prefix'));
            $class = (string) value(data_get($value, 'class'));
            $index = (string) value(data_get($value, 'index'));
            $value = (string) value(data_get($value, 'value'));

            return self::parseTableHeadColumn($value, $index, $class, $key_prefix);
        }
        /** @var string $value */
        $value = (string) value($value);

        return [
            'label' => $value,
            'class' => $class,
            'key'   => "{$key_prefix}{$index}",
        ];
    }

    public static function getProjectCreditsByProjectColumnLabel()
    {
        return __('models/sheet/credit.company_report_ytd_by_project_header_label');
    }

    public function project_credits_ytd_by_project_show1(Request $request, Project $project)
    {
        $data =
            $project->credits_ytd(
                $request->credit_category_id ?? '*',
                $request->from_date,
                fn($model) => optional($model)->project_name
            )
//                        ->mapWithKeys(fn($value, $label) => [ $label => compact('value', 'label') ])
//                        ->values();
        ;
        dd($data);
        return ProjectCreditsYtdByProjectResource::collection($data)
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
}
