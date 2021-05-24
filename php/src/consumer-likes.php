<?php

$conf = new RdKafka\Conf();
$conf->set('metadata.broker.list', 'kafka1:9092,kafka2:9092,kafka3:9092');
$conf->set('group.id', 'likes-consumer');
$conf->set('auto.offset.reset', 'earliest');
$conf->set('session.timeout.ms', 60000);
$conf->set('group.instance.id', gethostname());

$consumer = new RdKafka\KafkaConsumer($conf);

$consumer->subscribe(['likes']);

try {
    while (true) {
        $message = $consumer->consume(10 * 1000);
        switch ($message->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                $data = json_decode($message->payload, true);
                echo "Got " . ($data['like'] ? 'like 👍' : 'dislike 👎') . " for Post #{$data['postId']}" . PHP_EOL;
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                echo "No more messages; will wait for more ⏳" . PHP_EOL;
                break;
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                echo "⌛ Timed out" . PHP_EOL;
                break;
            default:
                echo "💩 Something went wrong" . PHP_EOL;
                throw new \Exception($message->errstr(), $message->err);
        }
    }
} finally {
    $consumer->close();
}

