<?php
add_filter('woocommerce_format_dimensions', function($dimension_string, $dimensions) {
    $unit       = get_option('woocommerce_dimension_unit', 'cm');
    $inch_to_cm = 2.54;

    $primary_parts   = [];
    $secondary_parts = [];

    foreach (['length', 'width', 'height'] as $dim) {
        $v = isset($dimensions[$dim]) ? (float) $dimensions[$dim] : 0.0;
        if ($v > 0) {
            if ($unit === 'in') {
                $converted        = $v * $inch_to_cm;
                $primary_parts[]  = rtrim(rtrim(number_format($v, 2), '0'), '.');
                $secondary_parts[] = round($converted, 2);
            } else {
                $converted        = $v / $inch_to_cm;
                $primary_parts[]  = rtrim(rtrim(number_format($v, 2), '0'), '.');
                $secondary_parts[] = round($converted, 2);
            }
        }
    }

    if ($primary_parts) {
        $primary_str   = implode(' × ', $primary_parts) . ' ' . $unit;
        $secondary_unit = ($unit === 'in') ? 'cm' : 'in';
        $secondary_str  = '(' . implode(' × ', $secondary_parts) . ' ' . $secondary_unit . ')';
        return $primary_str . ' ' . $secondary_str;
    }

    return '';
}, 10, 2);
