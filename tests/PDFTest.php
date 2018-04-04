<?php

namespace VerumConsilium\Browsershot\Tests;

use VerumConsilium\Browsershot\PDF;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\TestResponse;

class PDFTest extends TestCase
{
    /** @test */
    public function it_generates_a_pdf_from_a_valid_url_and_stores_it_to_disk()
    {
        $pdf = new PDF('https://google.com');

        Storage::fake('pdfs');

        $sotoredPath = $pdf->store('pdfs');

        Storage::assertExists($sotoredPath);
    }

    /** @test */
    public function it_generates_a_pdf_from_html_and_returns_it_intline()
    {
        $pdf = new PDF;
        $response = $pdf->loadHtml('<h1>Testing</h1>')
                        ->inline();

        $response = new TestResponse($response);

        $response->assertSuccessful();
        $response->assertHeader('Content-Disposition', 'inline');
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function it_generates_a_pdf_from_a_view_and_returns_a_download_response()
    {
        $pdf = new PDF;

        $response = $pdf->loadView('test')
                        ->download();

        $response = new TestResponse($response);

        $response->assertSuccessful();
        $response->assertHeader('Content-Disposition', 'attachment');
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function it_lets_you_modify_the_underlying_browsershot_class()
    {
        $pdf = new PDF;

        $response = $pdf->margins(20, 0, 0, 20)
                        ->dismissDialogs()
                        ->paperSize(200, 330)
                        ->inline();

        $response = new TestResponse($response);

        $response->assertSuccessful();
    }
}
