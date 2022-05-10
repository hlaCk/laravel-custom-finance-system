<?php

namespace Database\Factories\Sheet;

use App\Models\Info\Contractor\Contractor;
use App\Models\Info\EntryCategory;
use App\Models\Info\ExpenseCategory;
use App\Models\Info\Project\Project;
use App\Models\Sheet\Expense;

class ExpenseFactory extends \Database\Factories\AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string|\App\Models\Sheet\Expense
     */
    protected $model = Expense::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'date'              => $this->faker->dateTimeBetween(getDefaultFromDate(), getDefaultToDate()),
            'amount'            => $this->faker->numberBetween(10000, 100000),
            'vat_included'      => $this->faker->boolean(),
            'remarks'           => $this->faker->sentence((int) $this->faker->randomNumber(1)),
            'project_id'        => Project::onlyActive()->inRandomOrder()->first() ?: Project::factory(),
            'entry_category_id' => EntryCategory::onlyActive()->inRandomOrder()->first() ?: EntryCategory::factory(),
            'contractor_id'     => fn($attributes) => static::isEntryCategoryHasContractor($attributes) ?
                (Contractor::onlyActive()->inRandomOrder()->first() ?: Contractor::factory()) : null,
        ];
    }

    public static function isEntryCategoryHasContractor($attributes)
    {
        if( $entry_category_id = $attributes[ 'entry_category_id' ] ?? null ) {
            return EntryCategory::isHasContractor($entry_category_id);
        }

        return false;
    }

// region: Project

    public function makeProject(...$parameters)
    {
        return $this->state(fn(array $attributes) => [ 'project_id' => Project::factory(...$parameters) ]);
    }

    public function anyProject()
    {
        return $this->state(fn(array $attributes) => [ 'project_id' => Project::onlyActive()->inRandomOrder()->first() ]
        );
    }

    /**
     * @param int|\App\Models\Abstracts\Model $project
     *
     * @return \Database\Factories\Sheet\ExpenseFactory|static
     */
    public function forProject($project)
    {
        return $this->state(fn(array $attributes) => [ 'project_id' => $project ]);
    }
// endregion: Project

// region: EntryCategory
    public function makeEntryCategory(...$parameters)
    {
        return $this->state(fn(array $attributes) => [ 'entry_category_id' => EntryCategory::factory(...$parameters) ]);
    }

    public function anyEntryCategory()
    {
        return $this->state(
            fn(array $attributes) => [ 'entry_category_id' => EntryCategory::onlyActive()->inRandomOrder()->first() ]
        );
    }

    /**
     * @param int|\App\Models\Abstracts\Model $project
     *
     * @return \Database\Factories\Sheet\ExpenseFactory|static
     */
    public function forEntryCategory($model)
    {
        return $this->state(fn(array $attributes) => [ 'entry_category_id' => $model ]);
    }

    public function noEntryCategory(...$parameters)
    {
        return $this->state(fn(array $attributes) => [ 'entry_category_id' => null ]);
    }
// endregion: EntryCategory

// region: Contractor
    public function makeContractor(...$parameters)
    {
        return $this->state(
            fn(array $attributes) => [
                'contractor_id' => static::isEntryCategoryHasContractor($attributes)
                    ? Contractor::factory(...$parameters) : null,
            ]
        );
    }

    public function anyContractor()
    {
        return $this->state(
            fn(array $attributes) => [
                'contractor_id' => static::isEntryCategoryHasContractor($attributes)
                    ? Contractor::onlyActive()->inRandomOrder()->first() : null,
            ]
        );
    }

    /**
     * @param int|\App\Models\Abstracts\Model $project
     *
     * @return \Database\Factories\Sheet\ExpenseFactory|static
     */
    public function forContractor($model)
    {
        return $this->state(
            fn(array $attributes) => [
                'contractor_id' => static::isEntryCategoryHasContractor($attributes) ? $model
                    : null,
            ]
        );
    }

    public function noContractor(...$parameters)
    {
        return $this->state(fn(array $attributes) => [ 'contractor_id' => null ]);
    }
// endregion: Contractor

}
