<?php

namespace CoinbaseCommerce\Tests\Resources;

use CoinbaseCommerce\ApiResourceList;
use CoinbaseCommerce\Resources\Invoice;
use CoinbaseCommerce\Tests\BaseTest;

class InvoiceTest extends BaseTest
{
    public function setUp(): void
    {
        parent::setUp();
        Invoice::setClient($this->apiClient);
    }

    public function testInsertMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('invoice.json'));
        $data = [
            'business_name' => 'Crypto Account LLC',
            'customer_email' => 'customer@test.com',
            'customer_name' => 'Test Customer',
        ];
        $invoiceObj = new Invoice($data);
        $invoiceObj->insert();

        $this->assertRequested('POST', '/invoices', '');
        $this->assertEquals('BV9KGDVA', $invoiceObj->code);
    }

    public function testSaveMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('invoice.json'));
        $invoiceObj = new Invoice(
            [
                'business_name' => 'Crypto Account LLC',
                'customer_email' => 'customer@test.com',
                'customer_name' => 'Test Customer',
            ]
        );
        $invoiceObj->save();

        $this->assertRequested('POST', '/invoices', '');
        $this->assertInstanceOf(Invoice::getClassName(), $invoiceObj);
        $this->assertEquals('BV9KGDVA', $invoiceObj->code);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Update is not allowed
     */
    public function testSaveMethodWithId()
    {
        $this->appendRequest(200, $this->parseJsonFile('invoice.json'));
        $invoiceObj = new Invoice(
            [
                'id' => 'd0bb39fc-3690-4c88-bb41-74ca60b77df0',
                'business_name' => 'Crypto Account LLC',
                'customer_email' => 'customer@test.com',
                'customer_name' => 'Test Customer',
            ]
        );
        $invoiceObj->save();
    }

    public function testCreateMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('invoice.json'));
        $invoiceObj = Invoice::create(
            [
                'business_name' => 'Crypto Account LLC',
                'customer_email' => 'customer@test.com',
                'customer_name' => 'Test Customer',
            ]
        );

        $this->assertRequested('POST', '/invoices', '');
        $this->assertInstanceOf(Invoice::getClassName(), $invoiceObj);
        $this->assertEquals('BV9KGDVA', $invoiceObj->code);
    }

    public function testRefreshMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('invoice.json'));
        $id = 'd0bb39fc-3690-4c88-bb41-74ca60b77df0';
        $invoiceObj = new Invoice();
        $invoiceObj->id = $id;
        $invoiceObj->refresh();

        $this->assertRequested('GET', '/invoices/' . $id, '');
        $this->assertInstanceOf(Invoice::getClassName(), $invoiceObj);
        $this->assertEquals('BV9KGDVA', $invoiceObj->code);
    }

    public function testRetrieveMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('invoice.json'));
        $id = '488fcbd5-eb82-42dc-8a2b-10fdf70e0bfe';
        $invoiceObj = Invoice::retrieve($id);

        $this->assertRequested('GET', '/invoices/' . $id, '');
        $this->assertInstanceOf(Invoice::getClassName(), $invoiceObj);
        $this->assertEquals('BV9KGDVA', $invoiceObj->code);
    }

    public function testListMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('invoiceList.json'));
        $invoiceList = Invoice::getList(['limit' => 2]);

        $this->assertRequested('GET', '/invoices', 'limit=2');
        $this->assertInstanceOf(ApiResourceList::getClassName(), $invoiceList);
    }

    public function testAllMethod()
    {
        $firstPageInvoiceList = $this->parseJsonFile('firstPageInvoiceList.json');
        $startingAfter = $firstPageInvoiceList['pagination']['cursor_range'][1];
        $this->appendRequest(200, $firstPageInvoiceList);
        $this->appendRequest(200, $this->parseJsonFile('secondPageInvoiceList.json'));
        $list = Invoice::getAll(['order' => 'desc']);

        $this->assertRequested('GET', '/invoices', 'order=desc');
        $this->assertRequested('GET', '/invoices', 'order=desc&starting_after=' . $startingAfter);
        $this->assertCount(3, $list);
        $this->assertInstanceOf(Invoice::getClassName(), $list[0]);
    }

    public function testResolveMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('invoice.json'));
        $id = 'b76f81ec-3e8d-4342-aacb-582fab083660';
        $invoiceObj = Invoice::retrieve($id);
        $this->assertRequested('GET', '/invoices/' . $id, '');
        $id = $invoiceObj->id;
        $this->appendRequest(200, $this->parseJsonFile('invoice.json'));
        $invoiceObj->resolve();

        $this->assertRequested('POST', "/invoices/{$id}/resolve", '');
        $this->assertEquals($id, $invoiceObj->id);
    }

    public function testVoidMethod()
    {
        $this->appendRequest(200, $this->parseJsonFile('invoice.json'));
        $id = 'b76f81ec-3e8d-4342-aacb-582fab083660';
        $invoiceObj = Invoice::retrieve($id);
        $this->assertRequested('GET', '/invoices/' . $id, '');
        $id = $invoiceObj->id;
        $this->appendRequest(200, $this->parseJsonFile('invoice.json'));
        $invoiceObj->void();

        $this->assertRequested('POST', "/invoices/{$id}/void", '');
        $this->assertEquals($id, $invoiceObj->id);
    }
}
