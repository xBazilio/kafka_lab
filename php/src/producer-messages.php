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

function sendMessage(RdKafka\Conf $conf)
{
    $userIdFrom = rand(17234543, 21543887);
    do {
        $userIdTo = rand(17234543, 21543887);
    } while ($userIdFrom == $userIdTo);

    $message = 'The quick brown fox 🦊 jumps over the lazy dog 🐶';

    $payload = json_encode([
        'userIdFrom' => $userIdFrom,
        'userIdTo' => $userIdTo,
        'message' => $message,
    ]);

    $producer = new KafkaProducer($conf);
    $producer->procuce('messenger-messages', $payload, $userIdTo);
    echo "\t📨 Sent message from {$userIdFrom} to {$userIdTo}" . PHP_EOL;
}

// BEGIN multiprocess stuff
$pids = [];
$childProcesses = 42;
$parent = true;
function sigHandler($sigNo)
{
    global $parent;
    global $pids;

    $name = $parent ? 'parent' : 'child';

    switch ($sigNo) {
        case SIGINT:
            echo $name . ' got SIGINT ☠️' . PHP_EOL;
            break;
        case SIGTERM:
            echo $name . ' got SIGTERM ☠' . PHP_EOL;
            break;
        case SIGUSR1:
            echo $name . ' got SIGUSR1 ☠' . PHP_EOL;
            break;
    }

    if ($parent) {
        foreach ($pids as $pid) {
            posix_kill($pid, SIGUSR1);
            pcntl_waitpid($pid, $status);
        }
    }

    exit();
}

pcntl_async_signals(true);
pcntl_signal(SIGINT, 'sigHandler');
pcntl_signal(SIGTERM, 'sigHandler');

// END multiprocess stuff

while (1) {
    while (count($pids) < $childProcesses) {
        $pid = pcntl_fork();

        if ($pid == -1) {
            echo '💩 couldn\'t fork' . PHP_EOL;
            exit();
        }

        if ($pid) {
            $pids[] = $pid;
            pcntl_signal(SIGCHLD, SIG_IGN); // 🧙🪄
            echo '🦾 Created producer #' . $pid . PHP_EOL;
        } else {
            // child's logic
            $parent = false;
            pcntl_signal(SIGUSR1, 'sigHandler');
            sendMessage($conf);
            exit();
        }
    }

    while (count($pids) > 0) {
        foreach ($pids as $k => $pid) {
            $res = pcntl_waitpid($pid, $status, WNOHANG);
            if ($res !== 0) {
                unset($pids[$k]);
            }
        }
    }
}

