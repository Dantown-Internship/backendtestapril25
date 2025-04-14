<?php

function generatePaginationMeta($collection)
{
    return [
        'current_page' => $collection->currentPage(),
        'from' => $collection->firstItem(),
        'last_page' => $collection->lastPage(),
        'path' => $collection->getOptions()['path'],
        'per_page' => $collection->perPage(),
        'to' => $collection->lastItem(),
        'total' => $collection->total(),
    ];
}
function generatePaginationLinks($collection)
{
    return [
        'first' => $collection->getOptions()['path'].'?'.$collection->getOptions()['pageName'].'=1',
        'last' => $collection->getOptions()['path'].'?'.$collection->getOptions()['pageName'].'='.$collection->lastPage(),
        'prev' => $collection->previousPageUrl(),
        'next' => $collection->nextPageUrl(),
    ];
}
