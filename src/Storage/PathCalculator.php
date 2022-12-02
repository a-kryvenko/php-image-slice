<?php

namespace Antey\ImageSlice\Storage;

class PathCalculator
{
    public static function getDestinationFolder(string $filePath, string $folder): string
    {
        if (!empty($folder)) {
            if (str_ends_with($folder, '/')) {
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
            if (str_starts_with($name, '/')) {
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
        if (!str_contains($name, '.')) {
            return $name . '-' . ($number + 1);
        }

        $nameParts = explode('.', $name);
        $nameParts[count($nameParts) - 2] .= '-' . ($number + 1);

        return implode('.', $nameParts);
    }
}
