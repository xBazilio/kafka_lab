This is the Docker environment to try kafka.

### About

The project is intended to help in trying kafka. It's not so easy to install all the software, all the extensions and find the right approach to write code for kafka.

The project is centered around PHP. It has docker-compose file with all the containers defined. Two of them are PHP containers to imitate producer and consumer environment.

I did my best to include helpfull examples for most common use cases

### Installation

1. Download and unpack [kafka](https://kafka.apache.org/downloads) into `kafka_distr` folder
1. Copy and overwrite *.properties files from `kafka_custom_config` to `kafka_distr/config`
1. Copy and overwrite `kafka-server-start.sh` fo `kafka_distr/bin`
1. Run `docker-compose up`

### Usage

Now you should have the cluster running.

To run PHP contaners go into `php` directory and run `php_cli.sh` or `php_front.sh`. (They are ultimately the same, they just imitate 2 different machines)

PHP containers have working dir linked to project's `php/src` dir, so you can run various examples from here.

#### Creating topics

It's not possible to create topics from PHP. By running `php create-topics.php` you can see commands you need to run to create topics.

Login to kafka server with `docker container exec -it kafka_lab_kafka1_1 /bin/bash` and run those two commands.

#### Simple produce/consume

The pair `producer-likes.php` and `consumer-likes.php` shows an example of simple produce/consume pattern.

The topic `likes` has only 10 partitions. It should be enough when there's not so mutch messages. 

You can run producer in endless bash `wile` loop. Then open 2-3 php containers and run consumer in each. The setting

```php
$conf->set('group.instance.id', gethostname());
```

helps to prevent unnecessary rebalancing when restarting consumer.

#### Highload produce/consume

Imagine having messenger and thousands of users who send a lot of messages. Messages are stored in clusters. Say we have 50 clusters and decided that 100 threads for each should be plenty. That's why we have 500 partitions in the `messenger-messages` topic.

Producer `producer-messages.php` uses multithreading to imitate highload. Use CTRL+C to exit. Consumer `consumer-messages.php` runs 50 children threads each of which has unique `group.instance.id`. So restarting consumer won't trigger rebalancing.