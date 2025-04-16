<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
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
        try {
            // Generate PDF
            $pdf = Pdf::loadView($view, $data);

            // Set paper size and orientation
            $pdf->setPaper('a4', 'portrait');

            // Generate a unique filename
            $uniqueFilename = $filename . '_' . time() . '.pdf';
            $filePath = 'reports/' . $uniqueFilename;

            // Ensure the directory exists
            if (!Storage::disk('local')->exists('reports')) {
                Storage::disk('local')->makeDirectory('reports');
            }

            // Save PDF content to disk
            Storage::disk('local')->put($filePath, $pdf->output());
            return $filePath;
        } catch (\Exception $e) {
            Log::error('PDF generation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete a PDF file
     *
     * @param string $path
     * @return bool
     */
    public static function deletePdf(string $path): bool
    {
        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->delete($path);
        }

        return false;
    }
}
