<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Product;
use App\Models\ZalandoCategories;
use App\Models\ZalandoProducts;
use Illuminate\Console\Command;

class PullProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates the DB with products';

    protected $max = 200;

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
        $params = [/*'ageGroup' => 'adult', */'category' => 'mens-shoes'];
        $pulled = 0;
        $cats = [];
        $cnt = 0;
        $zCatModel = new ZalandoCategories();
        $inc = 100;

        try {
            // Pull products and put in the DB
            do {
                $cnt++;
                $pulled += $inc;
                $zProd = new ZalandoProducts();
                $prods = $zProd->fetchArticles(array_merge($params, ['pageSize' => $inc, 'page' => $cnt]));

                foreach ($prods['content'] as $prod) {
                    $catIds = [];
                    $product = new Product([
                        'zalando_id' => $prod['id'], 'color' => $prod['color'], 'name' => $prod['name'], 'season' => $prod['season'],
                        'data' => json_encode($prod),
                    ]);

                    $c = array_filter($prod['categoryKeys'], function ($val) {return stripos($val, 'mens-shoes') !== false;});
                    // Save this cat
                    foreach ($c as $item) {
                        // Check if it exists, else fetch and save it
                        $category = Category::where('key', $item)->first();
                        if (empty($category)) {
                            $zc = $zCatModel->fetchCategory($item);
                            $category = new Category([
                                'name' => $zc->name, 'key'=>$zc->key, 'filters'=>implode(',', $zc->suggestedFilters),
                                'parent_id' => Category::getParentCat($zc->parentKey),
                            ]);
                            $category->saveOrFail();
                        }
                        $catIds[] = $category->id;

                    }
                    if (empty($catIds)) {
                        continue;
                    }
                    $product->save();
                    $product->categories()->sync($catIds, false);
                    $pulled++;
                }

            } while($pulled < $this->max);

        } catch (\Exception $ex) {
            $this->error("Exception will pulling data: ".get_class($ex)."-> ".$ex->getMessage());
        }
    }
}
