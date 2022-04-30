<?php

namespace Sheets\CompanyReportYTD\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Info\{EntryCategory};
use App\Models\Sheet\{Credit, Expense};
use App\Traits\Reports\THasBTableComponent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Sheets\CompanyReportYTD\Http\Resources\ProjectCreditsYtdByProjectResource;
use Sheets\CompanyReportYTD\Http\Resources\ProjectExpensesYtdByCateoryResource;
use Sheets\CompanyReportYTD\Http\Resources\ProjectExpensesYtdByProjectResource;

class ProjectController extends Controller
{
    use THasBTableComponent;

    /**
     * Accepts from $request:
     * * date_format
     * * from_date
     * * to_date
     * * credit_category_id
     * * entry_category_id
     * * project_ids
     *
     * @GET /projects/expenses_credits_ytd_by_month
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function project_expenses_credits_ytd_by_month_show(Request $request): JsonResource
    {
        $groupBy = $request->get('date_format', 'M/Y');
        $fromDate = ($fromDate = $request->get('from_date')) ? carbon()->parse($fromDate) : null;
        $toDate = ($toDate = $request->get('to_date')) ? carbon()->parse($toDate) : null;
        $dates = getDatePeriodUntilAsArray($fromDate, $toDate, $groupBy, 'months');
        $projectIds = $request->get('project_ids');

        $creditCategoryId = $request->get('credit_category_id');
        $entryCategoryId = $request->get('entry_category_id');

        $projectNameColumnKey = static::getProjectNameColumnKey();
        $projectGrandTotalColumnKey = static::getProjectGrandTotalColumnKey();

        $_grand_total = __('models/sheet/credit.grand_total');

        $header_grand_total =
            self::parseTableHeadColumn([
                                           'value' => $_grand_total,
                                           'index' => $projectGrandTotalColumnKey,
                                           'class' => 'text-center font-bold',
                                       ]);
        $headers = [
            self::parseTableHeadColumn([
                                           'value' => self::getProjectExpensesColumnLabel(),
                                           'index' => $projectNameColumnKey,
                                           'class' => 'font-bold',
                                       ]),
        ];

        $credits = Credit::ByDate(
            $fromDate,
            $creditCategoryId,
            $projectIds
        )
                         ->get()
                         ->groupBy(static::makeFormattedDateAttributeGetter($groupBy))
                         ->map(fn($c) => [
                             // map dates -> credits
                             'amount' => $c->sum('amount'),
                             'count'  => $c->count(),
                         ]);
        $credits = toCollect($credits);

        $expenses = Expense::ByDate(
            $fromDate,
            $entryCategoryId,
            $projectIds
        )
                           ->get()
                           ->groupBy(static::makeFormattedDateAttributeGetter($groupBy))
                           ->map(fn($c) => [
                               // dates -> expenses
                               'amount' => $c->sum('amount'),
                           ]);

        $credits_counts = $credits->sum('count');
        $no_of_payments = toCollect($credits->map->count);
        $credits = $credits->map->amount;
        $expenses = $expenses->map->amount;

        $exists = $expenses->intersectByKeys($credits)->keys();
        $balance = toCollect([]);

        foreach( $exists as $month ) {
            $balance[ $month ] = (double) ($credits[ $month ] ?? 0) - (double) ($expenses[ $month ] ?? 0);
        }

        collectExcept($expenses, $exists)
                 ->each(function ($value, $month) use (&$balance) {
                     $balance[ $month ] = 0 - (double) $value;
                 });

        /** @var \Illuminate\Support\Collection $exists */
        collectExcept($credits, $exists)
                ->each(function ($value, $month) use (&$balance) {
                    $balance[ $month ] = (double) $value;
                });
        $credits = $credits->filter(fn($value) => (double) $value);

        $balance = $balance->filter(fn($value) => (double) $value);

        $no_of_payments = $no_of_payments->filter(fn($value) => (double) $value);

