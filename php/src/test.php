<?php

if (extension_loaded('rdkafka')) {
    echo 'Found rdkafka extension! 😸';
} else {
    echo 'No rdkafka found... 😿';
}

echo PHP_EOL;
