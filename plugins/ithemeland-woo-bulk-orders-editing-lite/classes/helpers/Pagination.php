<?php

namespace wobef\classes\helpers;

class Pagination
{
    public static function init($current_page, $max_num_pages)
    {
        $current_page = esc_html(intval($current_page));
        $max_num_pages = esc_html(intval($max_num_pages));
        $prev = max(1, $current_page - 1);
        $next = min($max_num_pages, $current_page + 1);
        $max_display = 3;
        $output = "<div class='wobef-top-nav-filters-paginate'>";
        if (isset($max_num_pages)) {
            $output .= "<a href='javascript:;' data-index='" . esc_attr($prev) . "' class='wobef-filter-form-action' data-search-action='pagination'><</a>";
            if ($current_page < $max_display) {
                for ($i = 1; $i <= min($max_display, $max_num_pages); $i++) {
                    $current = ($i == $current_page) ? 'current' : '';
                    $output .= "<a href='javascript:;' data-index='" . esc_attr($i) . "' class='wobef-filter-form-action " . esc_attr($current) . "' data-search-action='pagination'>" . esc_html($i) . "</a>";
                }
                if ($max_num_pages > $max_display) {
                    $output .= "<span>...</span>";
                    $output .= "<a href='javascript:;' data-index='" . esc_attr($max_num_pages) . "' class='wobef-filter-form-action' data-search-action='pagination'>" . esc_html($max_num_pages) . "</a>";
                }
            } elseif ($current_page == $max_display) {
                $max_num = ($max_display < $max_num_pages) ? $max_display + 1 : $max_display;
                for ($i = 1; $i <= $max_num; $i++) {
                    $current = ($i == $current_page) ? 'current' : '';
                    $output .= "<a href='javascript:;' data-index='" . esc_attr($i) . "' class='wobef-filter-form-action " . esc_attr($current) . "' data-search-action='pagination'>" . esc_html($i) . "</a>";
                }
                if ($max_num_pages > $current_page) {
                    $output .= "<span>...</span>";
                    $output .= "<a href='javascript:;' data-index='" . esc_attr($max_num_pages) . "' class='wobef-filter-form-action' data-search-action='pagination'>" . esc_html($max_num_pages) . "</a>";
                }
            } else {
                $output .= "<a href='javascript:;' data-index='1' class='wobef-filter-form-action' data-search-action='pagination'>1</a>";
                $output .= "<span>...</span>";
                for ($i = $current_page - 2; $i <= min($current_page + 2, $max_num_pages); $i++) {
                    $current = ($i == $current_page) ? 'current' : '';
                    $output .= "<a href='javascript:;' data-index='" . esc_attr($i) . "' class='wobef-filter-form-action " . esc_attr($current) . "' data-search-action='pagination'>" . esc_html($i) . "</a>";
                }
                if ($current_page + 2 < $max_num_pages) {
                    $output .= "<span>...</span>";
                    $output .= "<a href='javascript:;' data-index='" . esc_attr($max_num_pages) . "' class='wobef-filter-form-action' data-search-action='pagination'>" . esc_html($max_num_pages) . "</a>";
                }
            }
            $output .= "<a href='javascript:;' data-index='" . esc_attr($next) . "' class='wobef-filter-form-action' data-search-action='pagination'>></a>";
        }
        $output .= "</div>";
        $output .= "<div class='wobef-pagination-loading'>Loading</div>";
        return $output;
    }
}
