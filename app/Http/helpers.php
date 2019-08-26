<?php

const SECTION_TYPE_CATEGORY = 0;
const SECTION_TYPE_BOOK = 1;

const SECTION_LAYOUT_LIST_VERTICLE = 0;
const SECTION_LAYOUT_LIST_HORIZONTAL = 1;
const SECTION_LAYOUT_SINGLE_FEATURE = 2;
const SECTION_LAYOUT_GRID_HORIZOLTAL_TOP_CHART = 3;
const SECTION_LAYOUT_LIST_HORIZONTAL_READING = 4;

const BOOK_FIELD_INFO = 'info';
const BOOK_FIELD_SUMMARY = 'summary';
const BOOK_FIELD_METADATAS = 'metadatas';
const BOOK_FIELD_AUTHORS = 'authors';

const PAGINATE_LIMIT_RECORD = 15;

const FORMAT_INPUT = 'Y-m-d H:i:s';
const FORMAT_OUTPUT = 'Y-m-d_H:i:sP';

const LIBRARY_STATUS_UNREAD = 0;
const LIBRARY_STATUS_READING = 1;
const LIBRARY_STATUS_COMPLETE = 2;



if (!function_exists('get_list_section_type')) {
    /**
     * get static url
     * @list: book|category
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
     * @list: listVertical|listHorizontal|gridHorizontalTopChart|singleFeatured|listHorizontalReading
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

if (!function_exists('get_list_section_items')) {
    /**
     * get static url
     * @list: listVertical|listHorizontal|gridHorizontalTopChart|singleFeatured|listHorizontalReading
     * @return array
     */
    function get_list_section_items($type = null)
    {
        $types = [
            SECTION_LAYOUT_LIST_VERTICLE => 4,
            SECTION_LAYOUT_LIST_HORIZONTAL => 5,
            SECTION_LAYOUT_SINGLE_FEATURE => 1,
            SECTION_LAYOUT_GRID_HORIZOLTAL_TOP_CHART => 11,
            SECTION_LAYOUT_LIST_HORIZONTAL_READING => 4,
        ];

        return !empty($types[$type]) ? $types[$type] : $types;
    }
}


function isJSON($string){
    return is_string($string) && is_array(json_decode($string, true)) ? true : false;
}

function get_valid_book_fields($field  = null) {
    $list = [
        BOOK_FIELD_INFO => 'info',
        BOOK_FIELD_SUMMARY => 'summary',
        BOOK_FIELD_METADATAS => 'metadatas',
        BOOK_FIELD_AUTHORS => 'authors',
    ];

    return $field ? !empty($list[$field]) ? $list[$field] : null : $list;
}

function convert_to_output_date($strDate)
{
    return str_replace('_', 'T', $strDate);
}

function get_library_status($status = null)
{
    $list = [
        LIBRARY_STATUS_UNREAD =>        'unread',
        LIBRARY_STATUS_READING =>       'reading',
        LIBRARY_STATUS_COMPLETE =>      'completed',
    ];

    return isset($list[$status]) ? $list[$status] : $list;
}
