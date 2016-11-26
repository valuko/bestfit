<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Filter;
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

    protected $max = 700;

    protected $filters = [];

    protected $sizeList = [];

    protected $priceList = [];

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
        $params = ['ageGroup' => 'adult', 'category' => 'mens-shoes'];
        $pulled = 0;
        $cnt = 0;
        $zCatModel = new ZalandoCategories();
        $inc = 100;

        try {
            $filters = Filter::where('active', 1);
            foreach ($filters as $f) {
                $this->filters[$f->id] = ['name' => $f->name, 'type' => $f->type, 'values' => json_decode($f->values)];
            }

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

                    $this->parseSizePrice($prod);

                    #$product->save();
                    #$product->categories()->sync($catIds, false);
                    $pulled++;
                }

            } while($pulled < $this->max);

            // Save the price and sizes list
            $pRange = $this->genPriceRange();
            $this->savePriceSizes($pRange, $this->sizeList);

        } catch (\Exception $ex) {
            $this->error("Exception will pulling data: ".get_class($ex)."-> ".$ex->getMessage());
        }
    }

    protected function parseSizePrice($product)
    {
        foreach ($product['units'] as $unit) {
            if (!in_array($unit['size'], $this->sizeList)) {
                $this->sizeList[] = $unit['size'];
            }
            $price = (float)$unit['price']['value'];
            if (!in_array($price, $this->priceList)) {
                $this->priceList[] = (float)$price;
            }
        }

    }

    protected function genPriceRange()
    {
        $len = count($this->priceList);
        $k = ceil(log($len, 2) + 1);
        sort($this->priceList);
        $rlen = count($this->priceList)/$k;
        $chunks = array_chunk($this->priceList, $rlen);
        $pList = [];
        foreach ($chunks as $chunk) {
            $min = $chunk[0];
            $max = $chunk[count($chunk)-1];
            $pList[] = ['min' => $min, 'max' => $max, 'name' => "{$min}-{$max}"];
        }
        return $pList;
    }

    protected function savePriceSizes($priceList, $sizeList)
    {
        $filters = Filter::where('name', 'price')->orwhere('name', 'size')->get();
        if (empty($filters)) {
            return;
        }
        foreach ($filters as $filter) {
            if ($filter->name == 'size') {
                $values = [];
                foreach ($sizeList as $item) {
                    $values[] = ['key' => $item, 'displayName' => $item];
                }
                $oldVals = json_decode($filter->values);
                $filter->values = json_encode(array_merge($oldVals, $values));
                //$filter->save();
            } elseif ($filter->name == 'price') {
                $values = [];
                foreach ($priceList as $item) {
                    $values[] = ['key' => $item['name'], 'displayName' => $item['name']];
                }
                $filter->values = json_encode($values);
                $filter->save();
            }
        }
    }
}
