<?php

$consumerCount = 50;

// BEGIN multiprocess stuff
$pids = [];
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

for ($i = 0; $i < $consumerCount; $i++) {
    $groupInstanceId = gethostname() . '_' . $i;

    $pid = pcntl_fork();

    if ($pid == -1) {
        echo '💩 couldn\'t fork' . PHP_EOL;
        exit();
    }

    if ($pid) {
        $pids[] = $pid;
        pcntl_signal(SIGCHLD, SIG_IGN); // 🧙🪄
        echo '🦾 Created consumer #' . $pid . PHP_EOL;
    } else {
        // child's logic
        $parent = false;
        pcntl_signal(SIGUSR1, 'sigHandler');

        $conf = new RdKafka\Conf();
        $conf->set('metadata.broker.list', 'kafka1:9092,kafka2:9092,kafka3:9092');
        $conf->set('group.id', 'messages-consumer');
        $conf->set('auto.offset.reset', 'earliest');
        $conf->set('session.timeout.ms', 60000);
        $conf->set('group.instance.id', $groupInstanceId);

        $consumer = new RdKafka\KafkaConsumer($conf);

        $consumer->subscribe(['messenger-messages']);

        try {
            while (true) {
                $message = $consumer->consume(10 * 1000);
                switch ($message->err) {
                    case RD_KAFKA_RESP_ERR_NO_ERROR:
                        $data = json_decode($message->payload, true);
                        echo "User {$data['userIdFrom']} sent a message to user {$data['userIdTo']}" . PHP_EOL;
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
        } catch (Throwable $e) {
            echo "💩 Got " . get_class($e) . ' with message ' . $e->getMessage() . PHP_EOL;
        } finally {
            $consumer->close();
        }

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

    usleep(200000);
}
