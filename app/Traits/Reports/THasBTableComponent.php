<?php

namespace App\Traits\Reports;

trait THasBTableComponent
{
    public static function makeAttributeGetter($attribute_name = 'project_name'): \Closure
    {
        $attribute_name = (string) value($attribute_name);
        return fn(\Illuminate\Database\Eloquent\Model $model) => $model->$attribute_name;
    }

    public static function makeFormattedDateAttributeGetter($format = 'Y-m-d', $method_name = 'formatDate'): \Closure
    {
        $method_name = (string) value($method_name);
        $format = (string) value($format);
        return fn(\Illuminate\Database\Eloquent\Model $model) => $model->$method_name($format);
    }

    public static function getProjectNameColumnKey(): string
    {
        return 'id';
    }

    public static function getProjectGrandTotalColumnKey(): string
    {
        return 'actions';
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

        if( is_array($value) && func_num_args() === 1 ) {
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

    public static function getProjectExpensesColumnLabel()
    {
        return __('models/sheet/expense.company_report_ytd_header_label');
    }

    public static function getKeyPrefix($index = "", $prefix = "col-"): string
    {
        $index = (string) value($index);
        $prefix = (string) value($prefix);

        return "{$prefix}{$index}";
    }

    public static function getValueFromCollectionByLabel($collection, $label, $default = 0)
    {
        $collection = toCollect(value($collection));
        $label = (string) value($label);

        return $collection->has($label) ? $collection->get($label) : value($default);
    }

    public static function getZeroValueLabel(bool $wrap_function = false)
    {
        $label = '-';
        return $wrap_function ? (static fn() => $label) : $label;
    }

    public static function getProjectCreditsColumnLabel()
    {
        return __('models/sheet/credit.company_report_ytd_header_label');
    }

    public static function getProjectCreditsByProjectColumnLabel()
    {
        return __('models/sheet/credit.company_report_ytd_by_project_header_label');
    }

    public static function getProjectExpensesByProjectColumnLabel()
    {
        return __('models/sheet/expense.company_report_ytd_by_project_header_label');
    }

    public static function getProjectExpensesByCategoryColumn()
    {
        return __('models/sheet/expense.projects_report_ytd_header_label');
    }
}
