<?php

/*
 * This file is part of the FOSHttpCache package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\HttpCache\Tests\Unit;

use FOS\HttpCache\ResponseTagger;

class ResponseTaggerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetTagsHeaderValue()
    {
        $proxyClient = \Mockery::mock('FOS\HttpCache\ProxyClient\Invalidation\TagsInterface')
            ->shouldReceive('getTagsHeaderValue')
            ->with(['post-1', 'test,post'])
            ->once()
            ->andReturn('post-1,test_post')
            ->getMock();

        $tagger = new ResponseTagger($proxyClient);
        $this->assertFalse($tagger->hasTags());
        $tagger->addTags(['post-1', 'test,post']);
        $this->assertTrue($tagger->hasTags());
        $this->assertEquals('post-1,test_post', $tagger->getTagsHeaderValue());
    }

    public function testTagResponse()
    {
        $proxyClient = \Mockery::mock('FOS\HttpCache\ProxyClient\Invalidation\TagsInterface')
            ->shouldReceive('getTagsHeaderValue')
            ->with(['tag-1', 'tag-2'])
            ->once()
            ->andReturn(['tag-1', 'tag-2'])
            ->shouldReceive('getTagsHeaderName')
            ->once()
            ->andReturn('FOS-Tags')
            ->getMock();

        $tagger = new ResponseTagger($proxyClient);

        $response = \Mockery::mock('Psr\Http\Message\ResponseInterface')
            ->shouldReceive('withHeader')
            ->with('FOS-Tags', ['tag-1', 'tag-2'])
            ->getMock();

        $tagger->addTags(['tag-1', 'tag-2']);
        $tagger->tagResponse($response, true);
    }
}
