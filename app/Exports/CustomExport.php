<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CustomExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        // Return data as a collection
        return $this->data;
    }

    public function headings(): array
    {
//dd(array_keys((array) $this->data->first()), $this->data);
      	$firstItem = $this->data->first();
	$headers = $firstItem ? 
    	(method_exists($firstItem, 'getOriginal') 
          ? array_keys((array) $firstItem->getOriginal()) 
          : array_keys((array) $firstItem)) 
    	: [];
      $header_data = $this->data->first() ? array_map(function($column) {
    	$camelCaseColumn = ucwords($column);
    	return str_replace('_', ' ', $camelCaseColumn);
      }, $headers) : [];
      return $header_data;
    }
}

