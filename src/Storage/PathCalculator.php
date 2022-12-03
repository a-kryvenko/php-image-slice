<?php

namespace Antey\ImageSlice\Storage;

class PathCalculator
{
    public static function getDestinationFolder(string $filePath, string $folder): string
    {
        if (!empty($folder)) {
            if (substr($folder, -1) === '/') {
                $folder = substr($folder, 0, -1);
            }
            $destinationFolder = $folder;
        } else {
            $pathParts = explode('/', $filePath);
            unset($pathParts[count($pathParts) - 1]);
            $destinationFolder = implode('/', $pathParts);
        }

        return $destinationFolder;
    }

    public static function getFileName(string $filePath, string $name): string
    {
        if (!empty($name)) {
            if (substr($name, 0, 1) === '/') {
                $name = substr($name, 1);
            }
            $fileName = $name;
        } else {
            $pathParts = explode('/', $filePath);
            $fileName = $pathParts[count($pathParts) - 1];
        }

        return $fileName;
    }

    public static function getSliceName(string $name, int $number): string
    {
        if (strpos($name, '.') === false) {
            return $name . '-' . ($number + 1);
        }

        $nameParts = explode('.', $name);
        $nameParts[count($nameParts) - 2] .= '-' . ($number + 1);

        return implode('.', $nameParts);
    }
}
