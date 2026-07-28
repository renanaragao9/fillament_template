<?php

namespace App\Helpers;

use App\Exports\CollectionExport;
use Maatwebsite\Excel\Facades\Excel;

class CreateAndStoreXLSXFileHelper
{
    private string $primaryColor = '00695C';

    public function run(
        string $title,
        array $headers,
        array $attributes,
        array $dataReport,
        string $filename,
        object $user,
        bool $includeHeader = true,
    ): bool|string {
        $reportData = [];

        if (! empty($attributes)) {
            foreach ($dataReport as $row) {
                $rowData = [];
                foreach ($attributes as $key => $attribute) {
                    $rowData[] = data_get($row, $attribute);
                }
                $reportData[] = $rowData;
            }
        } else {
            $reportData = array_values($dataReport);
        }

        $path = 'mla-exports/'.$filename.'.xlsx';

        Excel::store(new CollectionExport(
            title: $title,
            data: $reportData,
            headers: $headers,
            user: $user,
            color: $this->primaryColor,
            includeHeader: $includeHeader,
        ), $path, 's3');

        return $path;
    }
}
