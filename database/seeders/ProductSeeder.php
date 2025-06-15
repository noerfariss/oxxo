<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // APPAREL
        $filePath = base_path('database/seeders/data/APPAREL.xlsx');
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        DB::beginTransaction();
        try {
            $appareal = Category::where('name', 'APPAREL')->first()->id;
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // skip header

                $name = $row[0] ?? null;
                $priceDC = $row[1];
                $pricePO = $row[2];

                if ($name && trim($name) !== '') {
                    $product = Product::create([
                        'name' => $name,
                        'category_id' => $appareal,
                    ]);

                    DB::table('product_price')->insert([
                        'product_id' => $product->id,
                        'product_attribute_id' => 1,
                        'price' => $priceDC,
                        'created_at' => Carbon::now()
                    ]);

                    DB::table('product_price')->insert([
                        'product_id' => $product->id,
                        'product_attribute_id' => 2,
                        'price' => $pricePO,
                        'created_at' => Carbon::now()
                    ]);
                }
            }

            DB::commit();
            echo "APPAREL Success";
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }

        // KIDS APPAREL
        $filePath = base_path('database/seeders/data/APPAREL_KIDS.xlsx');
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // skip header

                $name = $row[0] ?? null;
                $priceDC = $row[1];

                if ($name && trim($name) !== '') {
                    $product = Product::create([
                        'name' => $name,
                        'category_id' => Category::where('name', 'KIDS APPAREL')->first()->id,
                    ]);

                    DB::table('product_price')->insert([
                        'product_id' => $product->id,
                        'product_attribute_id' => 1,
                        'price' => $priceDC,
                        'created_at' => Carbon::now()
                    ]);
                }
            }

            DB::commit();
            echo "KIDS APPAREL Success";
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }

        // LINEN
        $filePath = base_path('database/seeders/data/LINEN.xlsx');
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // skip header

                $name = $row[0] ?? null;
                $priceDC = $row[1];

                if ($name && trim($name) !== '') {
                    $product = Product::create([
                        'name' => $name,
                        'category_id' => Category::where('name', 'LINEN')->first()->id,
                    ]);

                    DB::table('product_price')->insert([
                        'product_id' => $product->id,
                        'product_attribute_id' => 1,
                        'price' => $priceDC,
                        'created_at' => Carbon::now()
                    ]);
                }
            }

            DB::commit();
            echo "LINEN Success";
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }

        // OTHER
        $filePath = base_path('database/seeders/data/OTHERS.xlsx');
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                if ($index === 0) continue; // skip header

                $name = $row[0] ?? null;
                $priceDC = $row[1];

                if ($name && trim($name) !== '') {
                    $product = Product::create([
                        'name' => $name,
                        'category_id' => Category::where('name', 'OTHERS')->first()->id,
                    ]);

                    DB::table('product_price')->insert([
                        'product_id' => $product->id,
                        'product_attribute_id' => 1,
                        'price' => $priceDC,
                        'created_at' => Carbon::now()
                    ]);
                }
            }

            DB::commit();
            echo "OTHERS Success";
        } catch (Exception $e) {
            DB::rollBack();
            echo $e->getMessage();
        }
    }
}
