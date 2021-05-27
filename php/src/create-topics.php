<?php

$topicsConfig = [
    'messenger-messages' => [
        'partitions' => 500,
        'replication-factor' => 3
    ],

    'likes' => [
        'partitions' => 10,
        'replication-factor' => 3
    ]
];

echo PHP_EOL;
echo 'Sadly, you can only create/delete topics with kafka bin/kafka-topics.sh util. 
You need to connect to kafka-server to do it. 
So here are commands to run:' . PHP_EOL . PHP_EOL;

foreach ($topicsConfig as $name => $params) {
    echo "\t";
    // a bit of paranoia here
    $name = escapeshellarg($name);
    foreach ($params as $k => $v) {
        $params[$k] = escapeshellarg($v);
    }
    echo "export JMX_PORT=9094 && ./bin/kafka-topics.sh --create --if-not-exists --topic {$name} "
        . "--replication-factor {$params['replication-factor']} --partitions {$params['partitions']} "
        . "--bootstrap-server localhost:9092";

    echo PHP_EOL . PHP_EOL;
}
