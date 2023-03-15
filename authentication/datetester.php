#!/usr/bin/php
<?php


$date = 'Mar 25';
$date_str = date_create_from_format('M d', $date);
$date_str->setDate(date('Y'), $date_str->format('m'), $date_str->format('d'));
$formatted_date = date_format($date_str, 'Y-m-d');
echo $formatted_date;
