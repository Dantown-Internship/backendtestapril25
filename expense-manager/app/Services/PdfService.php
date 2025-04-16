<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    /**
     * Generate a PDF from a view
     *
     * @param string $view
     * @param array $data
     * @param string $filename
     * @return string Path to the generated PDF
     */
    public static function generatePdf(string $view, array $data, string $filename): string
    {
        // Generate PDF
        $pdf = Pdf::loadView($view, $data);

        // Set paper size and orientation
        $pdf->setPaper('a4', 'portrait');

        // Generate a unique filename
        $uniqueFilename = $filename . '_' . time() . '.pdf';
        $filePath = 'reports/' . $uniqueFilename;

        // Save PDF content to disk
        Storage::disk('public')->put($filePath, $pdf->output());
        return $filePath;
    }

    /**
     * Delete a PDF file
     *
     * @param string $path
     * @return bool
     */
    public static function deletePdf(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }
}
