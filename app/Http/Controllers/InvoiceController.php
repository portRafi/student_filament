<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Request;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Invoice;

class InvoiceController extends Controller
{
    public function generatePdf(Student $student)
    {
        $customer = new buyer([
            'name' => $student->name,
            'custom_fields' => [
                'email' => $student->email,
            ],
        ]);

        $item = InvoiceItem::make('Service 1')->pricePerUnit(2);

        $invoice = Invoice::make()
            ->buyer($customer)
            ->discountByPercent(10)
            ->taxRate(15)
            ->shipping(1.99)
            ->addItem($item);

        return $invoice->stream();
    }
}
