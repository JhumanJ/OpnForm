<?php

namespace App\Console\Commands\Tax;

use App\Exports\Tax\ArrayExport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Laravel\Cashier\Cashier;
use Stripe\Invoice;

class GenerateTaxExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:generate-stripe-export
                            {--start-date= : Start date (YYYY-MM-DD)}
                            {--end-date= : End date (YYYY-MM-DD)}
                            {--full-month : Use the full month of the start date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compute Stripe VAT per country';

    public const EU_TAX_RATES = [
        'AT' => 20,
        'BE' => 21,
        'BG' => 20,
        'HR' => 25,
        'CY' => 19,
        'CZ' => 21,
        'DK' => 25,
        'EE' => 20,
        'FI' => 24,
        'FR' => 20,
        'DE' => 19,
        'GR' => 24,
        'HU' => 27,
        'IE' => 23,
        'IT' => 22,
        'LV' => 21,
        'LT' => 21,
        'LU' => 17,
        'MT' => 18,
        'NL' => 21,
        'PL' => 23,
        'PT' => 23,
        'RO' => 19,
        'SK' => 20,
        'SI' => 22,
        'ES' => 21,
        'SE' => 25,
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // iterate through all Stripe invoices
        $startDate = $this->option('start-date');
        $endDate = $this->option('end-date');

        // Validate the date format
        if ($startDate && ! Carbon::createFromFormat('Y-m-d', $startDate)) {
            $this->error('Invalid start date format. Use YYYY-MM-DD.');

            return Command::FAILURE;
        }

        if ($endDate && ! Carbon::createFromFormat('Y-m-d', $endDate)) {
            $this->error('Invalid end date format. Use YYYY-MM-DD.');

            return Command::FAILURE;
        } elseif (! $endDate && $this->option('full-month')) {
            $endDate = Carbon::parse($startDate)->endOfMonth()->endOfDay()->format('Y-m-d');
        }

        $this->info('Start date: '.$startDate);
        $this->info('End date: '.$endDate);

        $processedInvoices = [];

        // Create a progress bar
        $queryOptions = [
            'limit' => 100,
            'expand' => ['data.customer', 'data.customer.address', 'data.customer.tax_ids', 'data.payment_intent',
                'data.payment_intent.payment_method', 'data.charge.balance_transaction'],
            'status' => 'paid',
        ];
        if ($startDate) {
            $queryOptions['created']['gte'] = Carbon::parse($startDate)->startOfDay()->timestamp;
        }
        if ($endDate) {
            $queryOptions['created']['lte'] = Carbon::parse($endDate)->endOfDay()->timestamp;
        }

        $invoices = Cashier::stripe()->invoices->all($queryOptions);
        $bar = $this->output->createProgressBar();
        $bar->start();
        $paymentNotSuccessfulCount = 0;
        $totalInvoice = 0;

        do {
            foreach ($invoices as $invoice) {
                // Ignore if payment was refunded
                if (($invoice->payment_intent->status ?? null) !== 'succeeded') {
                    $paymentNotSuccessfulCount++;

                    continue;
                }

                $processedInvoices[] = $this->formatInvoice($invoice);
                $totalInvoice++;

                // Advance the progress bar
                $bar->advance();
            }

            $queryOptions['starting_after'] = end($invoices->data)->id;

            sleep(5);
            $invoices = $invoices->all($queryOptions);
        } while ($invoices->has_more);

        $bar->finish();
        $this->line('');

        $aggregatedReport = $this->aggregateReport($processedInvoices);

        $filePath = 'opnform-tax-export-per-invoice_'.$startDate.'_'.$endDate.'.xlsx';
        $this->exportAsXlsx($processedInvoices, $filePath);

        $aggregatedReportFilePath = 'opnform-tax-export-aggregated_'.$startDate.'_'.$endDate.'.xlsx';
        $this->exportAsXlsx($aggregatedReport, $aggregatedReportFilePath);

        // Display the results
        $this->info('Total invoices: '.$totalInvoice.' (with '.$paymentNotSuccessfulCount.' payment not successful or trial free invoice)');

        return Command::SUCCESS;
    }

    private function aggregateReport($invoices): array
    {
        // Sum invoices per country
        $aggregatedReport = [];
        foreach ($invoices as $invoice) {
            $country = $invoice['cust_country'];
            $customerType = is_null($invoice['cust_vat_id']) && $this->isEuropeanCountry($country) ? 'individual' : 'business';
            if (! isset($aggregatedReport[$country])) {
                $defaultVal = [
                    'count' => 0,
                    'total_usd' => 0,
                    'tax_total_usd' => 0,
                    'total_after_tax_usd' => 0,
                    'total_eur' => 0,
                    'tax_total_eur' => 0,
                    'total_after_tax_eur' => 0,
                ];
                $aggregatedReport[$country] = [
                    'individual' => $defaultVal,
                    'business' => $defaultVal,
                ];
            }
            $aggregatedReport[$country][$customerType]['count']++;
            $aggregatedReport[$country][$customerType]['total_usd'] = ($aggregatedReport[$country][$customerType]['total_usd'] ?? 0) + $invoice['total_usd'];
            $aggregatedReport[$country][$customerType]['tax_total_usd'] = ($aggregatedReport[$country][$customerType]['tax_total_usd'] ?? 0) + $invoice['tax_total_usd'];
            $aggregatedReport[$country][$customerType]['total_after_tax_usd'] = ($aggregatedReport[$country][$customerType]['total_after_tax_usd'] ?? 0) + $invoice['total_after_tax_usd'];
            $aggregatedReport[$country][$customerType]['total_eur'] = ($aggregatedReport[$country][$customerType]['total_eur'] ?? 0) + $invoice['total_eur'];
            $aggregatedReport[$country][$customerType]['tax_total_eur'] = ($aggregatedReport[$country][$customerType]['tax_total_eur'] ?? 0) + $invoice['tax_total_eur'];
            $aggregatedReport[$country][$customerType]['total_after_tax_eur'] = ($aggregatedReport[$country][$customerType]['total_after_tax_eur'] ?? 0) + $invoice['total_after_tax_eur'];
        }

        $finalReport = [];
        foreach ($aggregatedReport as $country => $data) {
            foreach ($data as $customerType => $aggData) {
                $finalReport[] = [
                    'country' => $country,
                    'customer_type' => $customerType,
                    ...$aggData,
                ];
            }
        }

        return $finalReport;
    }

    private function formatInvoice(Invoice $invoice): array
    {
        $country = $invoice->customer->address->country ?? $invoice->payment_intent->payment_method->card->country ?? null;

        $vatId = $invoice->customer->tax_ids->data[0]->value ?? null;
        $taxRate = $this->computeTaxRate($country, $vatId);

        $taxAmountCollectedUsd = $taxRate > 0 ? $invoice->total * $taxRate / ($taxRate + 100) : 0;
        $totalEur = $invoice->charge->balance_transaction->amount;
        $taxAmountCollectedEur = $taxRate > 0 ? $totalEur * $taxRate / ($taxRate + 100) : 0;

        return [
            'invoice_id' => $invoice->id,
            'created_at' => Carbon::createFromTimestamp($invoice->created)->format('Y-m-d H:i:s'),
            'cust_id' => $invoice->customer->id,
            'cust_vat_id' => $vatId,
            'cust_country' => $country,
            'tax_rate' => $taxRate,
            'total_usd' => $invoice->total / 100,
            'tax_total_usd' => $taxAmountCollectedUsd / 100,
            'total_after_tax_usd' => ($invoice->total - $taxAmountCollectedUsd) / 100,
            'total_eur' => $totalEur / 100,
            'tax_total_eur' => $taxAmountCollectedEur / 100,
            'total_after_tax_eur' => ($totalEur - $taxAmountCollectedEur) / 100,
        ];
    }

    private function computeTaxRate($countryCode, $vatId)
    {
        // Since we're a French company, for France, always apply 20% VAT
        if ($countryCode == 'FR' ||
            is_null($countryCode) ||
            empty($countryCode)) {
            return self::EU_TAX_RATES['FR'];
        }

        if ($taxRate = (self::EU_TAX_RATES[$countryCode] ?? null)) {
            // If VAT ID is provided, then TAX is 0%
            if (! $vatId) {
                return $taxRate;
            }
        }

        return 0;
    }

    private function isEuropeanCountry($countryCode)
    {
        return isset(self::EU_TAX_RATES[$countryCode]);
    }

    private function exportAsXlsx($data, $filename)
    {
        if (count($data) == 0) {
            $this->info('Empty data. No file generated.');

            return;
        }

        (new ArrayExport($data))->store($filename, 'local', \Maatwebsite\Excel\Excel::XLSX);
        $this->line('File generated: '.storage_path('app/'.$filename));
    }
}
