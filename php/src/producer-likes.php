<?php

$conf = new RdKafka\Conf();
$conf->set('metadata.broker.list', 'kafka1:9092,kafka2:9092,kafka3:9092');

class KafkaProducer
{
    private \RdKafka\Conf $conf;

    public function __construct(RdKafka\Conf $conf)
    {
        $this->conf = $conf;
    }

    private function flush(\RdKafka\Producer $producer): bool
    {
        $result = RD_KAFKA_RESP_ERR_UNKNOWN;
        for ($flushRetries = 0; $flushRetries < 10; $flushRetries++) {
            $result = $producer->flush(10000);
            if (RD_KAFKA_RESP_ERR_NO_ERROR === $result) {
                break;
            }
        }
        return RD_KAFKA_RESP_ERR_NO_ERROR === $result;
    }

    public function procuce($topicName, $message, $key): bool
    {
        $producer = new RdKafka\Producer($this->conf);

        $topic = $producer->newTopic($topicName);

        $topic->produce(RD_KAFKA_PARTITION_UA, 0, $message, $key);
        $producer->poll(0);

        return $this->flush($producer);
    }
}

$producer = new KafkaProducer($conf);

$postId = rand(35, 147);
$like = rand(0, 5) > 1;

$message = json_encode(['postId' => $postId, 'like' => $like]);

$result = $producer->procuce('likes', $message, $postId);

if ($result) {
    echo "Post #{$postId} was " . ($like ? 'liked 👍' : 'disliked 👎') . PHP_EOL;
} else {
    echo "💩 couldn't produce message for Post #{$postId}" . PHP_EOL;
}
