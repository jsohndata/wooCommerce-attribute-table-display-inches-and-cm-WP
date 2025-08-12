![Screenshot of WooCommerce Dual-Unit Dimensions](images/screen.webp)

# WordPress - WooCommerce Dual-Unit Dimensions (in ↔ cm)

This WooCommerce snippet displays product dimensions in both inches and centimeters, automatically converting based on your store unit. Skips empty values to avoid `0 ×` clutter, ensuring clean, professional output. Works with WPCode Lite, Code Snippets, or a child theme `functions.php`.

> Built with assistance from **GPT-5 Thinking**.

---

## Features
- Detects your store’s dimension unit (**in** or **cm**) and converts to the other.
- Skips blank/zero values for **length**, **width**, **height**.
- Clean output with trimmed decimals (e.g., `22` instead of `22.00`).
- Safe to add/remove via WPCode Lite or Code Snippets plugin.

---

## Requirements
- WordPress with **WooCommerce**.
- One of:
  - **WPCode Lite** (recommended), or
  - **Code Snippets** plugin, or
  - Child theme access to `functions.php`.

---

## Installation (WPCode Lite – recommended)
1. **Plugins → Add New** → search **WPCode** → install **WPCode Lite** → **Activate**.
2. **Code Snippets → Add Snippet → Add Your Custom Code (New Snippet)**.
3. Select **Code Type: PHP Snippet**.
4. Paste the code from the **Code** section below.
5. Set **Insertion** to **Run Everywhere**.
6. **Save Snippet** → **Activate**.

### Alternative: Theme `functions.php`
- Use a **child theme** (best practice).
- Paste the code at the bottom of `functions.php`.
- Save and test a product page.

---

## Usage
- Ensure your products have **Dimensions** set (Length, Width, Height) under **Product data → Shipping**.
- The snippet formats in your **store unit** + converted unit.
- Any dimension left **blank or zero** is **omitted** from the display.

---

## Code

```php
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
