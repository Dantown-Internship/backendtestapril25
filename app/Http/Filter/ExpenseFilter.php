<?php
namespace App\Http\Filter;

class ExpenseFilter extends QueryFilter
{
    public function category($category)
    {
        $this->builder->where('category', 'like', $category . '%');

    }
    public function title($name)
    {
        $this->builder->where('title', 'like', $name . '%');
    }

}
