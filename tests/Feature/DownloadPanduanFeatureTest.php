<?php

namespace Tests\Feature;

use Tests\TestCase;

class DownloadPanduanFeatureTest extends TestCase
{
    /**
     * Test that the admission guide and tuition breakdown PDF can be downloaded.
     */
    public function test_user_can_download_panduan_pdf(): void
    {
        $response = $this->get(route('panduan.download'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString(
            'Panduan-dan-Rincian-Biaya-Pendaftaran-SMK-TI-Bali-Global-Badung.pdf',
            (string) $response->headers->get('content-disposition')
        );
    }

    /**
     * Test that the landing page renders the download guide button.
     */
    public function test_landing_page_renders_download_guide_button(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee(route('panduan.download'), false);
    }
}
