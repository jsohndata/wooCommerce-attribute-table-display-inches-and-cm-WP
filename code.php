<?php
/**
 * WooCommerce Dual-Unit Dimensions (in ↔ cm), skipping empty values.
 * Example: 22 × 28 in (55.88 × 71.12 cm)
 * Detects store unit (in/cm) and converts to the other.
 * Built with assistance from GPT-5 Thinking.
 */

add_filter('woocommerce_format_dimensions', function($dimension_string, $dimensions) {
    // Detect store unit (default to cm if not set)
    $unit = get_option('woocommerce_dimension_unit', 'cm');
    $inch_to_cm = 2.54;

    // Build arrays for primary (store unit) & secondary (converted) values
    $primary_parts = [];
    $secondary_parts = [];

    // Preserve dimension order: length × width × height
    foreach (['length', 'width', 'height'] as $dim) {
        if (!empty($dimensions[$dim]) && floatval($dimensions[$dim]) > 0) {
            $value = floatval($dimensions[$dim]);

            // Format primary number (trim trailing .00)
            $primary_formatted = rtrim(rtrim(number_format($value, 2), '0'), '.');

            if ($unit === 'in') {
                // Convert inches → cm
                $converted = $value * $inch_to_cm;
                $converted_formatted = round($converted, 2);
                $primary_parts[]   = $primary_formatted;
                $secondary_parts[] = $converted_formatted;
            } else {
                // Convert cm → inches
                $converted = $value / $inch_to_cm;
                $converted_formatted = round($converted, 2);
                $primary_parts[]   = $primary_formatted;
                $secondary_parts[] = $converted_formatted;
            }
        }
    }

    // Only build output if at least one dimension exists
    if (!empty($primary_parts)) {
        $primary_str      = implode(' × ', $primary_parts) . ' ' . $unit;
        $secondary_unit   = ($unit === 'in') ? 'cm' : 'in';
        $secondary_str    = '(' . implode(' × ', $secondary_parts) . ' ' . $secondary_unit . ')';
        return $primary_str . ' ' . $secondary_str;
    }

    // No dimensions: return empty string (hides the line)
    return '';
}, 10, 2);
