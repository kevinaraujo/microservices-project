<?php

namespace App\Console\Commands;

use App\Kafka\OrderHandler;
use Illuminate\Console\Command;
use Psr\Container\ContainerInterface;

class KafkaConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:consume {topic} {group}';

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
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(ContainerInterface $container)
    {
        $topic = $this->argument('topic');
        $group = $this->argument('group');

        $configs = [
            'consumer' => [
                'enable.auto.commit' => 'false',
                'auto.commit.interval.ms' => '100',
                'offset.store.method' => 'broker',
                'auto.offset.reset' => 'largest',
            ]
        ];

        $brokerCollection = $container->get('KafkaBrokerCollection');

        $consumer = new \PHPEasykafka\KafkaConsumer(
            $brokerCollection,
            [$topic],
            $group,
            $configs,
            $container
        );
        $consumer->consume(120 * 10000, [OrderHandler::class]);

        $this->info('Consuming topic form kafka');
    }
}
