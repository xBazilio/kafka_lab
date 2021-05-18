#!/bin/bash
docker run -it \
  --network=kafka-lab \
  --volume="$PWD/../kafka_distr:/opt/kafka" \
  kafka_lab_kafka-sandbox \
  /bin/bash