        $new_balance = [ $projectNameColumnKey => __('models/sheet/credit.balance') ];
        $new_credits = [ $projectNameColumnKey => __('models/sheet/credit.credits') ];
        $new_expenses = [ $projectNameColumnKey => __('models/sheet/credit.expenses') ];
        $new_no_of_payments = [ $projectNameColumnKey => __('models/sheet/credit.no_of_payments') ];

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
                    &$headers
                ) {
                    $label = $dates[ $d ];
                    $headers[] = self::parseTableHeadColumn($label, $d);
                    $_prefixed_key = static::getKeyPrefix($d);

                    $new_balance[ $_prefixed_key ] = when(
                        static::getValueFromCollectionByLabel($balance, $label),
                        makeFormatValueAsCurrency(),
                        static::getZeroValueLabel(true)
                    );

                    $new_credits[ $_prefixed_key ] = when(
                        static::getValueFromCollectionByLabel($credits, $label),
                        makeFormatValueAsCurrency(),
                        static::getZeroValueLabel(true)
                    );

                    $new_expenses[ $_prefixed_key ] = when(
                        static::getValueFromCollectionByLabel($expenses, $label),
                        makeFormatValueAsCurrency(),
                        static::getZeroValueLabel(true)
                    );

                    $new_no_of_payments[ $_prefixed_key ] =
                        static::getValueFromCollectionByLabel($no_of_payments, $label)
                            ?: static::getZeroValueLabel();

                    return true;
                }
            );

        $headers[] = $header_grand_total;
        $sum_balance = $balance->sum();
        $sum_credits = $credits->sum();
        $sum_expenses = $expenses->sum();

        $new_balance = toCollect($new_balance)
            ->put(
                $projectGrandTotalColumnKey,
                $sum_balance ? formatValueAsCurrency($sum_balance) : value(static::getZeroValueLabel())
            );

        $new_credits = toCollect($new_credits)
            ->put(
                $projectGrandTotalColumnKey,
                $sum_credits ? formatValueAsCurrency($sum_credits) : value(static::getZeroValueLabel())
            );

        $new_expenses = toCollect($new_expenses)
            ->put(
                $projectGrandTotalColumnKey,
                $sum_expenses ? formatValueAsCurrency($sum_expenses) : value(static::getZeroValueLabel())
            );

        $new_no_of_payments = toCollect($new_no_of_payments)
            ->put($projectGrandTotalColumnKey, $credits_counts ?: value(static::getZeroValueLabel()));

        $data = [
            $new_balance,
            $new_credits,
            $new_expenses,
            $new_no_of_payments,
        ];

        return ProjectCreditsYtdByProjectResource::make(compact('headers', 'data'));
    }

    /**
     * Accepts from $request:
     * * date_format
     * * from_date
     * * to_date
     * * credit_category_id
     * * project_ids
     *
     * @GET /projects/credits_ytd_by_project
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function project_credits_ytd_by_project_show(Request $request): JsonResource
    {
        $groupBy = $request->get('date_format', 'M/Y');
        $fromDate = ($fromDate = $request->get('from_date')) ? carbon()->parse($fromDate) : null;
        $toDate = ($toDate = $request->get('to_date')) ? carbon()->parse($toDate) : null;
        $dates = getDatePeriodUntilAsArray($fromDate, $toDate, $groupBy, 'months');
        $projectIds = $request->get('project_ids');
        $creditCategoryId = $request->get('credit_category_id');

        $projectNameColumnKey = static::getProjectNameColumnKey();
        $projectGrandTotalColumnKey = static::getProjectGrandTotalColumnKey();

        $_grand_total = __('models/sheet/credit.total');
        $_total_of = __('models/sheet/credit.total_of');

        $_new_credits = collect();
        $amountByProject = [];
        $amountByDate = [];

        $header_grand_total =
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
            $fromDate,
            $creditCategoryId,
            $projectIds
        )
                         ->get()
                         ->groupBy(static::makeAttributeGetter())
            ->map // map projects name -> dates
            ->groupBy(static::makeFormattedDateAttributeGetter($groupBy))
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
                &$amountByDate
            ) {
                $label = $dates[ $d ];
                $headers[] = self::parseTableHeadColumn($label, $d);

                foreach( $credits as $_project => $_dates ) {
                    $_value =
                        when(
                            $_original_value = static::getValueFromCollectionByLabel($_dates, $label),
                            makeFormatValueAsCurrency(),
                            static::getZeroValueLabel(true)
                        );

                    $_new_credits[ $_project ] = ($_new_credits[ $_project ] ?? collect());
                    $_new_credits[ $_project ][ static::getKeyPrefix($d) ] = $_value;

                    $amountByDate[ static::getKeyPrefix($d) ] ??= 0;
                    $amountByDate[ static::getKeyPrefix($d) ] += (double) ($_original_value ?: 0);
                }

                return true;
            });

        $headers[] = $header_grand_total;

        $data = $_new_credits
            ->map(fn($dates, $p) => toCollect($dates)
                ->prepend($p, $projectNameColumnKey)
                ->put(
                    $projectGrandTotalColumnKey,
                    when(
                        (double) ($amountByProject[ $p ] ?? 0),
                        makeFormatValueAsCurrency(),
                        static::getZeroValueLabel(true)
                    )
                )
            )
            ->values()
            ->add(
                toCollect($amountByDate)
                    ->put($projectGrandTotalColumnKey, array_sum($amountByDate))
                    ->map(fn($v, $k) => when($v, fn($_v) => formatValueAsCurrency($_v), static::getZeroValueLabel(true))
                    )
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
     * Accepts from $request:
     * * date_format
     * * from_date
     * * to_date
     * * entry_category_id
     * * project_ids
     *
     * @GET /projects/expenses_ytd_by_project
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function project_expenses_ytd_by_project_show(Request $request): JsonResource
    {
        $groupBy = $request->get('date_format', 'M/Y');
        $fromDate = ($fromDate = $request->get('from_date')) ? carbon()->parse($fromDate) : null;
        $toDate = ($toDate = $request->get('to_date')) ? carbon()->parse($toDate) : null;
        $dates = getDatePeriodUntilAsArray($fromDate, $toDate, $groupBy, 'months');
        $projectIds = $request->get('project_ids');
        $entryCategoryId = $request->get('entry_category_id');

        $projectNameColumnKey = static::getProjectNameColumnKey();
        $projectGrandTotalColumnKey = static::getProjectGrandTotalColumnKey();

        $_grand_total = __('models/sheet/credit.total');
        $_total_of = __('models/sheet/credit.total_of');

        $_new_expenses = collect();
        $amountByProject = [];
        $amountByDate = [];

        $header_grand_total =
            self::parseTableHeadColumn([
                                           'value' => $_grand_total,
                                           'index' => $projectGrandTotalColumnKey,
                                           'class' => 'text-center font-bold',
                                       ]);
        $headers = [
            self::parseTableHeadColumn([
                                           'value' => self::getProjectExpensesByProjectColumnLabel(),
                                           'index' => $projectNameColumnKey,
                                           'class' => 'font-bold',
                                       ]),
        ];

        $expenses = Expense::ByDate(
            $fromDate,
            $entryCategoryId,
            $projectIds
        )
                           ->get()
                           ->groupBy(static::makeAttributeGetter())
            ->map // map projects name -> dates
            ->groupBy(static::makeFormattedDateAttributeGetter($groupBy))
            // map project name -> dates -> expenses
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
                $expenses,
                &$_new_expenses,
                &$headers,
                &$amountByDate
            ) {
                $label = $dates[ $d ];
                $headers[] = self::parseTableHeadColumn($label, $d);

                foreach( $expenses as $_project => $_dates ) {
                    $_value =
                        when(
                            $_original_value = static::getValueFromCollectionByLabel($_dates, $label),
                            makeFormatValueAsCurrency(),
                            static::getZeroValueLabel(true)
                        );
                    $_prefixed_key = static::getKeyPrefix($d);
                    $_new_expenses[ $_project ] = ($_new_expenses[ $_project ] ?? collect());
                    $_new_expenses[ $_project ][ $_prefixed_key ] = $_value;

                    $amountByDate[ $_prefixed_key ] ??= 0;
                    $amountByDate[ $_prefixed_key ] += (double) ($_original_value ?: 0);
                }

                return true;
            });

        $headers[] = $header_grand_total;

        $data = $_new_expenses
            ->map(fn($dates, $p) => toCollect($dates)
                ->prepend($p, $projectNameColumnKey)
                ->put(
                    $projectGrandTotalColumnKey,
                    when(
                        (double) ($amountByProject[ $p ] ?? 0),
                        makeFormatValueAsCurrency(),
                        static::getZeroValueLabel(true)
                    )
                )
            )
            ->values()
            ->add(
                toCollect($amountByDate)
                    ->put($projectGrandTotalColumnKey, array_sum($amountByDate))
                    ->map(fn($v, $k) => when($v, fn($_v) => formatValueAsCurrency($_v), static::getZeroValueLabel(true))
                    )
                    ->prepend($_total_of, $projectNameColumnKey)
            );

        return ProjectExpensesYtdByProjectResource::make(
            compact(
                'headers',
                'data'
            )
        );
    }

    /**
     * Accepts from $request:
     * * date_format
     * * from_date
     * * to_date
     * * entry_category_id
     * * project_ids
     *
     * @GET /projects/expenses_ytd_by_category_month
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Resources\Json\JsonResource
     */
    public function project_expenses_ytd_by_category_month_show(Request $request): JsonResource
    {
        $groupBy = $request->get('date_format', 'M/Y');
        $fromDate = ($fromDate = $request->get('from_date')) ? carbon()->parse($fromDate) : null;
        $toDate = ($toDate = $request->get('to_date')) ? carbon()->parse($toDate) : null;
        $dates = getDatePeriodUntilAsArray($fromDate, $toDate, $groupBy, 'months');
        $entryCategoryId = $request->get('entry_category_id');
        $projectIds = $request->get('project_ids');

        $summary_column_key = 'id';
        $grand_total_column_key = 'actions';

        $amountByProject = [];
        $amountByDate = [];
        $data = Expense::byDate(
            $fromDate,
            $entryCategoryId,
            $projectIds
        )
                       ->get()
                       ->groupBy(static::makeAttributeGetter('entry_category_name'))
            ->map // map name -> dates
            ->groupBy(static::makeFormattedDateAttributeGetter($groupBy))
            // map name -> dates -> Expenses
            ->map(function ($c, $project) use (&$amountByProject) {
                $c = $c->map->sum('amount')->filter();
                $amount = (double) $c->sum();
                $amountByProject[ $project ] = ($amountByProject[ $project ] ?? 0) + $amount;

                return $c;
            });

        $headers = [
            self::parseTableHeadColumn([
                                           'value' => self::getProjectExpensesByCategoryColumn(),
                                           'index' => $summary_column_key,
                                           'class' => 'font-bold',
                                       ]),
        ];

        $amountByDate[ $summary_column_key ] = __('models/sheet/credit.total_of');
        $entry_categories = EntryCategory::get('name')->map->name;

        toCollect(array_fill(0, count($dates), '0'))
            ->map(
                function ($v, $d) use (
                    $dates,
                    $entry_categories,
                    $grand_total_column_key,
                    $summary_column_key,
                    &$data,
                    &$new_data,
                    &$amountByDate,
                    &$amountByProject,
                    &$headers
                ) {
                    $label = $dates[ $d ];
                    $headers[] = self::parseTableHeadColumn($label, $d);
                    $_prefixed_key = static::getKeyPrefix($d);

                    foreach( $data as $category => $_dates ) {
                        $_value =
                            when(
                                $_original_value = static::getValueFromCollectionByLabel($_dates, $label),
                                makeFormatValueAsCurrency(),
                                static::getZeroValueLabel(true)
                            );
                        $new_data[ $category ] ??= collect();
                        $new_data[ $category ][ $_prefixed_key ] = $_value;
                        $new_data[ $category ][ $summary_column_key ] ??= $category;
                        $new_data[ $category ][ $grand_total_column_key ] ??= when(
                            $amountByProject[ $category ] ?? 0,
                            makeFormatValueAsCurrency(),
                            static::getZeroValueLabel(true)
                        );

                        $amountByDate[ $_prefixed_key ] ??= 0;
                        $amountByDate[ $_prefixed_key ] += (double) ($_original_value ?: 0);
                    }

                    foreach( $entry_categories as $category ) {
                        $new_data[ $category ] ??= collect();
                        $new_data[ $category ][ $_prefixed_key ] ??= static::getZeroValueLabel();
                        $new_data[ $category ][ $summary_column_key ] ??= $category;
                        $new_data[ $category ][ $grand_total_column_key ] ??= when(
                            $amountByProject[ $category ] ?? 0,
                            makeFormatValueAsCurrency(),
                            static::getZeroValueLabel(true)
                        );
                    }

                    return true;
                }
            );

        $headers[] = self::parseTableHeadColumn([
                                                    'value' => __('models/sheet/credit.grand_total'),
                                                    'index' => $grand_total_column_key,
                                                    'class' => 'text-center font-bold',
                                                ]);
        $amountByDate[ $grand_total_column_key ] = array_sum($amountByDate);
        $amountByDate = toCollect($amountByDate)
            ->map(fn($v, $i) => $i === $summary_column_key
                ? $v
                : when(
                    $v,
                    makeFormatValueAsCurrency(),
                    static::getZeroValueLabel(true)
                ));

        $new_data = toCollect(array_values($new_data))
            ->add($amountByDate);

        return ProjectExpensesYtdByCateoryResource::make([
                                                             'headers' => $headers,
                                                             'payload' => $new_data,
                                                         ]);
        dd(
            $data,
            $new_data,
            $amountByDate,
            $amountByProject,
            $headers
        );

        $new_data = [];
        $new_data_for_sum = [];
        foreach( $data as $category => $_dates ) {
            $new_data[ $category ] = [ $summary_column_key => $category ];
            $new_data_for_sum[ $category ] = [ $summary_column_key => $category ];

            foreach( $dates as $date ) {
                $value = $_dates[ $date ] ?? 0;
                $new_data[ $category ][ $date ] = $value ? formatValueAsCurrency($value) : static::getZeroValueLabel();
                $new_data_for_sum[ $category ][ $date ] = $value;
            }

            $dates_sum = $_dates->sum();
            $new_data[ $category ][ $grand_total_column_key ] =
                $dates_sum ? formatValueAsCurrency($dates_sum) : static::getZeroValueLabel();
            $new_data_for_sum[ $category ][ $grand_total_column_key ] = $_dates->sum();
        }
        $new_data = toCollect(array_values($new_data));
        $new_data_for_sum = toCollect(array_values($new_data_for_sum));

        $new_data_totals = [ self::getProjectExpensesByCategoryColumn() => null ];
        foreach( $dates as $date ) {
            $_sum_dates = $new_data_for_sum->sum($date);
            $new_data_totals[ $date ] = $_sum_dates ? formatValueAsCurrency($_sum_dates) : static::getZeroValueLabel();
        }
        $grand_total = $new_data_for_sum->sum($grand_total_column_key);
        $new_data_totals[ $grand_total_column_key ] =
            $grand_total ? formatValueAsCurrency($grand_total) : static::getZeroValueLabel();
        $new_data[] = $new_data_totals;

        return ProjectExpensesYtdByCateoryResource::make([
                                                             'headers' => $headers,
                                                             'payload' => $new_data,
                                                             'grand_total' => $data->values()->map->sum()->sum(),
                                                         ]);
    }
}
