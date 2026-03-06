<?php
namespace App\Helpers;

class FileIconHelper
{
    public static function getIcon($extension)
    {
        $icons = [
            'pdf' => 'fa-file-pdf',
            'doc' => 'fa-file-word',
            'docx' => 'fa-file-word',
            'xls' => 'fa-file-excel',
            'xlsx' => 'fa-file-excel',
            'jpg' => 'fa-file-image',
            'jpeg' => 'fa-file-image',
            'png' => 'fa-file-image',
            'txt' => 'fa-file-alt',
            'csv' => 'fa-file-csv',
            'json' => 'fa-file-code',
            'md' => 'fa-file-alt',
            'default' => 'fa-file',
        ];

        return $icons[$extension] ?? $icons['default'];
    }
}
?>
