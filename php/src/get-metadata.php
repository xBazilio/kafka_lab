<?php

$conf = new RdKafka\Conf();
$conf->set('metadata.broker.list', 'kafka1:9092,kafka2:9092,kafka3:9092');
$conf->set('group.id', 'metadata-reader');

$kafkaConsumer = new RdKafka\KafkaConsumer($conf);

$metadata = $kafkaConsumer->getMetadata(true, null, 500);

echo PHP_EOL . 'Data was provided by ' . $metadata->getOrigBrokerName() . PHP_EOL . PHP_EOL;

$brokers = $metadata->getBrokers();
echo 'Found ' . count($brokers) . ' brokers:' . PHP_EOL;
foreach ($brokers as $broker) {
    echo "\t";
    echo '#' . $broker->getId() . ' ' . $broker->getHost() . ':' . $broker->getPort() . PHP_EOL;
}

echo PHP_EOL;

$topics = $metadata->getTopics();
echo 'Found ' . count($topics) . ' topics:' . PHP_EOL;
foreach ($topics as $topic) {
    echo "\t";
    echo $topic->getTopic() . ' with ' . count($topic->getPartitions()) . ' partitions' . PHP_EOL;
}

echo PHP_EOL;
