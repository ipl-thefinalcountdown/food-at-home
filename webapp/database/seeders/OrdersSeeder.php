<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class OrdersSeeder extends Seeder
{
    private $numberOfDays = 100;
    private $avgOrdersDay = [60, 10, 15, 15, 20, 30, 50]; // Domingo, Segunda, terça, ...
    private $quantidades = [1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2, 2, 2, 2, 2, 2, 2, 3, 3, 3, 4, 4, 5, 6];

    private $velocidadeCooks = [1, 1.2, 2, 1.7, 1.8, 1.5]; // são 6 cooks (4 a 9)
    private $velocidadeDelivery = [1, 1.4, 2.8, 2.3, 1.8, 1.3, 1.9, 2.1, 2.4, 1.2, 2.3, 1.9]; // são 12 deliveryman (10 a 21)
    private $customerIDs = [];
    private $productIDs = [];
    private $productPrices = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (DatabaseSeeder::$seedType == "full") {
            $this->numberOfDays = 5 * 365;   // 5 ANOS
        } else {
            $this->numberOfDays = 60;      // 2 meses
        }

        $this->command->info("Order seeder - Start");

        $this->command->info("Preparing Products");
        $prods = DB::select('select id, price from products');
        $this->productIDs = Arr::pluck($prods, 'id');
        $this->productPrices = Arr::pluck($prods, 'price', 'id');

        $this->command->info("Preparing Customers");
        $this->customerIDs = Arr::pluck(DB::select('select id from customers'), 'id');

        $faker = \Faker\Factory::create('pt_PT');

        $today = Carbon::today();
        $this->start_date = $today->copy();
        $this->start_date->subDays($this->numberOfDays);
        $d = $this->start_date->copy();

        $i = 0;
        while ($d->lessThanOrEqualTo($today)) {
            if ($i % 10 == 0) { /// 50 em 50 dias escreve no log
                $this->command->info("Order for day " . $d->format('d-m-Y'));
            }
            if ($i % 100 == 0) { /// 100 em 100 dias negócio cresce (ou diminui)
                for ($j = 0; $j < count($this->avgOrdersDay); $j++)
                    foreach ($this->avgOrdersDay as $avg) {
                        $fatorCrescimento = rand(-3, 5);
                        $this->avgOrdersDay[$j] += $this->avgOrdersDay[$j] * $fatorCrescimento / 100;
                    }
            }
            $totalOrdersDay = intval($this->avgOrdersDay[$d->dayOfWeek] + $this->avgOrdersDay[$d->dayOfWeek] * rand(-20, 20) / 100);
            $totalOrdersDay = $totalOrdersDay < 0 ? 0 : $totalOrdersDay;
            $ordersDay = [];
            for ($num = 0; $num < $totalOrdersDay; $num++) {
                $ordersDay[] = $this->createOrderArray($faker, $d);
            }
            DB::table('orders')->insert($ordersDay);
            $ids = DB::table('orders')->where('date', $d->format('Y-m-d'))->pluck('id')->toArray();

            foreach ($ids as $id) {
                $allItems = [];
                $total = $this->createOrderItemsArray($allItems, $id);
                DB::table('order_items')->insert($allItems);
                //DB::update('update orders set total_price = ? where id = ?', [$total, $id]);
            }
            $i++;
            $d->addDays(1);
        }
        $this->command->info("Updating Orders Total Price");
        DB::update('update orders set total_price = (select sum(sub_total_price) from order_items where order_items.order_id = orders.id)');

        $this->command->info("All Orders were created");
        $this->command->info("---- END ----");
    }

    private function createOrderArray($faker, $day)
    {
        $cook_id = rand(4, 9);
        $deliveryman_id = rand(10, 21);
        $timeCook = 5 * 60 * (10 / rand(2, 10) * $this->velocidadeCooks[$cook_id - 4]);
        $timeDeliver = 5 * 60 * (10 / rand(2, 10) * $this->velocidadeDelivery[$deliveryman_id - 10]);
        $timeTotal = $timeCook + $timeDeliver + rand(0, 600);
        $inicio = $day->copy()->addSeconds(rand(39600, 78000));
        $fim = $inicio->copy()->addSeconds($timeTotal);


        return [
            'status' => rand(0, 40) == 1 ? 'C' : 'D',
            'customer_id' => Arr::random($this->customerIDs),
            'notes' => rand(0, 20) == 1 ? $faker->realText(100) : null,
            'total_price' => 0,
            'date' =>  $day->format('Y-m-d'),
            'prepared_by' => $cook_id,
            'delivered_by' => $deliveryman_id,
            'opened_at' =>  $inicio,
            'current_status_at' =>  $fim,
            'closed_at' =>  $fim,
            'preparation_time' =>  $timeCook,
            'delivery_time' =>  $timeDeliver,
            'total_time' =>  $timeTotal,
            'created_at' => $inicio,
            'updated_at' => $fim
        ];
    }

    private function createOrderItemsArray(&$allItems, $id_order)
    {
        $totalItems = rand(1, 10);
        $total = 0;
        for ($i = 0; $i < $totalItems; $i++) {
            $prodID = Arr::random($this->productIDs);
            $qty = Arr::random($this->quantidades);
            $subTotal = $qty * $this->productPrices[$prodID];
            $allItems[] = [
                'order_id' => $id_order,
                'product_id' => $prodID,
                'quantity' => $qty,
                'unit_price' => $this->productPrices[$prodID],
                'sub_total_price' => $subTotal
            ];
        }
        return $total;
    }
}
