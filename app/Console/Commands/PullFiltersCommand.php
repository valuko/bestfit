<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Filter;
use App\Models\ZalandoCategories;
use App\Models\ZalandoFilters;
use Illuminate\Console\Command;

class PullFiltersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull:filters';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull Filters - to get the filters and categories from Zalando api';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $filtersList = ['sports'];

        // Fetch categories from Zalando here... limit to just categories under men
        try {
            $categories = new ZalandoCategories();
            $results = $categories->fetchCategories([
                'targetGroup' => 'men', 'name' => 'shoes'
            ]);

            foreach ($results->content as $result) {
                // insert and fetch filters
                $cat = new Category([
                   'name' => $result->name, 'key'=>$result->key, 'filters'=>implode(',', $result->suggestedFilters),
                ]);
                $cat->saveOrFail();
                $filtersList = array_unique(array_merge($filtersList, $result->suggestedFilters));
            }

            $filter = new ZalandoFilters();
            // Fetch and save all filters
            foreach ($filtersList as $item) {
                $f = $filter->fetchFilter($item);
                $filterModel = new Filter([
                    'name' => $f->name, 'display_name' => ucfirst(snake_case($f->name)),
                    'type' => $f->type, 'values' => json_encode($f->values),
                ]);
                $filterModel->saveOrFail();
            }

        } catch (\Exception $ex) {
            $this->error("Exception will loading all data: ".get_class($ex)."-> ".$ex->getMessage());
        }



    }
}
