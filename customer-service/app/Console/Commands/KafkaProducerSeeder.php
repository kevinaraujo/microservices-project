<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Console\Command;
use PHPEasykafka\KafkaProducer;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;

class KafkaProducerSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:produce-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $topicConf = $this->container->get('KafkaTopicConfig');
        $brokerCollection = $this->container->get('KafkaBrokerCollection');

        $customer_id = Uuid::uuid4();
        Customer::create([
            'id' => $customer_id,
            'name' => 'Joao - ' . $customer_id,
            'email' => 'joao' . $customer_id . '@gmail.com',
            'phone' => '(11)96775-9685'
        ]);

        $product_id = Uuid::uuid4();
        $order_id = Uuid::uuid4();

        $order = [
            'order' => [
                'id' => $order_id,
                'customer_id' => $customer_id,
                'status' => 'Reservado',
                'discount' => 5,
                'total' => 95,
                'order_date' => '2019-01-01',
                'return_date' => '2019-01-25',
            ],
            'items' => [
                [
                    'id' => Uuid::uuid4(),
                    'order_id' => $order_id,
                    'product' => [
                        'id' => $product_id,
                        'name' => 'Produto nome'
                    ],
                    'qty' => 1,
                    'total' => 100
                ]
            ]
        ];

        $producer =  new KafkaProducer(
            $brokerCollection,
            'orders',
            $topicConf
        );
        $producer->produce(json_encode($order));
    }
}
