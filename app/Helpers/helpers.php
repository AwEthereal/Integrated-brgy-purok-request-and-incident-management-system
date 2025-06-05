<?php

if (!function_exists('format_label')) {
    /**
     * Format a label by replacing underscores with spaces and capitalizing words
     * 
     * @param string $label
     * @return string
     */
    function format_label($label) {
        if (empty($label)) {
            return '';
        }
        return ucwords(str_replace('_', ' ', $label));
    }
}
