<?php
/*
 * Copyright (c) 2012-2021 Satoshi Fukutomi <info@fuktommy.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHORS AND CONTRIBUTORS ``AS IS'' AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED.  IN NO EVENT SHALL THE AUTHORS OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS
 * OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
 * OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF
 * SUCH DAMAGE.
 */
namespace Fuktommy\Kakuyomu\Model;

use Fuktommy\Kakuyomu\Entity\Episode;
use Fuktommy\Kakuyomu\Entity\Work;
use Fuktommy\Http\CachingClient;
use Fuktommy\WebIo\Resource;


class WorkApi
{
    /**
     * @var Fuktommy\WebIo\Resource
     */
    private $resource;

    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }

    public function get($id): Work
    {
        $url = "{$this->resource->config['kakuyomu_top']}/works/{$id}";
        $cacheTime = $this->resource->config['cache_time'];
        $feedSize = $this->resource->config['feed_size'];

        $client = new CachingClient($this->resource);
        $html = $client->fetch($url, $cacheTime);

        $doc = new \DOMDocument();
        $oldValue = libxml_use_internal_errors(true);
        $doc->loadHTML(trim($html));
        libxml_use_internal_errors($oldValue);
        $xpath = new \DOMXpath($doc);

        $title = $xpath->query('//title')->item(0)->textContent;
        $author = $xpath->query('//*[@id="workAuthor-activityName"]')->item(0)->textContent;

        $episodes = [];
        foreach ($xpath->query('//a[contains(@class,"widget-toc-episode-episodeTitle")]') as $e) {
            $episodes[] = new Episode(
                $e->getAttribute('href'),
                $xpath->query('.//*[contains(@class,"widget-toc-episode-titleLabel")]', $e)->item(0)->textContent,
                $xpath->query('.//*[@datetime]', $e)->item(0)->getAttribute('datetime')
            );
        }

        usort($episodes, function ($a, $b) {
            return strcmp($b->date, $a->date);
        });

        return new Work($title, $author, array_slice($episodes, 0, $feedSize));
    }
}
