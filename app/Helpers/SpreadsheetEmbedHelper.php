<?php

namespace App\Helpers;

class SpreadsheetEmbedHelper
{
    /**
     * Konversi URL Google Sheets ke mode embed (preview = lihat saja, edit = admin).
     */
    public static function toEmbedUrl(string $url, bool $editable = false): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        if (preg_match('/\/spreadsheets\/d\/([a-zA-Z0-9-_]+)/', $url, $matches)) {
            $id = $matches[1];
            $mode = $editable ? 'edit' : 'preview';

            return "https://docs.google.com/spreadsheets/d/{$id}/{$mode}?rm=minimal";
        }

        if (preg_match('/spreadsheets\/d\/e\/([a-zA-Z0-9-_]+)/', $url, $matches)) {
            return $editable ? $url : str_replace('/pubhtml', '/pubhtml?widget=true&headers=false', $url);
        }

        return $url;
    }
}
