<?php

const SECTION_TYPE_CATEGORY = 0;
const SECTION_TYPE_BOOK = 1;

const SECTION_LAYOUT_LIST_VERTICLE = 0;
const SECTION_LAYOUT_LIST_HORIZONTAL = 1;
const SECTION_LAYOUT_SINGLE_FEATURE = 2;
const SECTION_LAYOUT_GRID_HORIZOLTAL_TOP_CHART = 3;
const SECTION_LAYOUT_LIST_HORIZONTAL_READING = 4;


if (!function_exists('get_list_section_type')) {
    /**
     * get static url
     *
     * @return array
     */
    function get_list_section_type($type = null)
    {
        $types = [
            SECTION_TYPE_CATEGORY => 'category',
            SECTION_TYPE_BOOK => 'book'
        ];

        return !empty($types[$type]) ? $types[$type] : $types;
    }
}

if (!function_exists('get_list_section_layout')) {
    /**
     * get static url
     *
     * @return array
     */
    function get_list_section_layout($type = null)
    {
        $types = [
            SECTION_LAYOUT_LIST_VERTICLE => 'listVertical',
            SECTION_LAYOUT_LIST_HORIZONTAL => 'listHorizontal',
            SECTION_LAYOUT_SINGLE_FEATURE => 'singleFeatured',
            SECTION_LAYOUT_GRID_HORIZOLTAL_TOP_CHART => 'gridHorizontalTopChart',
            SECTION_LAYOUT_LIST_HORIZONTAL_READING => 'listHorizontalReading',
        ];

        return !empty($types[$type]) ? $types[$type] : $types;
    }
}


function isJSON($string){
    return is_string($string) && is_array(json_decode($string, true)) ? true : false;
}
