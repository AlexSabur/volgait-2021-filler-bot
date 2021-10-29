#!/usr/bin/env php
<?php

// print_r($argv);

$output_including_status = exec("./../ php filler play", $output, $result_code);

print_r($result_code);
