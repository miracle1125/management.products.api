<?php

namespace App\Services;

use PrintNode\Printer;
use PrintNode\PrintJob;
use PrintNode\Request;
use PrintNode\Response;

class PrintService
{
    private $request;

    /**
     * @return PrintService
     */
    public static function print(): PrintService
    {
        return app(PrintService::class);
    }

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Printer[]|null
     */
    public function getPrinters(): ?array
    {
        return $this->request->getPrinters();
    }

    /**
     * Sends a new print job request to the service from a PDF source.
     *
     * @param int|string $printerId The id of the printer you wish to print to.
     * @param string $title A title to give the print job.
     *  This is the name which will appear in the operating system's print queue.
     * @param string $content The path to the pdf file, a pdf string
     *
     * @return Response
     */
    public function newPdfPrintJob($printerId, string $title, string $content): Response
    {
        if (@is_file($content)) { // if file exists and is a file and not a directory
            $content = file_get_contents($content);
        }

        return $this->newPrintJob($printerId, $title, base64_encode($content));
    }

    public function printPdfFromUrl($printerId, $title, $url): Response
    {
        return $this->newPrintJob($printerId, $title, $url, 'pdf_uri');
    }

    /**
     * Sends a new print job request to the service.
     *
     * @param int|string $printerId The id of the printer you wish to print to.
     * @param string $title A title to give the print job.
     *  This is the name which will appear in the operating system's print queue.
     * @param string $content If contentType is pdf_uri or raw_uri,
     *  this should be the URI from which the document you wish to print can be downloaded.
     *  If contentType is pdf_base64 or raw_base64, this should be the
     *  base64-encoding of the document you wish to print.
     * @param string $contentType
     * @return Response
     */
    public function newPrintJob($printerId, string $title, string $content, $contentType = 'pdf_base64'): Response
    {
        $printJob = new PrintJob();
        $printJob->printer = $printerId;
        $printJob->contentType = $contentType;
        $printJob->content = $content;
        $printJob->source = env('APP_NAME');
        $printJob->title = $title;
        $printJob->options = [
            'paper' => 'User defined',
            'fit_to_page' => false
        ];

        return $this->request->post($printJob);
    }
}
