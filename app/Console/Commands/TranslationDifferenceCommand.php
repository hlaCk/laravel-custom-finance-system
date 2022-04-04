<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 *
 */
class TranslationDifferenceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translation:different';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show Translations difference';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->addArgument('group', InputArgument::IS_ARRAY, 'Load only the given groups', null)
             ->addOption('locale', 'l', InputOption::VALUE_OPTIONAL ^ InputOption::VALUE_IS_ARRAY, 'Load only the given locales', [])
             ->addOption('prepend', 'p', InputOption::VALUE_NONE, 'Prepend group name')
             ->addOption('tab', 't', InputOption::VALUE_NONE, 'Prepend key and value with tab')
             ->addOption('compact', 'c', InputOption::VALUE_NONE, 'Key and Value in different line')
             ->addOption('dump', 'D', InputOption::VALUE_NONE, 'Dump data')
             ->addOption('table', 'T', InputOption::VALUE_NONE, 'View data in table')
             ->addOption('quote', 'Q', InputOption::VALUE_NONE, 'Do not quote texts');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $config = config('nova-translation-editor');

        $groups = $config[ 'groups' ] ?: [];
        $select_group = (array) $this->argument('group') ?: [];
        $select_group = array_filter($select_group, fn($e) => $e && trim($e) !== '*');

        $select_locale = toCollect((array) $this->option('locale') ?: []);
        if( !$select_locale->isEmpty() ) {
            $select_locale->each(function($item, $itemIdx) use (&$select_locale) {
                if( str_contains($item, ',') ) {
                    $select_locale
                        ->push(
                            ...toCollect(explode(',', $item))
                                   ->map(fn($i) => $i)
                                   ->filter()
                        )
                        ->forget($itemIdx);
                }
            });
        }
        $select_locale = $select_locale->filter(fn($e) => $e && trim($e) !== '*')
                                       ->toArray();
        $languages = $select_locale ?: ($config[ 'languages' ] ?: [ 'en' ]);
        $tableView = $this->option('table');

        $response = [
            'languages' => $languages,
            'translations' => [],
        ];

        $expectExtractor = fn($d) => data_get($d, 'expect');
        // $actualExtractor = fn($d) => data_get($d, 'actual');
        $tableData = [];
        foreach( $groups as $group ) {
            if( !empty($select_group) && !in_array(trim($group), $select_group) ) {
                continue;
            }
            $response[ 'translations' ][ $group ] = [];

            foreach( $languages as $lang ) {
                $response[ 'translations' ][ $group ][ $lang ] = [];

                $file_array = (array) localTrans($group, [], $lang);
                $db = (array) trans($group, [], $lang);

                try {
                    $diff = toCollect(
                        array_recursive_diff(array_dot($db), array_dot($file_array), $expectExtractor)
                    // array_recursive_diff($db, $file_array, $expectExtractor)
                    );
                } catch(\Exception $exception) {
                    $diff = collect([]);
                }

                $response[ 'translations' ][ $group ][ $lang ] = array_merge($response[ 'translations' ][ $group ][ $lang ] ?? [], $diff->toArray() ?: []);
                if( empty($response[ 'translations' ][ $group ][ $lang ]) ) {
                    unset($response[ 'translations' ][ $group ][ $lang ]);
                }
            }

            if( empty($response[ 'translations' ][ $group ]) ) {
                unset($response[ 'translations' ][ $group ]);
            }
        }

        foreach( (array) array_get(undot($response), "translations") as $_tdGroup => $_tdData ) {
            foreach( (array) $_tdData as $_tdLang => $__tdData ) {
                foreach( (array) $__tdData as $indexDataI => $indexDataV ) {
                    $tableData[] = [
                        'Locale' => $_tdLang,
                        'Model' => $_tdGroup,
                        'Key' => $indexDataI,
                        'Value' => $indexDataV,
                        'FileValue' => localTrans("{$_tdGroup}.{$indexDataI}", [], $_tdLang),
                    ];
                }
            }
        }

        $prepend_group = $this->option('prepend');
        $tab = fn(int $times = 1) => $this->option('tab') ? str_repeat("\t", $times >= 0 ? $times : 0) : "";
        $quote = !$this->option('quote') ? fn($t) => '"' . "{$t}" . '"' : fn($t) => $t;
        $compact = !$this->option('compact') ? "=" : "";
        $output = "";
        foreach( $response[ 'translations' ] as $g => $translations ) {
            $string = $prepend_group || $compact ? "" : $quote($g) . "";

            foreach( $translations as $l => $v ) {
                $new_data = array_merge(array_only((array) localTrans($g, [], $l), array_keys($v)), $v);

                if( !$prepend_group ) {
                    $string .= $compact
                        ? $quote("{$g}.{$l}") . ":"
                        : "\n" . $tab(1) . $quote($l);
                }

                foreach( array_dot($new_data) as $_k => $_v ) {
                    $string .= "\n";
                    $string .= $prepend_group ? "" : $tab(2);
                    $string .= $quote($prepend_group ? "{$g}.{$l}.{$_k}" : "{$_k}");
                    $string .= $compact ?: "\n" . $tab(3);
                    $string .= !is_array($_v) && !is_object($_v)
                        ? $quote($_v)
                        : print_r($_v, true);
                }

                $string .= "\n";
            }

            $output .= $string . "\n" . ($compact ? "\n" : "");
        }

        if( !$tableView ) {
            $this->output->write(str_ireplace("\n\n\n", "\n\n", $output));
        }

        if( $this->option('dump') ) {
            dump($response);
        }

        /** @var $this \Illuminate\Console\Command */
        $tableView &&
        $this->table([
                         'Locale',
                         'Model',
                         'Key',
                         'Value',
                         'FileValue',
                     ], $tableData);

        return Command::SUCCESS;
    }
}
