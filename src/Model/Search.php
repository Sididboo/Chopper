<?php
namespace App\Model;

use App\Entity\Category;


class Search {
    /**
     * @var string
     */
    public $string = '';

    /**
     * @var Category[]
     */
    public $categories = [];
}