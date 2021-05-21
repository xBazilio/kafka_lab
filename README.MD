This is the Docker environment to try kafka.

### About

The project is intended to help in trying kafka. It's not so easy to install all the software, all the extensions and find the right approach to write code for kafka.

The project is centered around PHP. It has docker-compose file with all the containers defined. Two of them are PHP containers to imitate producer and consumer environment.

I did my best to include helpfull examples to most common use cases

### Installation

1. Download and unpack [kafka](https://kafka.apache.org/downloads) into `kafka_distr` folder
1. Copy and overwrite *.properties files from `kafka_custom_config` to `kafka_distr/config`
1. Run `docker-compose up`

### Usage

Now you should have the cluster running.

To run PHP contaners go into `php` directory and run `php_cli.sh` or `php_front.sh`. (They are ultimately the same, they just imitate 2 different machines)

PHP containers have working dir linked to project's `php/src` dir, so you can run various examples from here.
