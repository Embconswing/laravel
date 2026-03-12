<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class ZplPrinter
{
    protected string $printerName;

    public function __construct()
    {
        $this->printerName = config('printing.zpl_printer', 'Zebra_Label');
    }

    public function printLabel(array $data): bool
    {
        $zpl = $this->buildZpl($data);

        $fileName = 'label_' . uniqid() . '.zpl';
       $filePath = "C:\\Windows\\Temp\\{$fileName}";


        file_put_contents($filePath, $zpl);

        $command = "copy /B " . escapeshellarg($filePath) . " \"\\\\localhost\\{$this->printerName}\"";

        Log::info('Printing label...', [
            'printer' => $this->printerName,
            'file' => $filePath,
            'command' => $command,
            'zpl' => $zpl,
        ]);

        exec($command, $output, $resultCode);

        Log::info('Print result', [
            'output' => $output,
            'resultCode' => $resultCode,
        ]);

        //@unlink($filePath);

        return $resultCode === 0;
    }

    protected function buildZpl(array $data): string
    {
        $name = $data['name'] ?? 'N/A';
        $passport = $data['passport'] ?? 'N/A';
        $applicationNo = $data['application_no'] ?? 'N/A';

        return "^XA
^FO50,30^A0N,30,30^FDName: {$name}^FS
^FO50,70^A0N,30,30^FDPassport: {$passport}^FS
^FO50,110^A0N,30,30^FDApp No: {$applicationNo}^FS
^FO50,160^BY2
^BCN,100,Y,N,N
^FD{$applicationNo}^FS
^XZ";
    }
}
