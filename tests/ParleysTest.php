<?php
class ParleysTest extends TestCaseBase
{
    public function testOne()
    {
        $this->assertEmbed(
            'https://www.parleys.com/play/514892290364bc17fc56c4fa/chapter0/about',
            [
                'type' => 'video',
                'providerName' => 'parleys',
            ]
        );
    }
}
